<?php
require_once __DIR__ . '/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$pdo = pdo();
$method = $_SERVER['REQUEST_METHOD'];

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if ($method === 'GET' && isset($_GET['notes'])) {
        $stmt = $pdo->query("SELECT id, title, content, created_at FROM notes ORDER BY created_at DESC");
        $rows = $stmt->fetchAll();
        json_response(['ok' => true, 'data' => $rows]);
    }

    if ($method === 'GET' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if ($id <= 0) {
            json_response(['ok' => false, 'error' => 'ID inválido.'], 400);
        }
        $stmt = $pdo->prepare("SELECT id, title, content FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        $note = $stmt->fetch();
        if (!$note) {
            json_response(['ok' => false, 'error' => 'Nota não encontrada.'], 404);
        }
        json_response(['ok' => true, 'data' => $note]);
    }

    if ($method === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '' || $content === '') {
            json_response(['ok' => false, 'error' => 'Campos obrigatórios.'], 400);
        }
        $stmt = $pdo->prepare("INSERT INTO notes (title, content) VALUES (?, ?)");
        $stmt->execute([$title, $content]);
        $id = $pdo->lastInsertId();
        $row = $pdo->query("SELECT id, title, content, created_at FROM notes WHERE id = " . (int)$id)->fetch();
        json_response(['ok' => true, 'data' => $row], 201);
    }

    if ($method === 'DELETE') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            json_response(['ok' => false, 'error' => 'ID inválido.'], 400);
        }
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        json_response(['ok' => true]);
    }

    if ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        $content = trim($input['content'] ?? '');

        if ($id <= 0 || $title === '' || $content === '') {
            json_response(['ok' => false, 'error' => 'Dados inválidos.'], 400);
        }

        $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $id]);

        $row = $pdo->query("SELECT id, title, content, created_at FROM notes WHERE id = " . (int)$id)->fetch();
        json_response(['ok' => true, 'data' => $row]);
    }

    json_response(['ok' => false, 'error' => 'Rota não encontrada.'], 404);
} catch (Throwable $e) {
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
