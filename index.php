<?php
/**
 * Aplikasi To-Do List Berbasis PHP & MySQL (XAMPP)
 * dibuat untuk tes LSP
 */

// Konfigurasi Koneksi Database MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_todolist";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Proses Tambah Tugas (Aksi POST)
if (isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tasks (title, status) VALUES (?, 'belum')");
        mysqli_stmt_bind_param($stmt, "s", $title);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: index.php");
    exit();
}

// Proses Edit Tugas (Aksi POST dari Form Edit)
if (isset($_POST['update_task'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = mysqli_prepare($conn, "UPDATE tasks SET title = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $title, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: index.php");
    exit();
}

// Proses Ubah Status Tugas (Selesai / Belum)
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $result = mysqli_query($conn, "SELECT status FROM tasks WHERE id = $id");
    if ($row = mysqli_fetch_assoc($result)) {
        $new_status = ($row['status'] == 'selesai') ? 'belum' : 'selesai';
        mysqli_query($conn, "UPDATE tasks SET status = '$new_status' WHERE id = $id");
    }
    header("Location: index.php");
    exit();
}

// Proses Hapus Tugas
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM tasks WHERE id = $id");
    header("Location: index.php");
    exit();
}

// Ambil data tugas yang akan diedit jika parameter edit ada
$edit_task = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM tasks WHERE id = $id_edit");
    $edit_task = mysqli_fetch_assoc($edit_result);
}

// Ambil seluruh data tugas dari database
$tasks_result = mysqli_query($conn, "SELECT * FROM tasks ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To-Do List - Fitur Edit</title>
    <!-- Memuat Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Aplikasi To-Do List</h3>
                    </div>
                    <div class="card-body">
                        
                        <!-- Form Tambah atau Edit Tugas -->
                        <?php if ($edit_task): ?>
                            <!-- Form Edit Tugas -->
                            <form method="POST" action="" class="mb-4">
                                <input type="hidden" name="id" value="<?= $edit_task['id']; ?>">
                                <div class="input-group">
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_task['title']); ?>" required>
                                    <button type="submit" name="update_task" class="btn btn-warning">Update Tugas</button>
                                    <a href="index.php" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- Form Tambah Tugas -->
                            <form method="POST" action="" class="mb-4">
                                <div class="input-group">
                                    <input type="text" name="title" class="form-control" placeholder="Tambahkan tugas baru..." required>
                                    <button type="submit" name="add_task" class="btn btn-success">Tambah</button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <!-- Daftar Tugas -->
                        <h5 class="mb-3">Daftar Tugas</h5>
                        <ul class="list-group">
                            <?php if (mysqli_num_rows($tasks_result) == 0): ?>
                                <li class="list-group-item text-center text-muted">Belum ada tugas.</li>
                            <?php else: ?>
                                <?php while ($task = mysqli_fetch_assoc($tasks_result)): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <!-- Checkbox status tugas -->
                                            <input class="form-check-input" type="checkbox" 
                                                   onclick="window.location.href='index.php?toggle=<?= $task['id']; ?>'" 
                                                   <?= ($task['status'] == 'selesai') ? 'checked' : ''; ?>>
                                            <label class="form-check-label <?= ($task['status'] == 'selesai') ? 'text-decoration-line-through text-muted' : ''; ?>">
                                                <?= htmlspecialchars($task['title']); ?>
                                            </label>
                                        </div>
                                        <div>
                                            <!-- Tombol Edit -->
                                            <a href="index.php?edit=<?= $task['id']; ?>" class="btn btn-warning btn-sm me-1">Edit</a>
                                            <!-- Tombol Hapus -->
                                            <a href="index.php?delete=<?= $task['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus tugas ini?')">Hapus</a>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>