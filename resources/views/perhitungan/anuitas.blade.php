<?php
// function hitungPlafonPinjaman($jumlahSetoran, $sukuBungaBulanan, $jangkaWaktu)
// {
//     $plafonPinjaman = $jumlahSetoran / (($sukuBungaBulanan / 12) * (pow(1 + $sukuBungaBulanan / 12, $jangkaWaktu) / (pow(1 + $sukuBungaBulanan / 12, $jangkaWaktu) - 1)));
//     return $plafonPinjaman;
// }

// Gantilah nilai-nilai berikut sesuai dengan nilai yang sesuai dari rumus Anda
$jumlahSetoran = 1200000; // Misalnya, jumlah setoran sebesar $10,000
$sukuBungaBulanan = 0.25; // Misalnya, suku bunga bulanan sebesar 1%
$jangkaWaktu = 36; // Misalnya, jangka waktu pinjaman selama 24 bulan
//Anuitas
$plafonPinjaman = $jumlahSetoran / (($sukuBungaBulanan / 12) * (pow(1 + $sukuBungaBulanan / 12, $jangkaWaktu) / (pow(1 + $sukuBungaBulanan / 12, $jangkaWaktu) - 1)));

//Flat
$plafonPinjamanFlat = ($jumlahSetoran * $jangkaWaktu) / (1 + ($jangkaWaktu * $sukuBungaBulanan) / 12);
// Mengonversi hasil ke IDR dengan menambahkan simbol "IDR"
$plafonPinjamanIDR = number_format($plafonPinjaman, 0, ',', '.') . ' IDR';
$plafonPinjamanIDRFlat = number_format($plafonPinjamanFlat, 0, ',', '.') . ' IDR';
echo "Jumlah Plafon Pinjaman: $plafonPinjamanIDR <br>";

echo "Jumlah Plafon Pinjaman: $plafonPinjamanIDRFlat";
?>
