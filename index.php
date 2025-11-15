<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Data Alumni</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Data Alumni Sekolah</h2>
    <a href="tambah.php">+ Tambah Data</a>
    <table>
        <tr>
            <th>Nama</th>
            <th>NIK</th>
            <th>NISN</th>
            <th>Tempat Lahir</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Tahun Lulus</th>
            <th>Jurusan</th>
            <th>ubah</th>
        </tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM alumni");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
        <td>{$row['nama']}</td>
        <td>{$row['nik']}</td>
        <td>{$row['nisn']}</td>
        <td>{$row['tempat_lahir']}</td>
        <td>{$row['tanggal_lahir']}</td>
        <td>{$row['alamat']}</td>
        <td>{$row['tahun_lulus']}</td>
        <td>{$row['jurusan']}</td>
        <td>
          <a href='edit.php?id={$row['id']}'>Edit</a> |
          <a href='hapus.php?id={$row['id']}' onclick=\"return confirm('Yakin ingin hapus?')\">Hapus</a>
        </td>
      </tr>";
        }
        ?>
    </table>
</body>

</html>