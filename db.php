<?php
// db.php: conexão PDO com MySQL

// Detecta se o ambiente é local (localhost) ou de produção
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    // --- CONFIGURAÇÃO PARA AMBIENTE LOCAL (XAMPP) ---
    $DB_HOST = '127.0.0.1';
    $DB_NAME = 'notes_app';
    $DB_USER = 'root';
    $DB_PASS = '';
} else {
    // --- CONFIGURAÇÃO PARA SERVIDOR DE HOSPEDAGEM (InfinityFree, etc.) ---
    $DB_HOST = 'sql107.infinityfree.com';
    $DB_NAME = 'if0_40054245_notesapp';
    $DB_USER = 'if0_40054245'; // Corrigido para o nome de usuário correto
    $DB_PASS = 'Jairolopes18';
}

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
        try {
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
        } catch (PDOException $e) {
            // Em um site em produção, é melhor não exibir o erro detalhado.
            die('Erro de conexão com o banco de dados.');
        }
    }
    return $pdo;
}
?>
