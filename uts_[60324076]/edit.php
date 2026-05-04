<?php
require_once 'config/database.php';

// Cek apakah ada ID di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=ID tidak valid");
    exit();
}

$id = (int)$_GET['id'];
$errors = [];

// Ambil data awal  untuk ditampilkan di form
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $conn->prepare("SELECT * FROM kategori WHERE id_kategori=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
       header("Location: index.php?error=Data tidak ditemukan");
       exit();
    }

    $data = $result->fetch_assoc();
    $stmt->close();

    // Set variabel untuk form
    $kode = $data['kode_kategori'];
    $nama = $data['nama_kategori'];
    $deskripsi = $data['deskripsi'];
    $status = $data['status'];
}

// Proses update jika form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $kode = escape($conn, $_POST['kode']);
    $nama = escape($conn, $_POST['nama']);
    $deskripsi = escape($conn, $_POST['deskripsi']);
    $status = escape($conn, $_POST['status']);

    // VALIDASI
    if (empty($kode)) {
        $errors[] = "Kode wajib diisi";
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors[] = "Kode harus 4-10 karakter";
    } elseif (substr($kode, 0, 4) != "KAT-") {
        $errors[] = "Kode harus diawali KAT-";
    }

    if (empty($nama)) {
        $errors[] = "Nama wajib diisi";
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama minimal 3 karakter";
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama maksimal 50 karakter";
    }

    if (!empty($deskripsi) && strlen($deskripsi) > 200) {
        $errors[] = "Deskripsi maksimal 200 karakter";
    }

    // Cek duplikat (kecuali diri sendiri)
    if (empty($errors) == 0) {
        $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori=? AND id_kategori!=?");
        $stmt->bind_param("si", $kode, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Kode sudah digunakan";
        }
        $stmt->close();
    }

    // Jika tidak ada error, update database
    if (count($errors) == 0) {
        $stmt = $conn->prepare("UPDATE kategori SET kode_kategori=?, nama_kategori=?, deskripsi=?, status=? WHERE id_kategori=?");
        $stmt->bind_param("ssssi", $kode, $nama, $deskripsi, $status, $id);

        if ($stmt->execute()) {
            header("Location: index.php?success=Data berhasil diupdate");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">

            <div class="card">

                <div class="card-header bg-warning">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Edit Kategori
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

                        <div class="mb-3">
                            <label class="form-label">
                                Kode Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kode" class="form-control"
                                   value="<?php echo htmlspecialchars($kode); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" class="form-control"
                                   value="<?php echo htmlspecialchars($nama); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?php echo htmlspecialchars($deskripsi); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label><br>

                            <input type="radio" name="status" value="Aktif"
                                <?php echo ($status == 'Aktif') ? 'checked' : ''; ?>> Aktif

                            <input type="radio" name="status" value="Nonaktif"
                                <?php echo ($status == 'Nonaktif') ? 'checked' : ''; ?>> Nonaktif
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Update Data
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