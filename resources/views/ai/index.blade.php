{{-- resources/views/ai/index.blade.php --}}
@extends('layouts.app')

@section('title', 'AI Legal Assistant')

@section('content')
<div class="ai-dashboard">
    <!-- AI Chat Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-4rem)]">
        <!-- Left Panel - Case Selection & Quick Actions -->
        <div class="lg:col-span-1 flex flex-col space-y-6">
            <!-- Case Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
                <h3 class="font-bold text-lg mb-3">Select Case</h3>
                <select id="caseSelector" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 dark:bg-gray-700">
                    <option value="">General Legal Question</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}" data-case="{{ json_encode($case) }}">
                            {{ $case->case_number }} - {{ $case->case_title }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Selected Case Info -->
                <div id="caseInfo" class="mt-4 hidden">
                    <h4 class="font-semibold text-sm text-gray-500">Selected Case</h4>
                    <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <p class="font-bold" id="caseTitle"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" id="caseDetails"></p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
                <h3 class="font-bold text-lg mb-3">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="quickAction('summary')" class="quick-action-btn" data-action="summary">
                        <i class="fas fa-file-alt"></i>
                        <span>Case Summary</span>
                    </button>
                    <button onclick="quickAction('analyze')" class="quick-action-btn" data-action="analyze">
                        <i class="fas fa-chart-line"></i>
                        <span>Analyze</span>
                    </button>
                    <button onclick="quickAction('predict')" class="quick-action-btn" data-action="predict">
                        <i class="fas fa-crystal-ball"></i>
                        <span>Predict</span>
                    </button>
                    <button onclick="quickAction('checklist')" class="quick-action-btn" data-action="checklist">
                        <i class="fas fa-tasks"></i>
                        <span>Checklist</span>
                    </button>
                </div>
            </div>

            <!-- Document Generation -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
                <h3 class="font-bold text-lg mb-3">Generate Document</h3>
                <select id="documentType" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 dark:bg-gray-700 mb-3">
                    <option value="">Select Document Type</option>
                    <option value="application">Court Application</option>
                    <option value="reply">Reply</option>
                    <option value="affidavit">Affidavit</option>
                    <option value="notice">Legal Notice</option>
                    <option value="agreement">Settlement Agreement</option>
                    <option value="power_of_attorney">Power of Attorney</option>
                    <option value="complaint">Criminal Complaint</option>
                </select>
                <button onclick="generateDocument()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg">
                    Generate Document
                </button>
            </div>
        </div>

        <!-- Middle Panel - Chat Interface -->
        <div class="lg:col-span-2 flex flex-col bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Legal AI Assistant</h2>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-blue-100" id="aiStatus">Connecting...</span>
                                <span id="statusIndicator" class="w-3 h-3 bg-gray-400 rounded-full"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="clearChat()" class="text-white hover:text-gray-200" title="Clear Chat">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <button onclick="downloadChat()" class="text-white hover:text-gray-200" title="Download Chat">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="flex-1 p-4 overflow-y-auto" id="chatContainer" style="height: 60vh;">
                <div id="messages"></div>
                <div id="typingIndicator" class="hidden flex items-center space-x-2 p-4">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <span class="text-sm text-gray-500">AI is thinking...</span>
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex space-x-2">
                    <textarea 
                        id="messageInput"
                        rows="3"
                        placeholder="Ask your legal question or describe what you need..."
                        class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-700 resize-none"
                        onkeydown="handleEnter(event)"
                    ></textarea>
                    <button onclick="sendMessage()" id="sendBtn" class="self-end bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="mt-2 flex justify-between items-center text-sm text-gray-500">
                    <div>
                        <span id="selectedCaseInfo">No case selected</span>
                    </div>
                    <div class="flex space-x-4">
                        <button onclick="addContext('laws')" class="hover:text-blue-600">Add Legal References</button>
                        <button onclick="addContext('format')" class="hover:text-blue-600">Format Document</button>
                        <button onclick="addContext('translate')" class="hover:text-blue-600">Translate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Recent Conversations -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
        <h3 class="font-bold text-lg mb-3">Recent Conversations</h3>
        <div id="conversationHistory" class="space-y-2">
            <!-- Conversations will be loaded here -->
        </div>
    </div>
</div>

<!-- AI Chat Template -->
<template id="messageTemplate">
    <div class="message">
        <div class="message-header">
            <span class="sender"></span>
            <span class="time"></span>
        </div>
        <div class="message-content"></div>
    </div>
</template>

@push('styles')
<style>
.ai-dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1rem;
}

.quick-action-btn {
    @apply flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-gray-700 
           hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors 
           border border-gray-200 dark:border-gray-600;
}

.quick-action-btn i {
    @apply text-xl mb-2 text-blue-600 dark:text-blue-400;
}

