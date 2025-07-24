document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const wrapper = document.getElementById('wrapper');

    if (menuToggle && wrapper) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            wrapper.classList.toggle('toggled');
        });
    }

    // --- Logika untuk form kuesioner interaktif (lanjut pertanyaan) ---
    // Pastikan elemen-elemen ini ada sebelum mengaksesnya
    const pertanyaanItems = document.querySelectorAll('.pertanyaan-item');
    const submitButtonContainer = document.getElementById('submitButtonContainer');
    const totalQuestions = pertanyaanItems.length;

    // Fungsi untuk menampilkan pertanyaan berikutnya
    function showNextQuestion(currentQuestionIndex) {
        // Sembunyikan pertanyaan saat ini
        if (pertanyaanItems[currentQuestionIndex]) {
            pertanyaanItems[currentQuestionIndex].style.display = 'none';
        }

        const nextQuestionIndex = currentQuestionIndex + 1;

        if (nextQuestionIndex < totalQuestions) {
            // Tampilkan pertanyaan berikutnya
            if (pertanyaanItems[nextQuestionIndex]) {
                pertanyaanItems[nextQuestionIndex].style.display = 'block';
                // Scroll ke atas halaman untuk pertanyaan baru
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else {
            // Jika semua pertanyaan sudah dijawab, tampilkan tombol submit
            if (submitButtonContainer) {
                submitButtonContainer.style.display = 'block';
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); // Scroll ke bawah untuk tombol submit
            }
        }
    }

    // Event listener untuk radio button
    document.querySelectorAll('.question-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const currentQuestionIndex = parseInt(this.dataset.currentQuestion);
            showNextQuestion(currentQuestionIndex);
        });
    });

    // Event listener untuk tombol "Lanjut" pada pertanyaan isian
    document.querySelectorAll('.lanjut-button').forEach(button => {
        button.addEventListener('click', function() {
            const currentQuestionIndex = parseInt(this.dataset.currentQuestion);
            const textarea = pertanyaanItems[currentQuestionIndex].querySelector('textarea');
            // Cek apakah isian kosong sebelum melanjutkan
            if (textarea && textarea.value.trim() === '') {
                alert('Mohon isi jawaban Anda sebelum melanjutkan.');
                return; // Jangan lanjutkan jika kosong
            }
            showNextQuestion(currentQuestionIndex);
        });
    });

    // Inisialisasi tampilan kuesioner saat dimuat
    if (totalQuestions > 0) {
        // Sembunyikan semua kecuali pertanyaan pertama
        for (let i = 0; i < totalQuestions; i++) {
            if (pertanyaanItems[i]) {
                pertanyaanItems[i].style.display = 'none';
            }
        }
        if (pertanyaanItems[0]) {
            pertanyaanItems[0].style.display = 'block';
        }
    }
    // Pastikan tombol submit tersembunyi di awal
    if (submitButtonContainer) {
        submitButtonContainer.style.display = 'none';
    }
});