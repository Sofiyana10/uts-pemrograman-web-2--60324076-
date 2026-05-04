<?php
require_once 'config/database.php';

// Inisialisasi variabel
$errors = [];
$kode = '';
$nama = '';
$deskripsi = '';
$status = 'Aktif';

// Proses form jika di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi data
    $kode = escape($conn, $_POST['kode']);
    $nama = escape($conn, $_POST['nama']);
    $deskripsi = escape($conn, $_POST['deskripsi']);
    $status = escape($conn, $_POST['status']);

    // Validasi Kode
    if (empty($kode)) {
        $errors[] = "Kode wajib diisi";
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors[] = "Kode harus 4-10 karakter";
    } elseif (substr($kode, 0, 4) != "KAT-") {
        $errors[] = "Kode harus diawali KAT-";
    }

    // Validasi Nama Kategori
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi";
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama kategori minimal 3 karakter";
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama kategori maksimal 50 karakter";
    }

    // Validasi Deskripsi
    if (!empty($deskripsi) && strlen($deskripsi) > 200) {
    $errors[] = "Deskripsi maksimal 200 karakter";
    }

    // Cek duplikat
    if (count($errors) == 0) {
        $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ?");
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Kode sudah digunakan";
        }
    }

    // Insert
    if (count($errors) == 0) {
        $stmt = $conn->prepare("INSERT INTO kategori (kode_kategori,nama_kategori,deskripsi,status) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $kode, $nama, $deskripsi, $status);

        if ($stmt->execute()) {
            header("Location: index.php?success=Data berhasil ditambahkan");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">

                <!-- HEADER -->
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
                    </h4>
                </div>
                <div class="card-body">
                    <!-- ERROR -->
                    <?php if (count($errors) > 0): ?>
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle"></i> Terdapat kesalahan:</h6>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <!-- KODE -->
                        <div class="mb-3">
                            <label class="form-label">
                                Kode Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="kode"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($kode); ?>"
                                   placeholder="KAT-001"
                                   required>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($nama); ?>"
                                   placeholder="Masukkan nama kategori"
                                   required>
                        </div>

                        <!-- DESKRIPSI -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Deskripsi kategori"><?php echo htmlspecialchars($deskripsi); ?></textarea>
                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">
                            <label class="form-label">Status</label><br>

                            <input type="radio" name="status" value="Aktif"
                                <?php echo ($status == 'Aktif') ? 'checked' : ''; ?>>
                            Aktif

                            <input type="radio" name="status" value="Nonaktif"
                                <?php echo ($status == 'Nonaktif') ? 'checked' : ''; ?>>
                            Nonaktif
                        </div>

                        <hr>

                        <!-- BUTTON -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Data
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>