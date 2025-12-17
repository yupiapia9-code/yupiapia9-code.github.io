<?php
// projekbigdata/tambah_barang.php
include 'koneksi.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $koneksi->real_escape_string($_POST['nama_barang']);
    $kategori = $koneksi->real_escape_string($_POST['kategori']);
    $stok = (int)$_POST['stok'];
    $harga = (float)$_POST['harga'];

    $query = "INSERT INTO barang (nama_barang, kategori, stok, harga) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssid", $nama, $kategori, $stok, $harga);

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Data berhasil ditambahkan! Kembali ke <a href="../index.php">Data Barang</a></div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal menambahkan data: ' . $stmt->error . '</div>';
    }

    $stmt->close();
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">← Kembali ke Data Barang</a>
    <div class="card">
        <div class="card-header">
            <h3>➕ Tambah Data Barang Baru</h3>
        </div>
        <div class="card-body">
            <?php echo $message; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select class="form-select" id="kategori" name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Pecah Belah">Pecah Belah</option>
                        <option value="ATK">ATK</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" required min="1">
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga (Rp)</label>
                    <input type="number" step="0.01" class="form-control" id="harga" name="harga" required min="1000">
                </div>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>