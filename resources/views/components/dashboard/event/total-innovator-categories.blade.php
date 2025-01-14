<div class="card p-3 mt-3">
    <h5 class="text-center">Akumulasi Total Kategori yang Dipilih Inovator </h5>
    <div class="mt-3 row" id="cardContainer">
        <!-- Kartu-kartu data akan dimasukkan di sini -->
    </div>
</div>

<script type="module">
    const chartDataTotalInnovatorCategories = @json($chartData);
    const event_name = @json($event_name);
    window.chartDataTotalInnovatorCategories = chartDataTotalInnovatorCategories; // Store chart data globally
    window.event_name = event_name; // Store event name globally

    // Daftar warna yang akan dipakai untuk kategori secara dinamis
    const categoryColors = [
        "bg-success", // Hijau
        "bg-secondary",
        "bg-info",   // Biru
        "bg-warning", // Kuning
        "bg-primary", // Biru Tua
        "bg-danger",  // Merah
    ];

    // Menampilkan data dalam bentuk card
    document.addEventListener("DOMContentLoaded", function () {
        const cardContainer = document.getElementById('cardContainer');

        cardContainer.innerHTML = chartDataTotalInnovatorCategories.labels.map((label, index) => {
            // Menentukan warna secara dinamis berdasarkan urutan kategori
            const colorIndex = index % categoryColors.length; // Menghindari warna berlebih
            const categoryColor = categoryColors[colorIndex];

            return `
                <div class="col-12 col-md-3 mb-3">
                    <div class="card mb-3 custom-card ${categoryColor}">
                        <div class="card-body">
                            <h6 class="card-title">${label}</h6>
                            <p class="card-text"> <strong>${chartDataTotalInnovatorCategories.data[index]}</strong></p>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    });
</script>

<style>
    /* Styling card menjadi persegi dan konsisten */
    .custom-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
        margin: 0 auto; /* Agar card berada di tengah */
        height: 130px; /* Set tinggi tetap untuk kartu */
    }

    .custom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .custom-card .card-body {
        padding: 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        height: 100%; /* Menyesuaikan tinggi card */
    }

    .custom-card .card-title {
        font-size: 1rem;
        font-weight: bold;
        color: #ffffff; /* Warna teks hitam untuk judul */
        margin-bottom: 10px;
    }

    .custom-card .card-text {
        font-size: 0.9rem;
        color: #ffffff; /* Warna teks hitam untuk isi */
    }

    .custom-card .card-text strong {
        color: #ffffff;
        font-size: 2.7rem;  /* Ukuran font yang lebih besar */
        /* Warna teks hitam untuk strong */
    }

    /* Warna kategori */
    .bg-info {
        background-color: #17a2b8; /* Biru */
    }

    .bg-success {
        background-color: #28a745; /* Hijau */
    }

    .bg-warning {
        background-color: #ffc107; /* Kuning */
    }

    .bg-primary {
        background-color: #007bff; /* Biru Tua */
    }

    .bg-danger {
        background-color: #dc3545; /* Merah */
    }

    /* Responsive */
    @media (max-width: 768px) {
        .custom-card {
            width: 100%; /* Mengatur lebar kartu di perangkat mobile */
            height: 150px;
        }

        h5 {
            font-size: 1.3rem;
        }
    }
</style>
