<?php
// app/Http/Controllers/AIController.php

namespace App\Http\Controllers;

use App\Services\OllamaAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Case_Model;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(OllamaAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AI Dashboard View
     */
    public function index()
    {
        $user = Auth::user();
        $cases = $user->cases()->latest()->limit(50)->get();
        
        return view('ai.index', [
            'cases' => $cases,
            'user' => $user
        ]);
    }

    /**
     * Main AI chat endpoint
     */
    public function chat(Request $request, $caseId = null)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'context' => 'nullable|array'
        ]);

        $userId = Auth::id();
        
        // Check if Ollama is running
        if (!$this->aiService->checkStatus()) {
            return response()->json([
                'success' => false,
                'error' => 'Ollama AI server is not running.',
                'solution' => 'Please start Ollama server: ollama serve',
                'type' => 'server_error'
            ], 503);
        }

        try {
            $message = $request->message;
            $context = $request->context ?? [];

            if ($caseId) {
                $case = Case_Model::where('id', $caseId)
                    ->where('user_id', $userId)
                    ->firstOrFail();

                // Prepare context from case
                $caseContext = $this->prepareCaseContext($case);
                
                // Combine with additional context
                $fullContext = array_merge($caseContext, $context);
                
                $response = $this->aiService->askWithContext(
                    $message, 
                    $fullContext, 
                    'You are a legal AI assistant in Pakistan. Provide detailed, accurate legal advice based on the case information.'
                );
            } else {
                // General legal question with optional context
                $prompt = $this->buildPrompt($message, $context);
                $response = $this->aiService->callOllama($prompt);
            }

            // Log the interaction
            $this->logInteraction($userId, $caseId, $message, $response);

            return response()->json([
                'success' => true,
                'response' => $response,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'case_id' => $caseId
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'AI processing failed: ' . $e->getMessage(),
                'type' => 'processing_error'
            ], 500);
        }
    }

    /**
     * Get AI server status
     */
    public function status()
    {
        $isRunning = $this->aiService->checkStatus();
        $models = $this->aiService->getAvailableModels();

        return response()->json([
            'success' => $isRunning,
            'running' => $isRunning,
            'models' => $models,
            'message' => $isRunning 
                ? 'Ollama AI server is running and ready' 
                : 'Ollama server is not running.',
            'endpoint' => config('services.ollama.endpoint', 'http://localhost:11434')
        ]);
    }

    /**
     * Quick actions for case
     */
    public function quickAction(Request $request, $caseId)
    {
        $request->validate([
            'action' => 'required|in:analyze,predict,checklist,summary,timeline,issues,strategies'
        ]);

        $userId = Auth::id();
        $case = Case_Model::where('id', $caseId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $action = $request->action;

        switch ($action) {
            case 'analyze':
                $response = $this->aiService->analyzeCase($case);
                $title = "Case Analysis";
                break;
                
            case 'predict':
                $response = $this->aiService->predictOutcome($case);
                $title = "Outcome Prediction";
                break;
                
            case 'checklist':
                $response = $this->aiService->getHearingChecklist($case);
                $title = "Hearing Checklist";
                break;
                
            case 'summary':
                $response = $this->aiService->generateSummary($case);
                $title = "Case Summary";
                break;
                
            case 'timeline':
                $response = $this->aiService->createTimeline($case);
                $title = "Case Timeline";
                break;
                
            case 'issues':
                $response = $this->aiService->identifyLegalIssues($case);
                $title = "Legal Issues";
                break;
                
            case 'strategies':
                $response = $this->aiService->suggestStrategies($case);
                $title = "Legal Strategies";
                break;
                
            default:
                $response = "Invalid action";
                $title = "Error";
        }

        return response()->json([
            'success' => true,
            'title' => $title,
            'response' => $response,
            'action' => $action,
            'case_id' => $caseId
        ]);
    }

    /**
     * Generate legal document
     */
    public function generateDocument(Request $request, $caseId)
    {
        $request->validate([
            'document_type' => 'required|in:application,reply,affidavit,notice,agreement,power_of_attorney,complaint,custom',
            'custom_prompt' => 'nullable|string|max:1000'
        ]);

        $userId = Auth::id();
        $case = Case_Model::where('id', $caseId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $documentType = $request->document_type;
        $customPrompt = $request->custom_prompt;

        try {
            $response = $this->aiService->generateDocument($case, $documentType, $customPrompt);

            // Save document to database if needed
            $this->saveGeneratedDocument($caseId, $documentType, $response);

            return response()->json([
                'success' => true,
                'document_type' => $documentType,
                'content' => $response,
                'filename' => $this->generateFilename($case, $documentType),
                'downloadable' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Document generation failed: ' . $e->getMessage(),
                'document_type' => $documentType
            ], 500);
        }
    }

    /**
     * Prepare case context for AI
     */
    private function prepareCaseContext($case)
    {
        return [
            'case_number' => $case->case_number,
            'case_title' => $case->case_title,
            'case_type' => $case->case_type,
            'case_status' => $case->case_status,
            'court_name' => $case->court_name,
            'filing_date' => $case->filing_date,
            'first_party_name' => $case->first_party_name,
            'second_party_name' => $case->second_party_name,
            'case_description' => $case->case_description,
            'progress_percentage' => $case->progress_percentage,
            'next_date' => $case->next_date,
            'last_date' => $case->last_date,
            'judge_name' => $case->judge_name,
            'user_notes' => $case->user_notes
        ];
    }

    /**
     * Build prompt with context
     */
    private function buildPrompt($message, $context = [])
    {
        $prompt = "You are a legal AI assistant in Pakistan.\n\n";
        
        if (!empty($context)) {
            $prompt .= "Context:\n";
            foreach ($context as $key => $value) {
                $prompt .= "- {$key}: {$value}\n";
            }
            $prompt .= "\n";
        }
        
        $prompt .= "Question: {$message}\n\n";
        $prompt .= "Please provide comprehensive legal advice with relevant laws, practical steps, and considerations.";
        
        return $prompt;
    }

    /**
     * Log AI interaction
     */
    private function logInteraction($userId, $caseId, $query, $response)
    {
        // You can implement logging to database here
        \Log::info('AI Interaction', [
            'user_id' => $userId,
            'case_id' => $caseId,
            'query' => $query,
            'response_length' => strlen($response),
            'timestamp' => now()
        ]);
    }

    /**
     * Save generated document
     */
    private function saveGeneratedDocument($caseId, $type, $content)
    {
        // Implement document saving logic here
        // This could save to database or file system
    }

    /**
     * Generate filename for document
     */
    private function generateFilename($case, $documentType)
    {
        $date = now()->format('Y-m-d');
        $caseNumber = str_replace(['/', '\\', ' '], '_', $case->case_number);
        
        return "{$caseNumber}_{$documentType}_{$date}.txt";
    }
}