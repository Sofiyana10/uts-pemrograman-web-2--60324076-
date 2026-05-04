<?php
require_once 'config/database.php';

// Query dengan prepared statement
$stmt = $conn->prepare("SELECT * FROM kategori ORDER BY id_kategori DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

    <div class="container mt-5">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2><i class="bi bi-tags"></i> Data Kategori Buku</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
                </a>
            </div>
        </div>

        <?php
        // Tampilkan pesan success/error
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show">';
            echo '<i class="bi bi-check-circle"></i> ' . htmlspecialchars($_GET['success']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }

        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show">';
            echo '<i class="bi bi-x-circle"></i> ' . htmlspecialchars($_GET['error']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Daftar Kategori</h5>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="100">Kode</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th width="100">Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                // Loop data dari database
                                while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><code><?php echo htmlspecialchars($row['kode_kategori']); ?></code></td>
                                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                        <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'Aktif'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit.php?id=<?php echo $row['id_kategori']; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $row['id_kategori']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Total:</strong> <?php echo $result->num_rows; ?> kategori
                    </div>

                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        Belum ada data kategori.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script>
function confirmDelete(id){
    if(confirm('Yakin ingin menghapus?')){
        window.location.href = 'delete.php?id=' + id;
    }
}
</script>

</body>
</html>