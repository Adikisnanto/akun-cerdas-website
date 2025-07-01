document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');

    // Ubah background navbar saat scroll
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-white', 'shadow');
            navbar.classList.remove('bg-light');
        } else {
            navbar.classList.remove('bg-white', 'shadow');
            navbar.classList.add('bg-light');
        }
    });

    // Fungsi untuk membuat kartu Trello baru (sekarang memanggil PHP backend)
    const createTrelloCard = (name, desc) => {
        // URL endpoint PHP yang akan memproses pembuatan kartu Trello
        // Pastikan path ini benar relatif terhadap root web server Anda
        fetch('create_trello_card.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${encodeURIComponent(name)}&desc=${encodeURIComponent(desc)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Kartu berhasil dibuat:', data.data);
            } else {
                console.error('Gagal membuat kartu Trello:', data.message, data.error);
            }
        })
        .catch(error => {
            console.error('Terjadi kesalahan saat menghubungi server:', error);
        });
    };

    // Contoh membuat kartu Trello (akan memanggil PHP)
    createTrelloCard('Kartu Baru dari Frontend', 'Deskripsi untuk kartu baru dari JS via PHP.');
});