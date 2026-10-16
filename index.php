<?php
/**
 * Aplikasi To-Do List Berbasis PHP & MySQL (XAMPP)
 * Uji Kompetensi Junior Web Programmer BNSP
 */

// Konfigurasi Koneksi Database MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_todolist";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
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

// Proses Ubah Status Tugas (Selesai / Belum)
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    // Ambil status saat ini
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

// Ambil semua data tugas dari database
$tasks_result = mysqli_query($conn, "SELECT * FROM tasks ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To-Do List - Database MySQL</title>
    <!-- Memuat Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Aplikasi To-Do List (MySQL)</h3>
                    </div>
                    <div class="card-body">
                        
                        <!-- Form Tambah Tugas -->
                        <form method="POST" action="" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="title" class="form-control" placeholder="Tambahkan tugas baru..." required>
                                <button type="submit" name="add_task" class="btn btn-success">Tambah Tugas</button>
                            </div>
                        </form>

                        <!-- Daftar Tugas -->
                        <h5 class="mb-3">Daftar Tugas</h5>
                        <ul class="list-group">
                            <?php if (mysqli_num_rows($tasks_result) == 0): ?>
                                <li class="list-group-item text-center text-muted">Belum ada tugas.</li>
                            <?php else: ?>
                                <?php while ($task = mysqli_fetch_assoc($tasks_result)): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <!-- Checkbox ubah status -->
                                            <input class="form-check-input" type="checkbox" 
                                                   onclick="window.location.href='index.php?toggle=<?= $task['id']; ?>'" 
                                                   <?= ($task['status'] == 'selesai') ? 'checked' : ''; ?>>
                                            <label class="form-check-label <?= ($task['status'] == 'selesai') ? 'text-decoration-line-through text-muted' : ''; ?>">
                                                <?= htmlspecialchars($task['title']); ?>
                                            </label>
                                        </div>
                                        <!-- Tombol Hapus -->
                                        <a href="index.php?delete=<?= $task['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus tugas ini?')">Hapus</a>
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