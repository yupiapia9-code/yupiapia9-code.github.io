<?php
// projekbigdata/generate_data.php
include 'koneksi.php';

$kategori_list = ['Elektronik', 'Pecah Belah', 'ATK'];
$nama_awal = ['Monitor', 'Keyboard', 'Piring', 'Gelas', 'Buku', 'Pulpen', 'Laptop', 'Vas', 'Pensil', 'Printer', 'Mouse', 'Meja'];

$jumlah_data = 1000;
$total_inserted = 0;

echo "<h2>Memulai Generasi $jumlah_data Data Dummy...</h2>";

for ($i = 1; $i <= $jumlah_data; $i++) {
    $kategori_val = $kategori_list[array_rand($kategori_list)];
    $nama_barang = $nama_awal[array_rand($nama_awal)] . " ABC ID-" . str_pad($i, 4, '0', STR_PAD_LEFT);
    $stok = rand(10, 500);
    $harga = rand(500000, 5000000); 
    
    $query = "INSERT INTO barang (nama_barang, kategori, stok, harga) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssid", $nama_barang, $kategori_val, $stok, $harga);

    if ($stmt->execute()) {
        $total_inserted++;
    } else {
        echo "<p style='color:red;'>Error saat insert data ke-$i: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo "<hr><h2>✅ Proses Selesai!</h2>";
echo "<b>$total_inserted dari $jumlah_data data berhasil di-insert.</b>";
$koneksi->close();
?>