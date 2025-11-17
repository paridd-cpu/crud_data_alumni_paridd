<?php
session_start();
include 'koneksi.php';

$id = 0;
// Inisialisasi variabel untuk data lama
$nama_lengkap = "";
$tahun_lulus = "";
$jurusan = "";
$pekerjaan = "";
$telepon = "";
$email = "";
$alamat = "";

// Ambil ID dari URL (GET request)
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data lama dari database berdasarkan ID
    $sql = "SELECT * FROM alumni WHERE Id_Alumni = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $data = $result->fetch_assoc();
        $nama_lengkap = $data['Nama_Lengkap'];
        $tahun_lulus = $data['Tahun_Lulus'];
        $jurusan = $data['Jurusan'];
        $pekerjaan = $data['Pekerjaan_Saat_Ini'];
        $telepon = $data['Nomor_Telepon'];
        $email = $data['Email'];
        $alamat = $data['Alamat'];
    } else {
        // Jika ID tidak ditemukan
        $_SESSION['message'] = "Data alumni tidak ditemukan.";
        $_SESSION['msg_type'] = "alert-error";
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}

// Cek jika form disubmit (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $id = $_POST['id_alumni']; // Ambil ID dari hidden input
    $nama_lengkap = $_POST['nama_lengkap'];
    $tahun_lulus = $_POST['tahun_lulus'];
    $jurusan = $_POST['jurusan'];
    $pekerjaan = $_POST['pekerjaan'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    // Siapkan query UPDATE
    $sql = "UPDATE alumni SET 
                Nama_Lengkap = ?, 
                Tahun_Lulus = ?, 
                Jurusan = ?, 
                Pekerjaan_Saat_Ini = ?, 
                Nomor_Telepon = ?, 
                Email = ?, 
                Alamat = ? 
            WHERE Id_Alumni = ?";
            
    $stmt = $conn->prepare($sql);
    // Bind parameter (7 string, 1 integer, lalu 1 integer untuk ID)
    $stmt->bind_param("sisssssi", $nama_lengkap, $tahun_lulus, $jurusan, $pekerjaan, $telepon, $email, $alamat, $id);

    // Eksekusi query
    if ($stmt->execute()) {
        $_SESSION['message'] = "Data alumni berhasil diperbarui!";
        $_SESSION['msg_type'] = "alert-success";
    } else {
        if ($conn->errno == 1062) {
            $_SESSION['message'] = "Gagal memperbarui: Alamat Email '{$email}' sudah terdaftar.";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        $_SESSION['msg_type'] = "alert-error";
    }

    $stmt->close();
    $conn->close();

    // Redirect kembali ke index.php
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Alumni</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Edit Data Alumni</h2>

        <!-- Div untuk menampilkan error validasi JS -->
        <div id="error-message"></div>

        <form action="edit.php" method="POST" id="form-alumni" onsubmit="return validasiForm()">
            <!-- Input tersembunyi untuk menyimpan ID -->
            <input type="hidden" name="id_alumni" value="<?php echo $id; ?>">
            
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($nama_lengkap); ?>">
            </div>
            <div class="form-group">
                <label for="tahun_lulus">Tahun Lulus</label>
                <input type="number" id="tahun_lulus" name="tahun_lulus" class="form-control" min="1950" max="<?php echo date('Y') + 1; ?>" value="<?php echo htmlspecialchars($tahun_lulus); ?>">
            </div>
            <div class="form-group">
                <label for="jurusan">Jurusan</label>
                <input type="text" id="jurusan" name="jurusan" class="form-control" value="<?php echo htmlspecialchars($jurusan); ?>">
            </div>
            <div class="form-group">
                <label for="pekerjaan">Pekerjaan Saat Ini</label>
                <input type="text" id="pekerjaan" name="pekerjaan" class="form-control" value="<?php echo htmlspecialchars($pekerjaan); ?>">
            </div>
            <div class="form-group">
                <label for="telepon">Nomor Telepon</label>
                <input type="tel" id="telepon" name="telepon" class="form-control" value="<?php echo htmlspecialchars($telepon); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="form-control"><?php echo htmlspecialchars($alamat); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Data</button>
            <a href="index.php" class="btn" style="background-color: #7f8c8d; color: white;">Batal</a>
        </form>
    </div>

    <!-- Menggunakan script validasi yang sama dari tambah.php -->
    <script>
        function validasiForm() {
            var nama = document.getElementById('nama_lengkap').value;
            var tahun = document.getElementById('tahun_lulus').value;
            var jurusan = document.getElementById('jurusan').value;
            var pekerjaan = document.getElementById('pekerjaan').value;
            var telepon = document.getElementById('telepon').value;
            var email = document.getElementById('email').value;
            var alamat = document.getElementById('alamat').value;
            var errorMessage = document.getElementById('error-message');
            var errors = [];

            if (nama.trim() === "") { errors.push("Nama Lengkap wajib diisi."); }
            if (tahun.trim() === "") { errors.push("Tahun Lulus wajib diisi."); }
            if (jurusan.trim() === "") { errors.push("Jurusan wajib diisi."); }
            if (pekerjaan.trim() === "") { errors.push("Pekerjaan Saat Ini wajib diisi."); }
            if (telepon.trim() === "") { errors.push("Nomor Telepon wajib diisi."); }
            if (email.trim() === "") {
                errors.push("Email wajib diisi.");
            } else {
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) { errors.push("Format Email tidak valid."); }
            }
            if (alamat.trim() === "") { errors.push("Alamat wajib diisi."); }

            if (errors.length > 0) {
                errorMessage.innerHTML = errors.join('<br>');
                errorMessage.style.display = 'block';
                return false;
            }
            errorMessage.style.display = 'none';
            return true;
        }
    </script>

</body>
</html>
