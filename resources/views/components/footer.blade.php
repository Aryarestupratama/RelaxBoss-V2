<footer class="bg-slate-900 text-slate-300">
    <div class="container mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            
            <!-- Kolom 1: Branding & Kontak -->
            <div class="space-y-4">
                <a href="{{ route('welcome') }}" class="text-3xl font-bold text-white">
                    Relax<span class="bg-gradient-to-r from-[#007BFF] to-cyan-400 bg-clip-text text-transparent">Boss</span>
                </a>
                <p class="text-slate-400 leading-relaxed">
                    Platform manajemen stres inovatif untuk para profesional meraih keseimbangan dan produktivitas optimal.
                </p>
                <div class="flex space-x-3 pt-2">
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:bg-[#007BFF] hover:text-white transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:bg-[#007BFF] hover:text-white transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:bg-[#007BFF] hover:text-white transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Kolom 2: Tautan Navigasi -->
            <div>
                <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Tautan Cepat</h3>
                <ul class="mt-4 space-y-3">
                    <li><a href="#beranda" class="text-slate-400 hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="#fitur" class="text-slate-400 hover:text-white transition-colors">Fitur</a></li>
                    <li><a href="#review" class="text-slate-400 hover:text-white transition-colors">Testimoni</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Pusat Bantuan</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Legal & Perusahaan -->
            <div>
                <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Perusahaan</h3>
                <ul class="mt-4 space-y-3">
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Formulir Kontak -->
            <div>
                <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Hubungi Kami</h3>
                <form action="#" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="email" name="email" required placeholder="Email Anda" class="w-full px-4 py-2.5 rounded-md bg-slate-800 text-white placeholder-slate-500 border border-slate-700 focus:outline-none focus:ring-2 focus:ring-[#007BFF] transition">
                    <textarea name="message" rows="3" required placeholder="Pesan Anda..." class="w-full px-4 py-2.5 rounded-md bg-slate-800 text-white placeholder-slate-500 border border-slate-700 focus:outline-none focus:ring-2 focus:ring-[#007BFF] transition"></textarea>
                    <button type="submit" class="w-full bg-[#007BFF] hover:bg-blue-600 text-white font-bold py-2.5 rounded-md transition-colors shadow-lg shadow-blue-500/20">
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>

        <!-- Pemisah dan Copyright -->
        <div class="mt-16 border-t border-slate-800 pt-8 text-center">
            
            {{-- [DIUBAH] Bagian Tim Pembuat --}}
            <div class="mb-8 space-y-6">
                <div>
                    <h4 class="text-sm font-semibold text-slate-400 tracking-wider uppercase">Tim Pendiri</h4>
                    <div class="mt-3 flex flex-wrap justify-center gap-x-6 gap-y-2 text-slate-300">
                        <span>Arya Restu Pratama</span>
                        <span>Rezky Budiawan</span>
                        <span>Krisna Septiawan</span>
                        <span>Muhammad Rifqi Abdillah</span>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-400 tracking-wider uppercase">Pengembang v2.0</h4>
                    <div class="mt-3 flex flex-wrap justify-center gap-x-6 gap-y-2 text-slate-300">
                        <span>Arya Restu Pratama</span>
                        <span>Adika Faris Murtadha Hidayat</span>
                        <span>Felicia Ivana</span>
                    </div>
                </div>
            </div>

            <p class="text-sm text-slate-500">&copy; {{ date('Y') }} RelaxBoss. Seluruh hak cipta dilindungi.</p>
        </div>
    </div>
</footer>
