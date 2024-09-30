<?php
session_start();

// Fitur Logout
if (isset($_GET['logout'])) {
    session_destroy(); // Hapus semua session
    header("Location: index.php"); // Arahkan ke halaman index
    exit; // Hentikan eksekusi
}

// Inisialisasi to-do list jika belum ada di session
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}

// Tambah item to-do list
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update'])) {
    $task = $_POST['task'];
    $deadline_date = $_POST['deadline_date'] ?? ''; // Tanggal deadline opsional
    $deadline_time = $_POST['deadline_time'] ?? ''; // Jam deadline opsional
    $deadline = $deadline_date . ($deadline_time ? ' ' . $deadline_time : ''); // Gabungkan tanggal dan jam
    $status = false; // Status default (belum selesai)

    // Periksa apakah tugas sudah ada di dalam to-do list
    $taskExists = false;
    foreach ($_SESSION['todos'] as $todo) {
        if ($todo['task'] === $task && $todo['deadline'] === $deadline) {
            $taskExists = true;
            break;
        }
    }

    // Jika tugas belum ada, tambahkan ke dalam session
    if (!$taskExists && !empty($task)) {
        $_SESSION['todos'][] = [
            'task' => $task,
            'deadline' => $deadline,
            'status' => $status
        ];
    }
}

// Update status item (menandai sudah selesai)
if (isset($_GET['toggle'])) {
    $index = $_GET['toggle'];
    $_SESSION['todos'][$index]['status'] = !$_SESSION['todos'][$index]['status'];
}

// Hapus item dari to-do list
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    array_splice($_SESSION['todos'], $index, 1);
}

// Edit item to-do list
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $index = $_POST['index'];
    $task = $_POST['task'];
    $deadline_date = $_POST['deadline_date'] ?? '';
    $deadline_time = $_POST['deadline_time'] ?? '';
    $deadline = $deadline_date . ($deadline_time ? ' ' . $deadline_time : '');

    // Update tugas dan deadline
    $_SESSION['todos'][$index] = [
        'task' => $task,
        'deadline' => $deadline,
        'status' => $_SESSION['todos'][$index]['status']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="css/stylehome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <div class="todo-container">
        <h2>To-Do List</h2>

        <!-- Link Logout dengan Ikon -->
            <a href="home.php?logout=true" style="text-align: right; display: block; margin-bottom: 20px;">
                <button style="background-color: transparent; border: none;">
                    <i class="fas fa-sign-out-alt" style="font-size: 24px; color: black;"></i>
                </button>
            </a>

        <!-- Form untuk menambah tugas -->
        <form method="post" action="home.php">
            <input type="text" name="task" placeholder="Masukkan aktivitas..." required>
            <input type="date" name="deadline_date" placeholder="Pilih tanggal deadline (opsional)">
            <input type="time" name="deadline_time" placeholder="Pilih waktu deadline (opsional)">
            <button type="submit">Tambah</button>
        </form>

        <!-- Daftar to-do -->
        <ul class="todo-list">
            <?php foreach ($_SESSION['todos'] as $index => $todo): ?>
                <li class="<?= $todo['status'] ? 'completed' : '' ?>">
                    <input type="checkbox" onclick="window.location.href='home.php?toggle=<?= $index ?>'" <?= $todo['status'] ? 'checked' : '' ?>>
                    <span><?= $todo['task'] ?></span>
                    <?php if ($todo['deadline']): ?>
                        <small>(Deadline: <?= $todo['deadline'] ?>)</small>
                    <?php endif; ?>
                    <a href="home.php?delete=<?= $index ?>" class="delete-btn">Hapus</a>
                    <a href="#" onclick="editTask(<?= $index ?>)" class="edit-btn">Edit</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Form edit (hidden by default, muncul saat klik "Edit") -->
        <div id="editForm" style="display:none;">
            <h3>Edit Tugas</h3>
            <form method="post" action="home.php">
                <input type="hidden" name="index" id="editIndex">
                <input type="text" name="task" id="editTask" required>
                <input type="date" name="deadline_date" id="editDeadlineDate">
                <input type="time" name="deadline_time" id="editDeadlineTime">
                <button type="submit" name="update">Update</button>
            </form>
        </div>
    </div>

    <script>
        function editTask(index) {
            // Ambil data tugas yang akan diedit
            var task = <?= json_encode(array_column($_SESSION['todos'], 'task')) ?>;
            var deadline = <?= json_encode(array_column($_SESSION['todos'], 'deadline')) ?>;

            // Pisahkan tanggal dan waktu dari deadline
            var deadlineDate = '';
            var deadlineTime = '';
            if (deadline[index]) {
                var deadlineParts = deadline[index].split(' ');
                deadlineDate = deadlineParts[0];
                deadlineTime = deadlineParts[1] || '';
            }
            
            // Isi form edit dengan data tugas dan deadline
            document.getElementById('editIndex').value = index;
            document.getElementById('editTask').value = task[index];
            document.getElementById('editDeadlineDate').value = deadlineDate;
            document.getElementById('editDeadlineTime').value = deadlineTime;

            // Tampilkan form edit
            document.getElementById('editForm').style.display = 'block';
        }
    </script>
</body>
</html>
