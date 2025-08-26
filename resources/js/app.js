import "./bootstrap";

import Alpine from "alpinejs";

// --- MULAI PENAMBAHAN KODE ---
import "trix";
// Impor AOS dan Typed.js
import AOS from "aos";
import Typed from "typed.js";

// Inisialisasi AOS agar aktif di seluruh aplikasi
AOS.init({
    duration: 1000, // Durasi animasi dalam milidetik
    once: true, // Apakah animasi hanya terjadi sekali
});

// Membuat Typed.js tersedia secara global jika diperlukan di file Blade
// Ini adalah cara mudah agar script di Blade tetap berfungsi
window.Typed = Typed;

// --- SELESAI PENAMBAHAN KODE ---

window.Alpine = Alpine;

Alpine.start();
