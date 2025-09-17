<?php
// db.php: conexão PDO com MySQL
// Ajuste as credenciais conforme seu ambiente
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'notes_app';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_CHARSET = 'utf8mb4';

function pdo() {
    static $pdo = null;
    if ($pdo === null) {
        global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;
        $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    }
    return $pdo;
}
?>
