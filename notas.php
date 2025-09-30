<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}
require 'db.php';
// ...código para buscar notas igual ao index.php...
$q = $_GET['q'] ?? '';
if ($q) {
  $stmt = pdo()->prepare("SELECT * FROM notes WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC");
  $stmt->execute(["%$q%", "%$q%"]);
} else {
  $stmt = pdo()->query("SELECT * FROM notes ORDER BY created_at DESC");
}
$notes = $stmt->fetchAll();
$noteCount = count($notes);
function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Minhas Notas</title>
  <link rel="stylesheet" href="assets/style.css?v=<?= filemtime('assets/style.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
  <header>
    <div class="header-content">
      <div class="header-title">
        <h1>Notas Tapajós</h1>
        <p id="note-count">Total de notas: <?= $noteCount ?></p>
      </div>
      <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
      <nav class="main-menu">
        <span class="user-greeting">Olá, <?= htmlspecialchars(ucfirst(strtolower(explode(' ', $_SESSION['user'])[0]))) ?></span>
        <a href="notas.php" class="active">Minhas Notas</a>
        <a href="index.php">Nova Nota</a>
        <a href="perfil.php">Perfil</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </header>
  <main>
    <section class="notes">
      <div class="controls-container">
        <form method="get" class="search-form">
          <input type="text" name="q" placeholder="Buscar nota..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
          <button type="submit" class="btn">Buscar</button>
        </form>
        <button id="sort-toggle" class="btn btn-round" title="Alternar Ordem" data-order="desc"><i class="fas fa-sort"></i></button>
      </div>
      <h2>Lista de notas</h2>
      <ul id="notes-list">
        <?php foreach ($notes as $n): ?>
          <li data-id="<?= (int) $n['id'] ?>" <?php if (empty($notes)) echo 'id="empty"'; ?>>
            <article>
              <h3><?= e($n['title']) ?></h3>
              <p><?= nl2br(e($n['content'])) ?></p>
              <time datetime="<?= e($n['created_at']) ?>">Criado em: <?= e($n['created_at']) ?></time>
              <div class="note-actions">
                <button class="btn btn-edit edit-note" data-id="<?= (int) $n['id'] ?>">
                  <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-delete delete-note" data-id="<?= (int) $n['id'] ?>">
                  <i class="fas fa-trash"></i> Excluir
                </button>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
      <p id="empty" <?= !empty($notes) ? 'hidden' : '' ?>>
        Nenhuma nota encontrada. <a href="index.php">Crie a primeira!</a>
      </p>
    </section>
    <a href="index.php" id="add-note-fab" title="Nova Nota"><i class="fas fa-plus"></i></a>
  </main>
  <script src="assets/app.js?v=<?= filemtime('assets/app.js') ?>" defer></script>
</body>
</html>
