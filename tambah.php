<?php
session_start();
include 'koneksi.php';

// Cek jika form disubmit (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $nama_lengkap = $_POST['nama_lengkap'];
    $tahun_lulus = $_POST['tahun_lulus'];
    $jurusan = $_POST['jurusan'];
    $pekerjaan = $_POST['pekerjaan'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    // Siapkan SQL query menggunakan prepared statements untuk keamanan
    $sql = "INSERT INTO alumni (Nama_Lengkap, Tahun_Lulus, Jurusan, Pekerjaan_Saat_Ini, Nomor_Telepon, Email, Alamat) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameter ke query
    // "sisssss" berarti:
    // s = string (Nama_Lengkap)
    // i = integer (Tahun_Lulus) - Sesuai instruksi (Int(4))
    // s = string (Jurusan)
    // s = string (Pekerjaan_Saat_Ini)
    // s = string (Nomor_Telepon)
    // s = string (Email)
    // s = string (Alamat)
    $stmt->bind_param("sisssss", $nama_lengkap, $tahun_lulus, $jurusan, $pekerjaan, $telepon, $email, $alamat);

    // Eksekusi query
    if ($stmt->execute()) {
        // Set pesan sukses ke session
        $_SESSION['message'] = "Data alumni berhasil ditambahkan!";
        $_SESSION['msg_type'] = "alert-success";
    } else {
        // Cek jika error adalah karena email duplikat (UNIQUE)
        if ($conn->errno == 1062) {
            $_SESSION['message'] = "Gagal menambahkan: Alamat Email '{$email}' sudah terdaftar.";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        $_SESSION['msg_type'] = "alert-error";
    }

    // Tutup statement
    $stmt->close();
    $conn->close();

    // Redirect kembali ke halaman index.php
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Alumni</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Tambah Data Alumni Baru</h2>

        <!-- Div untuk menampilkan error validasi JS -->
        <div id="error-message"></div>

        <form action="tambah.php" method="POST" id="form-alumni" onsubmit="return validasiForm()">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control">
            </div>
            <div class="form-group">
                <label for="tahun_lulus">Tahun Lulus (Contoh: 2024)</label>
                <input type="number" id="tahun_lulus" name="tahun_lulus" class="form-control" min="1950" max="<?php echo date('Y') + 1; ?>">
            </div>
            <div class="form-group">
                <label for="jurusan">Jurusan</label>
                <input type="text" id="jurusan" name="jurusan" class="form-control">
            </div>
            <div class="form-group">
                <label for="pekerjaan">Pekerjaan Saat Ini</label>
                <input type="text" id="pekerjaan" name="pekerjaan" class="form-control">
            </div>
            <div class="form-group">
                <label for="telepon">Nomor Telepon</label>
                <input type="tel" id="telepon" name="telepon" class="form-control">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="form-control"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Data</button>
            <a href="index.php" class="btn" style="background-color: #7f8c8d; color: white;">Batal</a>
        </form>
    </div>

    <script>
        /**
         * Fungsi untuk validasi form client-side
         * Sesuai instruksi: "memastikan kolom wajib terisi"
         */
        function validasiForm() {
            // Ambil semua field yang wajib diisi
            var nama = document.getElementById('nama_lengkap').value;
            var tahun = document.getElementById('tahun_lulus').value;
            var jurusan = document.getElementById('jurusan').value;
            var pekerjaan = document.getElementById('pekerjaan').value;
            var telepon = document.getElementById('telepon').value;
            var email = document.getElementById('email').value;
            var alamat = document.getElementById('alamat').value;

            var errorMessage = document.getElementById('error-message');
            var errors = [];

            // Cek satu per satu
            if (nama.trim() === "") {
                errors.push("Nama Lengkap wajib diisi.");
            }
            if (tahun.trim() === "") {
                errors.push("Tahun Lulus wajib diisi.");
            }
            if (jurusan.trim() === "") {
                errors.push("Jurusan wajib diisi.");
            }
            if (pekerjaan.trim() === "") {
                errors.push("Pekerjaan Saat Ini wajib diisi.");
            }
            if (telepon.trim() === "") {
                errors.push("Nomor Telepon wajib diisi.");
            }
            if (email.trim() === "") {
                errors.push("Email wajib diisi.");
            } else {
                // Validasi format email sederhana
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    errors.push("Format Email tidak valid.");
                }
            }
            if (alamat.trim() === "") {
                errors.push("Alamat wajib diisi.");
            }

            // Jika ada error
            if (errors.length > 0) {
                // Tampilkan semua error
                errorMessage.innerHTML = errors.join('<br>');
                errorMessage.style.display = 'block';
                
                // Mencegah form disubmit
                return false;
            }

            // Jika tidak ada error
            errorMessage.style.display = 'none';
            // Izinkan form disubmit
            return true;
        }
    </script>

</body>
</html>
