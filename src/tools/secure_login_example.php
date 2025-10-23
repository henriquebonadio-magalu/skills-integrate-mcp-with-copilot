<?php
/**
 * secure_login_example.php
 *
 * Minimal example showing how to safely authenticate an admin user
 * using PDO prepared statements and password_verify().
 *
 * This is an example only — adapt paths, session handling and error
 * management to your project.
 */

// Load DB settings from env (or replace with your config loader)
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'sports_club';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die('DB connection failed');
}

// Example: process POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, pass_key FROM admin WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['pass_key'])) {
        // Regenerate session id, set session vars, etc.
        session_start();
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
        echo "Login OK\n";
    } else {
        echo "Invalid credentials\n";
    }
    exit;
}

// Simple HTML form for manual testing
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login example</title></head>
<body>
<form method="post">
  <label>Username: <input name="username"></label><br>
  <label>Password: <input name="password" type="password"></label><br>
  <button type="submit">Login</button>
</form>
</body>
</html>
