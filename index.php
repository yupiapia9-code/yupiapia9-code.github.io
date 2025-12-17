<?php
// projekbigdata/index.php
include 'koneksi.php';

// --- Konfigurasi Paging & Pencarian ---
$data_per_halaman = 10;
$halaman_aktif = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_aktif - 1) * $data_per_halaman;
$keyword = isset($_GET['cari']) ? $koneksi->real_escape_string($_GET['cari']) : '';

$where_clause = '';
if (!empty($keyword)) {
    $where_clause = " WHERE nama_barang LIKE '%$keyword%' OR kategori LIKE '%$keyword%'";
}

// 1. Total Barang (Informasi Total)
$total_query = $koneksi->query("SELECT COUNT(id) AS total FROM barang" . $where_clause);
$total_data = $total_query->fetch_assoc()['total'];
$total_halaman = ceil($total_data / $data_per_halaman);

// 2. Query Utama (Paging dan Pencarian)
$data_query = $koneksi->query("SELECT * FROM barang" . $where_clause . " LIMIT $data_per_halaman OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Big Data PT ABC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">📊 SISTEM BIG DATA BARANG PT TAQWA MULIA</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Record Barang</div>
                <div class="card-body">
                    <h1 class="card-title"><?php echo number_format($total_data); ?></h1>
                    <p class="card-text">Total semua data yang ditemukan.</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Grafik Jumlah Stok per Kategori</div>
                <div class="card-body"><canvas id="stockChart"></canvas></div>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <form action="index.php" method="GET" class="input-group">
                <input type="text" name="cari" class="form-control" placeholder="Cari Nama atau Kategori..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                <a href="index.php" class="btn btn-outline-danger">Reset</a>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <a href="views/tambah_barang.php" class="btn btn-success">➕ Tambah Data Barang (Poin 6)</a>
        </div>
    </div>
    
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($total_data > 0) {
                $no = $offset + 1;
                while ($row = $data_query->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";
                    echo "<td>" . number_format($row['stok']) . "</td>";
                    echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Data tidak ditemukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($total_halaman > 1) { ?>
                <li class="page-item <?php echo ($halaman_aktif <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?halaman=<?php echo $halaman_aktif - 1; ?><?php echo !empty($keyword) ? '&cari=' . $keyword : ''; ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_halaman; $i++) { 
                    if ($i == $halaman_aktif || $i == 1 || $i == $total_halaman || ($i >= $halaman_aktif - 2 && $i <= $halaman_aktif + 2)) {
                        $active = ($i == $halaman_aktif) ? 'active' : '';
                        echo "<li class='page-item $active'><a class='page-link' href='?halaman=$i" . (!empty($keyword) ? '&cari=' . $keyword : '') . "'>$i</a></li>";
                    }
                } ?>
                <li class="page-item <?php echo ($halaman_aktif >= $total_halaman) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?halaman=<?php echo $halaman_aktif + 1; ?><?php echo !empty($keyword) ? '&cari=' . $keyword : ''; ?>">Next</a>
                </li>
            <?php } ?>
        </ul>
        <p class="text-center">Halaman <?php echo $halaman_aktif; ?> dari <?php echo $total_halaman; ?>.</p>
    </nav>
</div>

<script>
    <?php
    // --- Data untuk Grafik Rekap Kategori ---
    $chart_data = [];
    $kategori_query = $koneksi->query("SELECT kategori, SUM(stok) as total_stok FROM barang GROUP BY kategori");
    while($row = $kategori_query->fetch_assoc()) {
        $chart_data[] = [
            'kategori' => $row['kategori'],
            'stok' => (int)$row['total_stok']
        ];
    }
    $chart_labels = array_column($chart_data, 'kategori');
    $chart_values = array_column($chart_data, 'stok');
    ?>

    const ctx = document.getElementById('stockChart').getContext('2d');
    const stockChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Total Stok Barang',
                data: <?php echo json_encode($chart_values); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
