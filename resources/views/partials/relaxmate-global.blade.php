{{-- 
    File ini berisi semua yang dibutuhkan untuk komponen chatbot global.
    Cukup @include('partials.relaxmate-global') di layout utama Anda.
--}}

<div x-data="chatComponent()" x-cloak class="fixed inset-0 z-50 pointer-events-none">
    
    <!-- Tombol Aksi (FAB) -->
    <div class="absolute bottom-6 right-6 pointer-events-auto">
        <button @click="toggleChat()"
                class="relative w-16 h-16 bg-gradient-to-br from-[#007BFF] to-blue-600 rounded-full shadow-2xl shadow-blue-500/40 flex items-center justify-center text-white transform hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-500/50">
            
            <div x-show="!isChatOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.17 48.17 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
            </div>
            
            <div x-show="isChatOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 rotate-90" x-transition:enter-end="opacity-100 rotate-0">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </div>

            <!-- Notifikasi pesan baru -->
            <span x-show="hasNewMessage" class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
        </button>
    </div>

    <!-- Jendela Chat -->
    <div x-show="isChatOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform translate-x-full opacity-0"
         x-transition:enter-end="transform translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="transform translate-x-0 opacity-100"
         x-transition:leave-end="transform translate-x-full opacity-0"
         class="fixed inset-y-0 right-0 w-full max-w-sm sm:max-w-md bg-slate-50 shadow-2xl z-40 flex flex-col rounded-l-2xl overflow-hidden pointer-events-auto">

        <!-- Header Chat -->
        <div class="flex items-center justify-between p-4 border-b bg-white flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img src="https://placehold.co/40x40/E0F2FE/334155?text=RM" alt="RelaxMate" class="w-10 h-10 rounded-full shadow-sm">
                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white"></span>
                </div>
                <div>
                    <p class="text-md font-semibold text-gray-800">RelaxMate</p>
                    <p class="text-xs text-gray-500">AI Wellness Coach Anda</p>
                </div>
            </div>
            <button @click="isChatOpen = false" class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" /></svg>
            </button>
        </div>
        
        <!-- Area Pesan -->
        <div class="flex-grow overflow-y-auto p-6" x-ref="chatArea">
            <div class="space-y-4">
                <template x-for="(message, index) in messages" :key="message.id || index">
                    <div class="space-y-4">
                        <!-- Penanda Tanggal -->
                        <template x-if="shouldShowDateSeparator(index)">
                            <div class="flex justify-center my-2">
                                <span class="text-xs text-gray-500 bg-gray-200 px-3 py-1 rounded-full" x-text="formatDateSeparator(message.created_at)"></span>
                            </div>
                        </template>

                        <!-- Bubble Pesan -->
                        <div class="flex items-start gap-3" :class="message.sender_type === 'user' ? 'justify-end' : 'justify-start'">
                            <!-- Avatar AI -->
                            <img x-show="message.sender_type === 'ai'" src="https://placehold.co/40x40/E0F2FE/334155?text=RM" alt="AI" class="w-8 h-8 rounded-full flex-shrink-0 shadow-sm">
                            
                            <div class="flex flex-col max-w-[85%]" :class="message.sender_type === 'user' ? 'items-end' : 'items-start'">
                                <div class="p-3 rounded-2xl break-words shadow-md"
                                     :class="message.sender_type === 'user' ? 'bg-[#007BFF] text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border'">
                                    <p class="text-sm" x-html="renderMarkdown(message.message_text)"></p>
                                </div>
                                <span class="text-xs text-gray-400 mt-1" x-text="formatTime(message.created_at)"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Indikator Loading -->
                <div x-show="isLoading" class="flex items-start gap-3 justify-start">
                    <img src="https://placehold.co/40x40/E0F2FE/334155?text=RM" alt="AI" class="w-8 h-8 rounded-full flex-shrink-0 shadow-sm">
                    <div class="bg-white p-3 rounded-2xl rounded-bl-none inline-flex items-center gap-2 border">
                        <span class="h-2 w-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0s;"></span>
                        <span class="h-2 w-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></span>
                        <span class="h-2 w-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.4s;"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Area Input Pesan -->
        <div class="p-4 border-t bg-white flex-shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <textarea x-model="newMessage"
                          @keydown.enter.exact.prevent="sendMessage()"
                          :disabled="isLoading" rows="1" 
                          class="flex-grow p-3 bg-slate-100 rounded-full border-transparent focus:border-[#007BFF] focus:ring-[#007BFF] resize-none"
                          placeholder="Ketik pesan Anda..."></textarea>
                
                <button type="submit" :disabled="isLoading || newMessage.trim() === ''"
                        class="w-12 h-12 flex-shrink-0 text-white rounded-full flex items-center justify-center transition-all duration-300"
                        :class="{ 'bg-[#007BFF] hover:bg-blue-600': newMessage.trim() !== '', 'bg-gray-300 cursor-not-allowed': newMessage.trim() === '' || isLoading }">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function chatComponent() {
    return {
        isChatOpen: false,
        hasNewMessage: false,
        isLoading: true,
        messages: [],
        newMessage: '',
        conversationId: null,

        init() {
            this.conversationId = localStorage.getItem('relaxmate_conversation_id');
            this.fetchHistory();
            this.$watch('messages', () => this.scrollToBottom());
        },

        toggleChat() {
            this.isChatOpen = !this.isChatOpen;
            if (this.isChatOpen) {
                this.hasNewMessage = false;
                this.scrollToBottom();
            }
        },

        async fetchHistory() {
            this.isLoading = true;
            if (!this.conversationId) {
                this.messages = [this.createWelcomeMessage()];
                this.isLoading = false;
                return;
            }

            try {
                const response = await fetch(`{{ route('relaxmate.history') }}?conversation_id=${this.conversationId}`, {
                    method: 'GET',
                    headers: { 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    // [PERBAIKAN] Log yang lebih detail di konsol browser
                    const errorText = await response.text();
                    console.error("Server merespons dengan error:", response.status, errorText);
                    throw new Error(`Gagal memuat riwayat (Status: ${response.status}). Cek log server untuk detail.`);
                }
                
                const data = await response.json();
                this.messages = data.length > 0 ? data : [this.createWelcomeMessage()];
            } catch (error) {
                console.error('Error saat fetchHistory:', error);
                this.messages = [this.createWelcomeMessage(), this.createErrorMessage(error.message)];
            } finally {
                this.isLoading = false;
            }
        },
        
        async sendMessage() {
            const userMessageText = this.newMessage.trim();
            if (userMessageText === '' || this.isLoading) return;

            if (!this.conversationId) {
                this.conversationId = `conv_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                localStorage.setItem('relaxmate_conversation_id', this.conversationId);
                this.messages = this.messages.filter(m => m.id !== 'welcome');
            }

            this.messages.push({
                id: `user_${Date.now()}`,
                sender_type: 'user',
                message_text: userMessageText,
                created_at: new Date().toISOString()
            });
            
            this.isLoading = true;
            this.newMessage = '';

            try {
                const response = await fetch("{{ route('relaxmate.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: userMessageText,
                        conversation_id: this.conversationId
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Terjadi kesalahan pada server.');
                }
                
                const aiResponse = await response.json();

                this.messages.push({
                    id: `ai_${Date.now()}`,
                    sender_type: 'ai',
                    message_text: aiResponse.reply,
                    metadata: aiResponse.metadata,
                    created_at: new Date().toISOString()
                });
                
                if (!this.isChatOpen) {
                    this.hasNewMessage = true;
                }

            } catch (error) {
                console.error('Error sending message:', error);
                this.messages.push(this.createErrorMessage(error.message));
            } finally {
                this.isLoading = false;
            }
        },

        // --- Fungsi Utilitas ---
        createWelcomeMessage() {
            return { id: 'welcome', sender_type: 'ai', message_text: 'Halo! Saya RelaxMate, AI Wellness Coach Anda. Ada yang bisa saya bantu hari ini?', created_at: new Date().toISOString() };
        },
        createErrorMessage(text) {
            return { id: `error_${Date.now()}`, sender_type: 'ai', message_text: `Maaf, terjadi kesalahan: ${text}`, created_at: new Date().toISOString() };
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.chatArea;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },
        renderMarkdown(text) {
            if (typeof text !== 'string') return '';
            return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        },
        formatTime(timestamp) {
            if (!timestamp) return '';
            return new Date(timestamp).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },
        shouldShowDateSeparator(index) {
            if (index === 0) return true;
            const current = new Date(this.messages[index].created_at).setHours(0,0,0,0);
            const previous = new Date(this.messages[index-1].created_at).setHours(0,0,0,0);
            return current !== previous;
        },
        formatDateSeparator(timestamp) {
            const today = new Date().setHours(0,0,0,0);
            const yesterday = new Date(today).setDate(new Date().getDate() - 1);
            const msgDate = new Date(timestamp).setHours(0,0,0,0);

            if (msgDate === today) return 'Hari ini';
            if (msgDate === yesterday) return 'Kemarin';
            return new Date(msgDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
    }
}
</script>
