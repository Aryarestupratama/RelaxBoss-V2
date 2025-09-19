import "./bootstrap";

import Alpine from "alpinejs";

// Impor lain yang Anda butuhkan
import "trix";
import AOS from "aos";
import Typed from "typed.js";

// Inisialisasi AOS
AOS.init({
    duration: 1000,
    once: true,
});

// Jadikan variabel global jika diperlukan oleh bagian lain dari aplikasi Anda
window.Typed = Typed;
window.Alpine = Alpine;

// Mulai Alpine
Alpine.start();
