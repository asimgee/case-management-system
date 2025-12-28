{{-- resources/views/components/ai-assistant.blade.php --}}
@props(['case' => null])

<div class="ai-assistant-wrapper" x-data="aiAssistant()" x-init="init()">
    <!-- Main AI Button -->
    <button @click="toggleChat" 
            :class="{ 'bg-green-600': isOpen, 'bg-blue-600': !isOpen }"
            class="fixed bottom-6 right-6 w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-white z-50 hover:scale-110 transition-all duration-300">
        <i class="fas fa-robot text-xl" :class="{ 'fa-times': isOpen, 'fa-comment': !isOpen }"></i>
    </button>

    <!-- AI Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-24 right-6 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50"
         style="height: 600px;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white">Legal AI Assistant</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-blue-100">Powered by Ollama</span>
                            <span x-show="status.running" class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span x-show="!status.running" class="w-2 h-2 bg-red-400 rounded-full"></span>
                        </div>
                    </div>
                </div>
                <button @click="toggleChat" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Status Indicator -->
            <div x-show="!status.running" class="mt-2 p-2 bg-red-500/20 border border-red-500/30 rounded-lg">
                <p class="text-xs text-white">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Ollama not running. Start with: <code class="bg-black/30 px-1 rounded">ollama serve</code>
                </p>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="flex-1 p-4 overflow-y-auto h-96" id="aiMessages">
            <template x-for="(message, index) in messages" :key="index">
                <div :class="{ 
                    'flex justify-end': message.sender === 'user',
                    'flex justify-start': message.sender === 'ai'
                }" class="mb-3">
                    <div :class="{ 
                        'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': message.sender === 'user',
                        'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200': message.sender === 'ai'
                    }" class="max-w-xs lg:max-w-md rounded-lg p-3">
                        <div class="flex items-start space-x-2">
                            <div x-show="message.sender === 'ai'" class="flex-shrink-0">
                                <i class="fas fa-robot text-purple-500 mt-1"></i>
                            </div>
                            <div class="flex-1">
                                <div class="whitespace-pre-wrap" x-html="formatMessage(message.text)"></div>
                                <div class="text-xs opacity-50 mt-1" x-text="message.time"></div>
                            </div>
                            <div x-show="message.sender === 'user'" class="flex-shrink-0">
                                <i class="fas fa-user text-blue-500 mt-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex justify-start mb-3">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div x-show="caseId" class="px-4 pb-2">
            <div class="grid grid-cols-2 gap-2 mb-2">
                <button @click="quickAction('summary')" 
                        class="text-xs bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 py-2 rounded-lg transition-colors">
                    <i class="fas fa-file-alt mr-1"></i> Summary
                </button>
                <button @click="quickAction('analyze')" 
                        class="text-xs bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 py-2 rounded-lg transition-colors">
                    <i class="fas fa-chart-line mr-1"></i> Analyze
                </button>
                <button @click="quickAction('predict')" 
                        class="text-xs bg-green-500/10 hover:bg-green-500/20 text-green-600 dark:text-green-400 py-2 rounded-lg transition-colors">
                    <i class="fas fa-crystal-ball mr-1"></i> Predict
                </button>
                <button @click="quickAction('checklist')" 
                        class="text-xs bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 py-2 rounded-lg transition-colors">
                    <i class="fas fa-tasks mr-1"></i> Checklist
                </button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="flex space-x-2">
                <input x-model="inputMessage" 
                       @keyup.enter="sendMessage"
                       :disabled="!status.running"
                       placeholder="Ask your legal question..."
                       class="flex-1 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                
                <button @click="sendMessage" 
                        :disabled="!status.running || !inputMessage.trim()"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            
            <!-- Document Generation -->
            <div x-show="caseId" class="mt-2">
                <select x-model="selectedDocument" 
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm">
                    <option value="">Generate Document...</option>
                    <option value="application">Court Application</option>
                    <option value="reply">Reply</option>
                    <option value="affidavit">Affidavit</option>
                    <option value="notice">Legal Notice</option>
                    <option value="agreement">Settlement Agreement</option>
                    <option value="power_of_attorney">Power of Attorney</option>
                    <option value="complaint">Criminal Complaint</option>
                </select>
            </div>
        </div>
    </div>
</div>

