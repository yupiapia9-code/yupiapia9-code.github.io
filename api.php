<?php
// projekbigdata/api.php
include 'koneksi.php';

header('Content-Type: application/json');

$query = "SELECT id, nama_barang, kategori, stok, harga FROM barang";
$result = $koneksi->query($query);

$response = [
    'status' => 'success',
    'total_records' => 0,
    'data' => []
];

if ($result && $result->num_rows > 0) {
    $data_barang = [];
    while ($row = $result->fetch_assoc()) {
        // Konversi tipe data untuk output JSON yang rapi
        $row['harga'] = (float)$row['harga']; 
        $row['stok'] = (int)$row['stok'];
        $data_barang[] = $row;
    }
    
    $response['total_records'] = count($data_barang);
    $response['data'] = $data_barang;
}

echo json_encode($response, JSON_PRETTY_PRINT);
$koneksi->close();
?>