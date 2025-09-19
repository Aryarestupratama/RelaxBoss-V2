<x-app-layout>
    <x-slot name="title">
        AI RelaxMate
    </x-slot>

    {{-- [DIROMBAK TOTAL] Struktur utama diubah untuk mendukung layout seperti Gemini --}}
    <div x-data="chatComponent()" x-cloak class="h-[calc(100vh-129px)] bg-slate-100 flex overflow-hidden">
        
        {{-- [IMPROVISASI] Sidebar Riwayat Percakapan dengan transisi lebar --}}
        <aside class="bg-white flex-shrink-0 transition-all duration-300 ease-in-out" 
               :class="isSidebarOpen ? 'w-80 border-r' : 'w-0 lg:w-20 lg:border-r'">
            
            <div class="h-full flex flex-col">
                <div class="p-4 border-b flex justify-between items-center flex-shrink-0" :class="!isSidebarOpen && 'lg:justify-center'">
                    <h2 class="font-bold text-lg text-gray-800" x-show="isSidebarOpen" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Riwayat</h2>
                    {{-- [PERBAIKAN] Ikon diubah menjadi hamburger standar --}}
                    <button @click="toggleSidebar()" class="text-gray-500 hover:text-gray-800 p-2 rounded-md hover:bg-slate-100">
                        <i class="fa-solid fa-bars fa-lg"></i>
                    </button>
                </div>
                
                <div class="flex-grow overflow-y-auto overflow-x-hidden">
                    <div x-show="Object.keys(conversationGroups).length === 0 && !isLoading" class="p-8 text-center text-gray-400">
                        <i class="fa-solid fa-comments fa-2x mb-4"></i>
                        <p class="text-sm" x-show="isSidebarOpen">Percakapan Anda akan tersimpan di sini.</p>
                    </div>

                    <ul class="p-2">
                        <template x-for="(group, date) in conversationGroups" :key="date">
                            <li>
                                <h3 class="px-3 py-2 text-xs font-bold text-gray-400 uppercase" x-show="isSidebarOpen" x-text="formatDateSeparator(date)"></h3>
                                <ul>
                                    <template x-for="conversation in group" :key="conversation.conversation_id">
                                        <li class="p-1">
                                            {{-- [PERUBAHAN] Kirim tanggal ke fungsi loadConversation --}}
                                            <button @click="loadConversation(conversation.conversation_id, date)" 
                                                    :class="{ 'bg-blue-100 text-blue-700': conversation.conversation_id === conversationId }"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-100 transition truncate">
                                                <i class="fa-regular fa-comment text-gray-500"></i>
                                                {{-- [PERUBAHAN] Tampilkan judul dan tanggal lengkap --}}
                                                <div class="flex-grow text-left overflow-hidden" x-show="isSidebarOpen">
                                                    <span class="text-sm font-semibold text-gray-800" x-text="conversation.title || 'Percakapan Awal'"></span>
                                                    <span class="text-xs text-gray-500 block" x-text="formatFullDate(conversation.created_at)"></span>
                                                </div>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </li>
                        </template>
                    </ul>
                </div>
                 {{-- [DIHAPUS] Tombol Percakapan Baru --}}
            </div>
        </aside>

        {{-- Area Chat Utama --}}
        <div class="flex-grow h-full flex flex-col bg-white">
            <!-- Header Chat -->
            <div class="border-b p-4 flex items-center justify-between flex-shrink-0 bg-slate-50">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full overflow-hidden bg-blue-100">
                            <img src="{{ asset('storage/components/icon-relaxmate.png') }}" alt="RelaxMate" class="w-10 h-10 object-contain">
                        </div>
                        <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">AI RelaxMate</h1>
                        <p class="text-sm text-green-600">Online</p>
                    </div>
                </div>
            </div>

            <!-- Area Pesan -->
            <div class="flex-grow p-6 flex flex-col overflow-hidden">
                {{-- Layar Sambutan --}}
                <div 
                    x-show="messages.length === 0 && !isLoading" 
                    class="m-auto text-center text-gray-600 px-4">
                    <img src="{{ asset('storage/components/icon-relaxmate.png') }}" 
                        alt="RelaxMate Logo" 
                        class="w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 mx-auto mb-4 object-contain">
                    <h2 class="text-2xl font-bold text-gray-800">Bagaimana saya bisa membantu Anda hari ini?</h2>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
                        <template x-for="prompt in promptStarters" :key="prompt">
                            <button @click="startWithPrompt(prompt)" 
                                class="p-4 bg-slate-100 hover:bg-slate-200 rounded-lg text-left text-sm font-medium transition">
                                <p x-text="prompt"></p>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Kontainer Pesan (scrollable) --}}
                <div 
                    x-ref="chatArea" 
                    class="flex-1 overflow-y-auto pr-2 scroll-smooth flex flex-col-reverse"
                >
                    
                    <template x-for="(message, index) in messages" :key="message.id || index">
                        <div class="space-y-4">
                            <!-- Penanda Tanggal -->
                            <template x-if="shouldShowDateSeparator(index)">
                                <div class="flex justify-center my-2">
                                    <span class="text-xs text-gray-500 bg-gray-200 px-3 py-1 rounded-full" 
                                        x-text="formatDateSeparator(message.created_at)">
                                    </span>
                                </div>
                            </template>

                            <!-- Bubble Pesan -->
                            <div class="flex items-end gap-3" 
                                :class="message.sender_type === 'user' ? 'justify-end' : 'justify-start'">
                                
                                <!-- Avatar AI -->
                                <div x-show="message.sender_type === 'ai'" 
                                    class="w-8 h-8 bg-blue-100 flex items-center justify-center rounded-full flex-shrink-0 shadow-sm overflow-hidden">
                                    <img src="{{ asset('storage/components/icon-relaxmate.png') }}" alt="RelaxMate" class="w-6 h-6 object-contain">
                                </div>

                                <div class="flex flex-col max-w-[85%]" 
                                    :class="message.sender_type === 'user' ? 'items-end' : 'items-start'">
                                    <div class="p-3 rounded-2xl break-words shadow-md"
                                        :class="message.sender_type === 'user' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border'">
                                        <div class="prose prose-sm max-w-none"
                                            :class="message.sender_type === 'user' ? 'prose-invert' : ''"
                                            x-html="renderMarkdown(message.message_text)">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-1" 
                                        x-text="formatTime(message.created_at)"></span>
                                </div>

                                <!-- Avatar User -->
                                <img x-show="message.sender_type === 'user'" 
                                    src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://placehold.co/40x40/007BFF/FFFFFF?text=' . strtoupper(substr(Auth::user()->name, 0, 1)) }}" 
                                    alt="User" class="w-8 h-8 rounded-full flex-shrink-0 shadow-sm">
                            </div>
                        </div>
                    </template>

                    {{-- Loading Bubble --}}
                    <div x-show="isLoading" class="flex items-start gap-3 justify-start">
                        <div class="w-8 h-8 bg-blue-100 flex items-center justify-center rounded-full flex-shrink-0 shadow-sm overflow-hidden">
                            <img src="{{ asset('storage/components/icon-relaxmate.png') }}" alt="RelaxMate" class="w-6 h-6 object-contain">
                        </div>
                        <div class="bg-white p-3 rounded-2xl rounded-bl-none border flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0s;"></span>
                                <span class="h-2 w-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></span>
                                <span class="h-2 w-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.4s;"></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 italic" x-text="loadingMessage"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Input Pesan -->
            <div class="bg-slate-50 border-t p-4 flex-shrink-0">
                {{-- [PERUBAHAN] Tampilkan form atau pesan read-only --}}
                <div x-show="!isViewingOldConversation">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-4 max-w-3xl mx-auto">
                        <textarea x-model="newMessage"
                                  @keydown.enter.exact.prevent="sendMessage()"
                                  :disabled="isLoading" 
                                  rows="1" 
                                  class="w-full p-3 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition resize-none" 
                                  placeholder="Ketik pesan Anda di sini..."></textarea>
                        <button type="submit" 
                                :disabled="isLoading || newMessage.trim() === ''"
                                class="bg-blue-600 text-white rounded-full w-12 h-12 flex-shrink-0 flex items-center justify-center hover:bg-blue-700 transition"
                                :class="{ 'bg-blue-600 hover:bg-blue-700': newMessage.trim() !== '', 'bg-gray-300 cursor-not-allowed': newMessage.trim() === '' || isLoading }">
                            <i class="fa-solid fa-paper-plane text-xl"></i>
                        </button>
                    </form>
                </div>
                <div x-show="isViewingOldConversation" class="text-center text-sm text-gray-500 italic p-3 bg-slate-200 rounded-lg max-w-3xl mx-auto">
                    <p>Anda hanya dapat melanjutkan percakapan hari ini untuk menjaga konsistensi riwayat.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script>
            function chatComponent() {
                return {
                    isSidebarOpen: localStorage.getItem('sidebarOpen') === 'false' ? false : true,
                    isViewingOldConversation: false,
                    isLoading: true,
                    messages: [],
                    newMessage: '',
                    conversationId: null,
                    conversationGroups: {},
                    loadingMessages: [
                        'Sedang merangkai kata-kata yang menenangkan...',
                        'Sedang menjadi pendengar terbaik Anda...',
                        'Mencari perspektif baru untuk Anda...',
                        'Menyiapkan ruang aman untuk Anda bercerita...',
                        'Mengambil napas dalam-dalam sejenak...'
                    ],
                    loadingMessage: '',
                    promptStarters: [
                        'Beri saya tips cepat untuk mengurangi stres di kantor.',
                        'Bagaimana cara memulai meditasi mindfulness?',
                        'Saya merasa cemas tanpa alasan yang jelas.',
                        'Jelaskan teknik pernapasan untuk menenangkan diri.'
                    ],

                    init() {
                        marked.setOptions({
                            breaks: true,
                        });
                        this.fetchConversationGroups();
                        this.loadTodaysConversation();
                        this.$watch('messages', () => this.scrollToBottom());
                    },

                    toggleSidebar() {
                        this.isSidebarOpen = !this.isSidebarOpen;
                        localStorage.setItem('sidebarOpen', this.isSidebarOpen);
                    },
                    
                    async loadTodaysConversation() {
                        this.isLoading = true;
                        this.isViewingOldConversation = false;
                        try {
                            const response = await fetch(`{{ route('relaxmate.todays') }}`);
                            if (!response.ok) throw new Error('Gagal memuat percakapan hari ini.');
                            const data = await response.json();

                            if (data.conversation_id && data.messages.length > 0) {
                                this.conversationId = data.conversation_id;
                                localStorage.setItem('relaxmate_conversation_id', this.conversationId);
                                this.messages = data.messages;
                            } else {
                                this.conversationId = null;
                                localStorage.removeItem('relaxmate_conversation_id');
                                this.messages = [];
                            }
                        } catch (error) {
                            this.messages = [];
                            console.error(error);
                        } finally {
                            this.isLoading = false;
                        }
                    },
                    
                    async loadConversation(convId, date) {
                        if (this.conversationId === convId && !this.isViewingOldConversation) return;

                        const today = new Date().toISOString().slice(0, 10);
                        
                        if (date === today) {
                            await this.loadTodaysConversation();
                            if (window.innerWidth < 1024) this.isSidebarOpen = false;
                            return;
                        }

                        this.isLoading = true;
                        this.messages = [];
                        this.conversationId = convId;
                        this.isViewingOldConversation = true;
                        
                        try {
                            const response = await fetch(`{{ route('relaxmate.history') }}?conversation_id=${convId}`);
                            if (!response.ok) throw new Error('Gagal memuat riwayat.');
                            this.messages = await response.json();
                        } catch (error) {
                            this.messages = [this.createErrorMessage(error.message)];
                        } finally {
                            this.isLoading = false;
                            if (window.innerWidth < 1024) this.isSidebarOpen = false;
                        }
                    },

                    startWithPrompt(prompt) {
                        this.newMessage = prompt;
                        this.sendMessage();
                    },

                    async sendMessage() {
                        const userMessageText = this.newMessage.trim();
                        if (userMessageText === '' || this.isLoading) return;

                        this.loadingMessage = this.loadingMessages[Math.floor(Math.random() * this.loadingMessages.length)];

                        let isNewConversation = false;
                        if (!this.conversationId) {
                            isNewConversation = true;
                            this.conversationId = `conv_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                            localStorage.setItem('relaxmate_conversation_id', this.conversationId);
                            this.messages = [];
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
                                    conversation_id: this.conversationId,
                                    is_new: isNewConversation
                                })
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.error || 'Terjadi kesalahan pada server.');
                            }
                            
                            const aiResponse = await response.json();
                            this.messages.push(aiResponse.reply);

                            if (isNewConversation) {
                                // [PERBAIKAN KRITIS] Panggil fetchConversationGroups setelah AI merespons
                                // Ini memastikan judul dari AI (jika ada) bisa diambil
                                await this.fetchConversationGroups();
                            }

                        } catch (error) {
                            console.error('Error sending message:', error);
                            this.messages.pop();
                            this.messages.push(this.createErrorMessage(error.message));
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async fetchConversationGroups() {
                        try {
                            const response = await fetch(`{{ route('relaxmate.groups') }}`);
                            if (!response.ok) throw new Error('Gagal memuat grup percakapan.');
                            this.conversationGroups = await response.json();
                        } catch (error) {
                            console.error(error);
                        }
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
                        return marked.parse(text);
                    },
                    formatTime(timestamp) {
                        if (!timestamp) return '';
                        return new Date(timestamp).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    },
                    shouldShowDateSeparator(index) {
                        if (index === 0) return true;
                        if (!this.messages[index-1]) return false;
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
                    },
                    formatFullDate(timestamp) {
                        if (!timestamp) return '';
                        return new Date(timestamp).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>