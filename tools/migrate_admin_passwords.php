<?php
/**
 * migrate_admin_passwords.php
 *
 * One-time migration script to convert plain-text admin passwords
 * into bcrypt hashes using PHP's password_hash().
 *
 * Usage (from project root):
 *  php tools/migrate_admin_passwords.php
 *
 * REQUIREMENTS
 * - PHP 7.4+ with PDO and PDO_MYSQL
 * - A recent backup of your database (this script will update rows).
 *
 * The script will only hash passwords that don't already look like
 * a bcrypt hash (checks prefix $2y$ or $2b$).
 */

// Read DB connection from environment variables for safety.
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'sports_club';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

echo "\n== Admin password migration script ==\n";
echo "This script will convert plain-text admin passwords into bcrypt hashes.\n";
echo "Make sure you have a DB backup before proceeding.\n\n";

$confirm = readline("Type 'YES' to continue: ");
if (trim($confirm) !== 'YES') {
    echo "Aborted. No changes made.\n";
    exit(0);
}

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    fwrite(STDERR, "DB connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$select = $pdo->query("SELECT id, username, pass_key FROM admin");
$admins = $select->fetchAll(PDO::FETCH_ASSOC);

if (!$admins) {
    echo "No admin rows found in 'admin' table.\n";
    exit(0);
}

$updated = 0;
foreach ($admins as $row) {
    $id = $row['id'];
    $username = $row['username'];
    $pass = $row['pass_key'];

    // naive check for bcrypt hash: starts with $2y$ or $2b$
    if (preg_match('/^\$2[aby]\$\d{2}\$/', $pass)) {
        echo "[OK] id=$id username=$username already hashed.\n";
        continue;
    }

    echo "Hashing password for id=$id username=$username... ";
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    if ($hash === false) {
        echo "FAILED\n";
        continue;
    }

    $stmt = $pdo->prepare('UPDATE admin SET pass_key = :hash WHERE id = :id');
    $stmt->execute([':hash' => $hash, ':id' => $id]);
    echo "done.\n";
    $updated++;
}

echo "\nMigration complete. $updated account(s) updated.\n";
echo "Remember to revoke access to this script after use.\n";

?>
