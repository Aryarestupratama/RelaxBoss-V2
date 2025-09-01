<x-app-layout>
    <x-slot name="title">
        AI RelaxMate
    </x-slot>

    {{-- [PERUBAHAN] Struktur utama diubah untuk mendukung sidebar persisten di desktop --}}
    <div x-data="chatComponent()" x-cloak class="relative h-[calc(100vh-129px)] bg-slate-100 flex justify-center overflow-hidden">
        
        <aside x-show="isSidebarOpen" 
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-300"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="absolute lg:relative inset-y-0 left-0 w-80 bg-white border-r z-20 flex flex-col flex-shrink-0"
               @click.outside="if (window.innerWidth < 1024) isSidebarOpen = false">
            
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-lg text-gray-800">Riwayat Percakapan</h2>
                <button @click="isSidebarOpen = false" class="text-gray-500 hover:text-gray-800">
                    <i class="fa-solid fa-xmark fa-lg"></i>
                </button>
            </div>
            <div class="flex-grow overflow-y-auto">
                <ul class="p-2">
                    <template x-for="(group, date) in conversationGroups" :key="date">
                        <li>
                            <h3 class="px-3 py-2 text-xs font-bold text-gray-400 uppercase" x-text="formatDateSeparator(date)"></h3>
                            <ul>
                                <template x-for="conversation in group" :key="conversation.conversation_id">
                                    <li class="p-1">
                                        <button @click="loadConversation(conversation.conversation_id)" 
                                                :class="{ 'bg-blue-100 text-blue-700': conversation.conversation_id === conversationId }"
                                                class="w-full text-left px-3 py-2 rounded-md hover:bg-slate-100 transition truncate">
                                            <span class="text-sm" x-text="conversation.title || 'Percakapan ' + formatTime(conversation.created_at)"></span>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </li>
                    </template>
                </ul>
            </div>
             <div class="p-4 border-t">
                <button @click="startNewConversation()" class="w-full flex items-center justify-center gap-2 py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus"></i>
                    Percakapan Baru
                </button>
            </div>
        </aside>

        {{-- Overlay ini hanya aktif di layar mobile/tablet --}}
        <div x-show="isSidebarOpen" @click="isSidebarOpen = false" class="absolute inset-0 bg-black/30 z-10 lg:hidden" x-transition.opacity></div>

        {{-- [PERUBAHAN] Wrapper chat sekarang menjadi bagian dari flex layout di desktop --}}
        <div class="w-full max-w-4xl h-full flex flex-col bg-white shadow-xl flex-grow">
            <!-- Header Chat -->
            <div class="border-b p-4 flex items-center justify-between flex-shrink-0 bg-slate-50">
                <div class="flex items-center gap-4">
                    {{-- [PERUBAHAN] Tombol ini sekarang ada dua: satu untuk mobile, satu untuk desktop saat sidebar tertutup --}}
                    <button @click="isSidebarOpen = true" class="text-gray-500 hover:text-blue-600 lg:hidden">
                         <i class="fa-solid fa-bars fa-lg"></i>
                    </button>
                    <button x-show="!isSidebarOpen" @click="isSidebarOpen = true" class="hidden lg:block text-gray-500 hover:text-blue-600">
                        <i class="fa-solid fa-bars fa-lg"></i>
                    </button>
                    <div class="relative">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full overflow-hidden">
                            <img src="{{ asset('storage/components/icon-relaxmate.png') }}" 
                                alt="RelaxMate" 
                                class="w-full h-full object-cover">
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
            <div class="flex-grow p-6 overflow-y-auto flex flex-col" x-ref="chatArea">
                <div class="flex-grow"></div> 
                <div class="space-y-6">
                    <template x-for="(message, index) in messages" :key="message.id || index">
                        <div class="space-y-4">
                            <!-- Penanda Tanggal -->
                            <template x-if="shouldShowDateSeparator(index)">
                                <div class="flex justify-center my-2">
                                    <span class="text-xs text-gray-500 bg-gray-200 px-3 py-1 rounded-full" x-text="formatDateSeparator(message.created_at)"></span>
                                </div>
                            </template>
                            <!-- Bubble Pesan -->
                            <div class="flex items-end gap-3" :class="message.sender_type === 'user' ? 'justify-end' : 'justify-start'">
                                <!-- Avatar AI -->
                               <div x-show="message.sender_type === 'ai'" 
                                    class="w-8 h-8 bg-blue-500 flex items-center justify-center rounded-full flex-shrink-0 shadow-sm overflow-hidden">
                                    <img src="{{ asset('storage/components/icon-relaxmate.png') }}" 
                                        alt="RelaxMate" 
                                        class="w-6 h-6 object-contain">
                                </div>

                                <div class="flex flex-col max-w-[85%]" :class="message.sender_type === 'user' ? 'items-end' : 'items-start'">
                                    <div class="p-3 rounded-2xl break-words shadow-md" :class="message.sender_type === 'user' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border'">
                                        <p class="text-sm" x-html="renderMarkdown(message.message_text)"></p>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-1" x-text="formatTime(message.created_at)"></span>
                                </div>
                                <!-- Avatar Pengguna -->
                                <img x-show="message.sender_type === 'user'" src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://placehold.co/40x40/007BFF/FFFFFF?text=' . strtoupper(substr(Auth::user()->name, 0, 1)) }}" alt="User" class="w-8 h-8 rounded-full flex-shrink-0 shadow-sm">
                            </div>
                        </div>
                    </template>
                    
                    <!-- Indikator Loading dengan pesan dinamis -->
                    <div x-show="isLoading" class="flex items-start gap-3 justify-start">
                        <div class="w-8 h-8 bg-blue-500 text-white flex items-center justify-center rounded-full flex-shrink-0 shadow-sm"><i class="fa-solid fa-robot"></i></div>
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
                <form @submit.prevent="sendMessage()" class="flex items-center gap-4">
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
        </div>
    </div>

    @push('scripts')
        <script>
            function chatComponent() {
                return {
                    isSidebarOpen: window.innerWidth >= 1024,
                    // ... sisa JavaScript tidak berubah ...
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

                    init() {
                        this.fetchConversationGroups();
                        this.loadLatestConversation();
                        this.$watch('messages', () => this.scrollToBottom());
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

                    async loadLatestConversation() {
                        this.isLoading = true;
                        try {
                            const response = await fetch(`{{ route('relaxmate.latest') }}`);
                            if (!response.ok) throw new Error('Gagal memuat percakapan terakhir.');
                            const data = await response.json();
                            if (data.conversation_id) {
                                this.conversationId = data.conversation_id;
                                localStorage.setItem('relaxmate_conversation_id', this.conversationId);
                                this.messages = data.messages.length > 0 ? data.messages : [this.createWelcomeMessage()];
                            } else {
                                this.startNewConversation();
                            }
                        } catch (error) {
                            this.messages = [this.createWelcomeMessage(), this.createErrorMessage(error.message)];
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async loadConversation(convId) {
                        if (this.conversationId === convId) {
                            if (window.innerWidth < 1024) this.isSidebarOpen = false;
                            return;
                        }
                        this.isLoading = true;
                        this.messages = [];
                        this.conversationId = convId;
                        localStorage.setItem('relaxmate_conversation_id', convId);
                        
                        try {
                            const response = await fetch(`{{ route('relaxmate.history') }}?conversation_id=${convId}`);
                            if (!response.ok) throw new Error('Gagal memuat riwayat.');
                            const data = await response.json();
                            this.messages = data.length > 0 ? data : [this.createWelcomeMessage()];
                        } catch (error) {
                            this.messages = [this.createErrorMessage(error.message)];
                        } finally {
                            this.isLoading = false;
                            if (window.innerWidth < 1024) this.isSidebarOpen = false;
                        }
                    },

                    startNewConversation() {
                        this.conversationId = null;
                        localStorage.removeItem('relaxmate_conversation_id');
                        this.messages = [this.createWelcomeMessage()];
                        if (window.innerWidth < 1024) this.isSidebarOpen = false;
                    },
                    
                    async sendMessage() {
                        const userMessageText = this.newMessage.trim();
                        if (userMessageText === '' || this.isLoading) return;

                        this.loadingMessage = this.loadingMessages[Math.floor(Math.random() * this.loadingMessages.length)];

                        if (!this.conversationId) {
                            this.conversationId = `conv_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                            localStorage.setItem('relaxmate_conversation_id', this.conversationId);
                            this.messages = []; // Hapus pesan selamat datang
                        }

                        this.messages.push({
                            id: `user_${Date.now()}`,
                            sender_type: 'user',
                            message_text: userMessageText,
                            created_at: new Date().toISOString()
                        });
                        
                        const currentMessage = this.newMessage;
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
                            this.messages.push(aiResponse.reply); // Server mengirim balasan lengkap

                            if (this.conversationId === localStorage.getItem('relaxmate_conversation_id')) {
                            this.fetchConversationGroups(); // Perbarui sidebar
                            }

                        } catch (error) {
                            console.error('Error sending message:', error);
                            this.messages.push(this.createErrorMessage(error.message));
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    createWelcomeMessage() {
                        return { id: 'welcome', sender_type: 'ai', message_text: 'Halo! Saya RelaxMate. Ada yang bisa saya bantu hari ini?', created_at: new Date().toISOString() };
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
                        // Simple markdown for bold
                        return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
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
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>