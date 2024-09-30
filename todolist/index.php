<?php
session_start();

// Jika form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah user terdaftar dan password benar
    if (isset($_SESSION['users'][$username]) && $_SESSION['users'][$username] == $password) {
        // Set session login
        $_SESSION['logged_in'] = $username;

        // Redirect ke home.php
        header("Location: home.php");
        exit(); // Pastikan script berhenti setelah redirect
    } else {
        // Jika login gagal, tampilkan pesan error
        $error_message = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        
        <!-- Tampilkan pesan error jika ada -->
        <?php if (isset($error_message)): ?>
            <p style="color:red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <form method="post" action="index.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Login</button>
        </form>
        <p>Belum memiliki akun? <a href="registrasi.php">Daftar di sini</a></p>
    </div>
</body>
</html>