.quick-action-btn span {
    @apply text-xs font-medium;
}

.message {
    @apply mb-4 p-3 rounded-lg;
}

.message.user {
    @apply bg-blue-100 dark:bg-blue-900 ml-auto max-w-3/4;
}

.message.ai {
    @apply bg-gray-100 dark:bg-gray-700 mr-auto max-w-3/4;
}

.message-header {
    @apply flex justify-between items-center mb-2 text-sm;
}

.message-content {
    @apply whitespace-pre-wrap;
}
</style>
@endpush

@push('scripts')
<script>
class AIChat {
    constructor() {
        this.currentCase = null;
        this.conversation = [];
        this.isTyping = false;
        this.aiStatus = 'disconnected';
        
        this.init();
    }
    
    init() {
        this.checkAIStatus();
        this.loadConversationHistory();
        this.setupEventListeners();
        this.loadCases();
        
        // Auto-refresh status every 30 seconds
        setInterval(() => this.checkAIStatus(), 30000);
    }
    
    setupEventListeners() {
        // Case selector
        document.getElementById('caseSelector').addEventListener('change', (e) => {
            this.selectCase(e.target.value);
        });
        
        // Document type selector
        document.getElementById('documentType').addEventListener('change', (e) => {
            if (e.target.value && this.currentCase) {
                this.generateDocument(e.target.value);
            }
        });
    }
    
    async checkAIStatus() {
        try {
            const response = await fetch('/ai/status');
            const data = await response.json();
            
            this.aiStatus = data.running ? 'connected' : 'disconnected';
            this.updateStatusIndicator(data.running);
            
            if (data.running && this.conversation.length === 0) {
                this.addMessage(
                    "Hello! I am your Legal AI Assistant. I can help you analyze cases, predict outcomes, generate documents, and answer legal questions. How can I assist you today?",
                    'ai'
                );
            }
        } catch (error) {
            console.error('Failed to check AI status:', error);
            this.aiStatus = 'error';
            this.updateStatusIndicator(false);
        }
    }
    
    updateStatusIndicator(isConnected) {
        const indicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('aiStatus');
        
        if (isConnected) {
            indicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
            statusText.textContent = 'AI Assistant Connected';
        } else {
            indicator.className = 'w-3 h-3 bg-red-500 rounded-full';
            statusText.textContent = 'AI Assistant Disconnected';
        }
    }
    
    selectCase(caseId) {
        if (!caseId) {
            this.currentCase = null;
            document.getElementById('caseInfo').classList.add('hidden');
            document.getElementById('selectedCaseInfo').textContent = 'No case selected';
            return;
        }
        
        const selectedOption = document.getElementById('caseSelector').selectedOptions[0];
        const caseData = JSON.parse(selectedOption.dataset.case);
        
        this.currentCase = caseData;
        
        // Update UI
        document.getElementById('caseTitle').textContent = caseData.case_title;
        document.getElementById('caseDetails').textContent = 
            `${caseData.case_number} | ${caseData.court_name} | ${caseData.case_status}`;
        document.getElementById('caseInfo').classList.remove('hidden');
        document.getElementById('selectedCaseInfo').textContent = 
            `Discussing: ${caseData.case_number}`;
    }
    
    async sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        
        if (!message || this.isTyping) return;
        
        // Add user message
        this.addMessage(message, 'user');
        input.value = '';
        this.showTypingIndicator();
        