<script>
function aiAssistant() {
    return {
        isOpen: false,
        inputMessage: '',
        messages: [],
        isTyping: false,
        caseId: @json($case->id ?? null),
        status: {
            running: false,
            models: []
        },
        selectedDocument: '',
        
        init() {
            this.checkStatus();
            this.loadHistory();
            
            // Auto-check status every 30 seconds
            setInterval(() => {
                this.checkStatus();
            }, 30000);
            
            // Watch for document selection
            this.$watch('selectedDocument', (value) => {
                if (value) {
                    this.generateDocument(value);
                    this.selectedDocument = '';
                }
            });
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        },
        
        checkStatus() {
            fetch('/ai/status')
                .then(response => response.json())
                .then(data => {
                    this.status.running = data.running;
                    this.status.models = data.models;
                    
                    if (data.running && this.messages.length === 0) {
                        this.addMessage('Hello! I am your Legal AI Assistant. I can help you analyze cases, predict outcomes, generate documents, and answer legal questions. How can I assist you today?', 'ai');
                    }
                });
        },
        
        sendMessage() {
            if (!this.inputMessage.trim() || !this.status.running) return;
            
            const message = this.inputMessage.trim();
            this.inputMessage = '';
            
            this.addMessage(message, 'user');
            this.isTyping = true;
            
            const url = this.caseId 
                ? `/ai/chat/${this.caseId}`
                : '/ai/chat';
                
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isTyping = false;
                if (data.success) {
                    this.addMessage(data.response, 'ai');
                } else {
                    this.addMessage(`Error: ${data.error}`, 'ai');
                }
            })
            .catch(error => {
                this.isTyping = false;
                this.addMessage('Sorry, I encountered an error. Please try again.', 'ai');
            });
        },
        
        quickAction(action) {
            if (!this.caseId || !this.status.running) return;
            
            this.addMessage(`Generating ${action}...`, 'user');
            this.isTyping = true;
            
            fetch(`/ai/quick/${this.caseId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isTyping = false;
                if (data.success) {
                    this.addMessage(`**${data.title}:**\n\n${data.response}`, 'ai');
                } else {
                    this.addMessage(`Error generating ${action}`, 'ai');
                }
            });
        },
        
        generateDocument(type) {
            if (!this.caseId || !this.status.running) return;
            
            this.addMessage(`Generating ${type} document...`, 'user');
            this.isTyping = true;
            
            fetch(`/ai/generate-document/${this.caseId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    document_type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isTyping = false;
                if (data.success) {
                    this.addMessage(`**${type.toUpperCase()} Document Draft:**\n\n${data.content}`, 'ai');
                } else {
                    this.addMessage(`Error generating document: ${data.error}`, 'ai');
                }
            });
        },
        
        addMessage(text, sender) {
            this.messages.push({
                text: text,
                sender: sender,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });
            
            this.saveHistory();
            this.scrollToBottom();
        },
        
        formatMessage(text) {
            // Convert markdown-like formatting to HTML
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">$1</code>')
                .replace(/\n/g, '<br>')
                .replace(/^(#+)\s*(.*)$/gm, (match, hashes, content) => {
                    const level = hashes.length;
                    return `<h${level} class="font-bold mt-2 mb-1 text-${level === 1 ? 'xl' : level === 2 ? 'lg' : 'base'}">${content}</h${level}>`;
                })
                .replace(/^- (.*)$/gm, '<li>$1</li>')
                .replace(/(<li>.*<\/li>)/g, '<ul class="list-disc pl-5 my-1">$1</ul>')
                .replace(/\d+\.\s+(.*)$/gm, '<li>$1</li>')
                .replace(/(<li>.*<\/li>)/g, '<ol class="list-decimal pl-5 my-1">$1</ol>');
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('aiMessages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
        
        saveHistory() {
            try {
                localStorage.setItem('ai_chat_history', JSON.stringify(this.messages.slice(-20)));
            } catch (e) {
                console.error('Failed to save chat history:', e);
            }
        },
        
        loadHistory() {
            try {
                const saved = localStorage.getItem('ai_chat_history');
                if (saved) {
                    this.messages = JSON.parse(saved);
                }
            } catch (e) {
                console.error('Failed to load chat history:', e);
            }
        }
    }
}
</script>