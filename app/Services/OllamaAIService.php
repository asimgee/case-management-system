<?php
// app/Services/OllamaAIService.php

namespace App\Services;

use App\Models\Case_Model;
use App\Models\Document;
use App\Models\Hearing;
use App\Models\Client;
use App\Models\Lawyer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OllamaAIService
{
    private $baseUrl;
    private $model;

    public function __construct()
    {
        // Default Ollama URL (localhost:11434)
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama2');
    }

    /**
     * Get comprehensive case data for AI
     */
    public function getFullCaseData($caseId, $userId)
    {
        $case = Case_Model::with([
            'caseType',
            'caseRemedy',
            'courtType',
            'assignedLawyer',
            'additionalLawyer',
            'clients',
            'documents' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'hearings' => function($query) {
                $query->orderBy('hearing_date', 'desc')->with('attendees', 'expenses');
            }
        ])->where('id', $caseId)
          ->where('user_id', $userId)
          ->first();

        if (!$case) {
            return null;
        }

        // Build comprehensive context
        $context = "=== LEGAL CASE MANAGEMENT SYSTEM - CASE ANALYSIS ===\n\n";
        
        // 1. Basic Case Information
        $context .= "1. CASE OVERVIEW:\n";
        $context .= "   Case Number: {$case->case_number}\n";
        $context .= "   Case Title: {$case->case_title}\n";
        $context .= "   Status: " . ucfirst(str_replace('_', ' ', $case->case_status)) . "\n";
        $context .= "   Filing Date: " . $case->filing_date->format('d-m-Y') . "\n";
        $context .= "   Next Date: " . ($case->next_date ? $case->next_date->format('d-m-Y') : 'Not Set') . "\n";
        $context .= "   Court: {$case->court_name}\n";
        $context .= "   Case Type: " . ($case->caseType->name ?? 'Not Specified') . "\n";
        $context .= "   Legal Remedy: " . ($case->caseRemedy->name ?? 'Not Specified') . "\n";
        $context .= "   Court Type: " . ($case->courtType->name ?? 'Not Specified') . "\n\n";

        // 2. Parties Information
        $context .= "2. PARTIES INVOLVED:\n";
        
        // First Party
        $context .= "   FIRST PARTY (Plaintiff/Complainant):\n";
        $context .= "     Name: {$case->first_party_name}\n";
        if ($case->first_party_contact) {
            $context .= "     Contact: {$case->first_party_contact}\n";
        }
        if ($case->first_party_cnic) {
            $context .= "     CNIC: {$case->first_party_cnic}\n";
        }
        if ($case->first_party_address) {
            $context .= "     Address: {$case->first_party_address}\n";
        }
        $context .= "     Council: " . ($case->first_party_council ?? 'Not Specified') . "\n\n";
        
        // Second Party
        $context .= "   SECOND PARTY (Defendant/Respondent):\n";
        $context .= "     Name: {$case->second_party_name}\n";
        if ($case->second_party_contact) {
            $context .= "     Contact: {$case->second_party_contact}\n";
        }
        if ($case->second_party_cnic) {
            $context .= "     CNIC: {$case->second_party_cnic}\n";
        }
        if ($case->second_party_address) {
            $context .= "     Address: {$case->second_party_address}\n";
        }
        $context .= "     Council: " . ($case->second_party_council ?? 'Not Specified') . "\n\n";

        // Additional Clients
        if ($case->clients->isNotEmpty()) {
            $context .= "   ADDITIONAL PARTIES:\n";
            foreach ($case->clients as $client) {
                if ($client->type !== 'first_party' && $client->type !== 'second_party') {
                    $context .= "     - {$client->name} ({$client->type_label}) - {$client->council_label}\n";
                    if ($client->contact_number) {
                        $context .= "       Contact: {$client->contact_number}\n";
                    }
                }
            }
            $context .= "\n";
        }

        // 3. Legal Representation
        $context .= "3. LEGAL REPRESENTATION:\n";
        if ($case->assignedLawyer) {
            $context .= "   Assigned Lawyer: {$case->assignedLawyer->name}\n";
            if ($case->assignedLawyer->specialization) {
                $context .= "     Specialization: {$case->assignedLawyer->specialization}\n";
            }
            if ($case->assignedLawyer->license_number) {
                $context .= "     License: {$case->assignedLawyer->license_number}\n";
            }
        } else {
            $context .= "   Assigned Lawyer: Not Assigned\n";
        }
        
        if ($case->additionalLawyer) {
            $context .= "   Additional Lawyer: {$case->additionalLawyer->name}\n";
        }
        
        if ($case->offending_lawyer) {
            $context .= "   Opposing Lawyer: {$case->offending_lawyer}\n";
        }
        
        if ($case->opponent_council) {
            $context .= "   Opponent Council: {$case->opponent_council}\n";
        }
        
        if ($case->power_of_attorney) {
            $context .= "   Power of Attorney: {$case->power_of_attorney}\n";
        }
        $context .= "\n";

        // 4. Hearings History
        if ($case->hearings->isNotEmpty()) {
            $context .= "4. HEARINGS HISTORY:\n";
            foreach ($case->hearings as $hearing) {
                $context .= "   Hearing Date: " . $hearing->hearing_date->format('d-m-Y') . "\n";
                if ($hearing->hearing_time) {
                    $context .= "   Time: " . $hearing->hearing_time->format('h:i A') . "\n";
                }
                $context .= "   Type: " . ucfirst($hearing->type ?? 'Regular') . "\n";
                $context .= "   Purpose: {$hearing->purpose}\n";
                if ($hearing->judge) {
                    $context .= "   Judge: {$hearing->judge}\n";
                }
                if ($hearing->outcome) {
                    $context .= "   Outcome: {$hearing->outcome}\n";
                }
                if ($hearing->notes) {
                    $context .= "   Notes: " . substr($hearing->notes, 0, 200) . "\n";
                }
                
                // Next hearing if scheduled
                if ($hearing->next_hearing_date) {
                    $context .= "   Next Hearing: " . $hearing->next_hearing_date->format('d-m-Y');
                    if ($hearing->next_hearing_time) {
                        $context .= " at " . $hearing->next_hearing_time->format('h:i A');
                    }
                    $context .= "\n";
                }
                $context .= "   ---\n";
            }
            $context .= "\n";
        }

        // 5. Documents Information
        if ($case->documents->isNotEmpty()) {
            $context .= "5. DOCUMENTS AND EVIDENCE:\n";
            foreach ($case->documents as $document) {
                $context .= "   - {$document->name}\n";
                $context .= "     Type: " . ($document->category_label ?? 'Document') . "\n";
                $context .= "     Uploaded: " . $document->created_at->format('d-m-Y') . "\n";
                if ($document->description) {
                    $context .= "     Description: " . substr($document->description, 0, 100) . "\n";
                }
            }
            $context .= "\n";
        }

        // 6. FIR Information (if applicable)
        if ($case->fir_no || $case->police_station || $case->offence) {
            $context .= "6. FIR DETAILS:\n";
            if ($case->fir_no) {
                $context .= "   FIR Number: {$case->fir_no}/{$case->fir_year}\n";
            }
            if ($case->police_station) {
                $context .= "   Police Station: {$case->police_station}\n";
            }
            if ($case->offence) {
                $context .= "   Offence: {$case->offence}\n";
            }
            if ($case->other_fir_details) {
                $context .= "   Additional Details: " . substr($case->other_fir_details, 0, 200) . "\n";
            }
            $context .= "\n";
        }

        // 7. Case Notes and Remarks
        $context .= "7. CASE NOTES AND REMARKS:\n";
        if ($case->case_notes) {
            $context .= "   Case Notes: {$case->case_notes}\n";
        }
        if ($case->remarks) {
            $context .= "   Remarks: {$case->remarks}\n";
        }
        if ($case->next_order) {
            $context .= "   Next Order: {$case->next_order}\n";
        }
        $context .= "\n";

        // 8. Case Statistics
        $context .= "8. CASE STATISTICS:\n";
        $context .= "   Total Hearings: " . $case->hearings->count() . "\n";
        $context .= "   Total Documents: " . $case->documents->count() . "\n";
        $context .= "   Total Parties: " . $case->clients->count() . "\n";
        $context .= "   Case Age: " . $case->filing_date->diffInDays(now()) . " days\n";
        $context .= "   Progress: " . ($case->progress_percentage ?? 0) . "%\n";

        return $context;
    }

    /**
     * Call Ollama API
     */
    public function callOllama($prompt, $model = null)
    {
        try {
            $model = $model ?: $this->model;
            
            $response = Http::timeout(120)->post($this->baseUrl . '/api/generate', [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'top_k' => 40,
                    'repeat_penalty' => 1.1,
                    'num_predict' => 2000,
                ]
            ]);

            if ($response->successful()) {
                return $response->json()['response'];
            } else {
                Log::error('Ollama API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return "I apologize, but I'm having trouble processing your request at the moment. Please try again or check if Ollama is running.";
            }
        } catch (\Exception $e) {
            Log::error('Ollama API Exception', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl
            ]);
            
            return "I'm currently unavailable. Please ensure Ollama is running on your system. Start Ollama with: 'ollama serve'";
        }
    }

    /**
     * Ask question about case
     */
    public function askCaseQuestion($caseId, $userId, $question)
    {
        $caseData = $this->getFullCaseData($caseId, $userId);
        
        if (!$caseData) {
            return [
                'success' => false,
                'error' => 'Case not found or access denied',
                'answer' => null
            ];
        }

        // Prepare detailed prompt
        $prompt = "You are an expert legal AI assistant in Pakistan. Your task is to analyze the following case information and answer the user's question professionally and accurately.\n\n";
        $prompt .= "=== CASE DATA ===\n";
        $prompt .= $caseData . "\n";
        $prompt .= "=== END CASE DATA ===\n\n";
        $prompt .= "USER'S QUESTION: {$question}\n\n";
        $prompt .= "INSTRUCTIONS:\n";
        $prompt .= "1. Answer based ONLY on the case information provided above\n";
        $prompt .= "2. If information is missing, state what additional information is needed\n";
        $prompt .= "3. Provide practical, actionable advice\n";
        $prompt .= "4. Reference specific dates, names, and documents when relevant\n";
        $prompt .= "5. Format your response with clear sections if needed\n";
        $prompt .= "6. Keep responses concise but comprehensive\n\n";
        $prompt .= "BEGIN YOUR RESPONSE:\n";

        $answer = $this->callOllama($prompt);

        return [
            'success' => true,
            'answer' => $answer,
            'case_data_length' => strlen($caseData)
        ];
    }

    /**
     * Analyze case for weaknesses and strengths
     */
    public function analyzeCase($caseId, $userId)
    {
        $caseData = $this->getFullCaseData($caseId, $userId);
        
        if (!$caseData) {
            return "Case not found or access denied.";
        }

        $prompt = "As a senior legal analyst, analyze this case and provide a comprehensive assessment:\n\n";
        $prompt .= $caseData . "\n\n";
        $prompt .= "Please provide analysis in this format:\n";
        $prompt .= "1. CASE STRENGTHS\n";
        $prompt .= "2. POTENTIAL WEAKNESSES\n";
        $prompt .= "3. LEGAL STRATEGY RECOMMENDATIONS\n";
        $prompt .= "4. REQUIRED DOCUMENTATION\n";
        $prompt .= "5. TIMELINE PREDICTIONS\n";
        $prompt .= "6. RISK ASSESSMENT\n";
        $prompt .= "7. NEXT IMMEDIATE ACTIONS\n";

        return $this->callOllama($prompt);
    }

    /**
     * Generate legal documents draft
     */
    public function generateDocumentDraft($caseId, $userId, $documentType)
    {
        $caseData = $this->getFullCaseData($caseId, $userId);
        
        if (!$caseData) {
            return "Case not found or access denied.";
        }

        $documentTemplates = [
            'application' => "Draft a court application with proper format, cause title, prayers, and grounds.",
            'reply' => "Draft a reply to the opposing party's application.",
            'affidavit' => "Draft an affidavit with proper verification clause.",
            'notice' => "Draft a legal notice under Section 80 CPC.",
            'agreement' => "Draft a settlement agreement between parties.",
            'power_of_attorney' => "Draft a Power of Attorney document.",
            'complaint' => "Draft a criminal complaint under relevant sections."
        ];

        $template = $documentTemplates[$documentType] ?? "Draft a legal document.";

        $prompt = "You are a legal drafter. Using the following case information, {$template}\n\n";
        $prompt .= "CASE INFORMATION:\n";
        $prompt .= $caseData . "\n\n";
        $prompt .= "INSTRUCTIONS:\n";
        $prompt .= "1. Use proper legal format and language\n";
        $prompt .= "2. Include all necessary parties' details\n";
        $prompt .= "3. Reference relevant laws if known\n";
        $prompt .= "4. Include date and signature places\n";
        $prompt .= "5. Make it ready-to-use with [BRACKETS] for custom information\n\n";
        $prompt .= "DOCUMENT DRAFT:\n";

        return $this->callOllama($prompt);
    }

    /**
     * Predict case outcome
     */
    public function predictOutcome($caseId, $userId)
    {
        $caseData = $this->getFullCaseData($caseId, $userId);
        
        if (!$caseData) {
            return "Case not found or access denied.";
        }

        $prompt = "As an experienced legal predictor, analyze this case and predict the likely outcome:\n\n";
        $prompt .= $caseData . "\n\n";
        $prompt .= "Consider:\n";
        $prompt .= "1. Strength of evidence\n";
        $prompt .= "2. Legal precedents in Pakistan\n";
        $prompt .= "3. Current case status and history\n";
        $prompt .= "4. Typical outcomes for similar cases\n";
        $prompt .= "5. Potential settlement possibilities\n\n";
        $prompt .= "Provide prediction with confidence percentage and reasoning.";

        return $this->callOllama($prompt);
    }

    /**
     * Get next hearing preparation checklist
     */
    public function getHearingChecklist($caseId, $userId)
    {
        $caseData = $this->getFullCaseData($caseId, $userId);
        
        if (!$caseData) {
            return "Case not found or access denied.";
        }

        $prompt = "Create a comprehensive checklist for the next hearing preparation:\n\n";
        $prompt .= $caseData . "\n\n";
        $prompt .= "Checklist should include:\n";
        $prompt .= "1. Documents to bring\n";
        $prompt .= "2. Witness preparation\n";
        $prompt .= "3. Legal arguments to make\n";
        $prompt .= "4. Questions for opposing party\n";
        $prompt .= "5. Fee arrangements\n";
        $prompt .= "6. Court etiquette reminders\n";
        $prompt .= "7. Backup plans\n";

        return $this->callOllama($prompt);
    }

    /**
     * Check Ollama server status
     */
    public function checkStatus()
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/api/tags');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get available Ollama models
     */
    public function getAvailableModels()
    {
        try {
            $response = Http::get($this->baseUrl . '/api/tags');
            if ($response->successful()) {
                return collect($response->json()['models'])->pluck('name')->toArray();
            }
        } catch (\Exception $e) {
            Log::error('Failed to get Ollama models', ['error' => $e->getMessage()]);
        }
        
        return ['llama2', 'mistral', 'codellama']; // Default fallback
    }
}