        try {
            const url = this.currentCase 
                ? `/ai/chat/${this.currentCase.id}`
                : '/ai/chat';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.success) {
                this.addMessage(data.response, 'ai');
                this.saveConversation();
            } else {
                this.addMessage(`Error: ${data.error}`, 'error');
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('Sorry, I encountered an error. Please try again.', 'error');
        }
    }
    
    async quickAction(action) {
        if (!this.currentCase || this.isTyping) return;
        
        this.addMessage(`Generating ${action}...`, 'user');
        this.showTypingIndicator();
        
        try {
            const response = await fetch(`/ai/quick/${this.currentCase.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ action })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.success) {
                this.addMessage(`**${data.title}:**\n\n${data.response}`, 'ai');
                this.saveConversation();
            } else {
                this.addMessage(`Error generating ${action}`, 'error');
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage(`Failed to generate ${action}`, 'error');
        }
    }
    
    async generateDocument(documentType) {
        if (!this.currentCase || this.isTyping) return;
        
        this.addMessage(`Generating ${documentType} document...`, 'user');
        this.showTypingIndicator();
        
        try {
            const response = await fetch(`/ai/generate-document/${this.currentCase.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ document_type: documentType })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.success) {
                this.addMessage(`**${documentType.toUpperCase()} Document:**\n\n${data.content}`, 'ai');
                this.saveConversation();
                
                // Enable download
                this.createDownloadLink(data.content, `${documentType}_${this.currentCase.case_number}.txt`);
            } else {
                this.addMessage(`Error: ${data.error}`, 'error');
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('Failed to generate document', 'error');
        }
    }
    
    addMessage(text, sender) {
        const messagesDiv = document.getElementById('messages');
        const message = {
            id: Date.now(),
            text: text,
            sender: sender,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            date: new Date().toLocaleDateString()
        };
        
        this.conversation.push(message);
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        messageDiv.innerHTML = `
            <div class="message-header">
                <span class="font-bold">${sender === 'user' ? 'You' : 'AI Assistant'}</span>
                <span class="text-xs opacity-75">${message.time}</span>
            </div>
            <div class="message-content">${this.formatMessage(text)}</div>
        `;
        
        messagesDiv.appendChild(messageDiv);
        this.scrollToBottom();
    }
    
    formatMessage(text) {
        // Format markdown-like text
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">$1</code>')
            .replace(/\n/g, '<br>')
            .replace(/^- (.*)$/gm, '<li>$1</li>')
            .replace(/(<li>.*<\/li>)/g, '<ul class="list-disc pl-5 my-1">$1</ul>');
    }
    
    showTypingIndicator() {
        this.isTyping = true;
        document.getElementById('typingIndicator').classList.remove('hidden');
        this.scrollToBottom();
    }
    
    hideTypingIndicator() {
        this.isTyping = false;
        document.getElementById('typingIndicator').classList.add('hidden');
    }
    
    scrollToBottom() {
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    }
    
    clearChat() {
        if (confirm('Are you sure you want to clear the chat?')) {
            document.getElementById('messages').innerHTML = '';
            this.conversation = [];
            localStorage.removeItem('ai_conversation');
        }
    }
    
    saveConversation() {
        try {
            localStorage.setItem('ai_conversation', JSON.stringify(this.conversation.slice(-50)));
            this.updateConversationHistory();
        } catch (error) {
            console.error('Failed to save conversation:', error);
        }
    }
    
    loadConversationHistory() {
        try {
            const saved = localStorage.getItem('ai_conversation');
            if (saved) {
                const conversations = JSON.parse(saved);
                this.conversation = conversations;
                
                // Display all messages
                conversations.forEach(msg => {
                    this.addMessage(msg.text, msg.sender);
                });
            }
        } catch (error) {
            console.error('Failed to load conversation history:', error);
        }
    }
    
    updateConversationHistory() {
        const historyDiv = document.getElementById('conversationHistory');
        historyDiv.innerHTML = '';
        
        // Group by date
        const grouped = this.conversation.reduce((acc, msg) => {
            if (!acc[msg.date]) {
                acc[msg.date] = [];
            }
            acc[msg.date].push(msg);
            return acc;
        }, {});
        
        Object.entries(grouped).forEach(([date, messages]) => {
            const dateDiv = document.createElement('div');
            dateDiv.className = 'mb-3';
            dateDiv.innerHTML = `
                <h4 class="text-sm font-semibold text-gray-500 mb-2">${date}</h4>
                <div class="space-y-1">
                    ${messages.map(msg => `
                        <div class="text-xs p-2 bg-gray-50 dark:bg-gray-700 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                             onclick="aiChat.loadConversation('${msg.id}')">
                            <span class="font-medium">${msg.sender === 'user' ? 'Q:' : 'A:'}</span>
                            <span class="truncate">${msg.text.substring(0, 50)}${msg.text.length > 50 ? '...' : ''}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            historyDiv.appendChild(dateDiv);
        });
    }
    
    createDownloadLink(content, filename) {
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
    }
    
    addContext(type) {
        const input = document.getElementById('messageInput');
        const contexts = {
            laws: 'Please include relevant Pakistani laws and legal references.',
            format: 'Please format this as a formal legal document.',
            translate: 'Please translate this to Urdu/English.'
        };
        
        if (contexts[type]) {
            input.value += `\n\n${contexts[type]}`;
        }
    }
}

// Initialize AI Chat
let aiChat = new AIChat();

// Global functions for inline event handlers
window.handleEnter = function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        aiChat.sendMessage();
    }
};

window.sendMessage = function() {
    aiChat.sendMessage();
};

window.quickAction = function(action) {
    aiChat.quickAction(action);
};

window.generateDocument = function() {
    const type = document.getElementById('documentType').value;
    if (type) {
        aiChat.generateDocument(type);
    }
};

window.clearChat = function() {
    aiChat.clearChat();
};

window.downloadChat = function() {
    const content = aiChat.conversation.map(msg => 
        `${msg.sender.toUpperCase()} [${msg.time}]:\n${msg.text}\n\n`
    ).join('---\n');
    
    aiChat.createDownloadLink(content, `ai_chat_${new Date().toISOString().split('T')[0]}.txt`);
};
</script>
@endpush
@endsection