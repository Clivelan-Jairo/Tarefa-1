<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

require 'db.php';

$editingNote = null;
$editId = $_GET['edit'] ?? null;

if ($editId) {
    $stmt = pdo()->prepare("SELECT * FROM notes WHERE id = ?");
    $stmt->execute([$editId]);
    $editingNote = $stmt->fetch();
}

function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criar Nota - Bloco de Notas</title>
  <link rel="stylesheet" href="assets/style.css?v=<?= filemtime('assets/style.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
  <header>
    <div class="header-content">
      <div class="header-title">
        <h1>Notas Tapajós</h1>
      </div>
      <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
      <nav class="main-menu">
        <span class="user-greeting">Olá, <?= htmlspecialchars(ucfirst(strtolower(explode(' ', $_SESSION['user'])[0]))) ?></span>
        <a href="notas.php">Minhas Notas</a>
        <a href="index.php" class="active">Nova Nota</a>
        <a href="perfil.php">Perfil</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </header>

  <main>
    <section class="new-note">
      <a href="notas.php" id="cancel-edit" class="btn btn-cancel" style="display:none; float: right; margin-bottom: 10px; text-decoration: none;">Cancelar Edição</a>
      <h2><?= $editingNote ? 'Editando Nota' : 'Criar nova nota' ?></h2>
      <form id="note-form" data-editing="<?= $editingNote ? e($editingNote['id']) : '' ?>">
        <div class="field">
          <label for="title">Título</label>
          <input id="title" name="title" type="text" maxlength="120" value="<?= $editingNote ? e($editingNote['title']) : '' ?>" required>
          <small id="title-count">0/120</small>
        </div>
        <div class="field">
          <label for="content">Conteúdo</label>
          <textarea id="content" name="content" rows="4" required><?= $editingNote ? e($editingNote['content']) : '' ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editingNote ? 'Salvar alterações' : 'Salvar' ?></button>
      </form>
    </section>
  </main>

  <footer>
    <small>Exemplo educacional — PHP + MySQL + JS</small>
  </footer>

  <script src="assets/app.js?v=<?= filemtime('assets/app.js') ?>" defer></script>
  <script>
    // Script para inicializar o estado de edição
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('note-form');
      const title = document.getElementById('title');
      const titleCount = document.getElementById('title-count');
      const cancelEditBtn = document.getElementById('cancel-edit');

      if (form.dataset.editing) {
        titleCount.textContent = `${title.value.length}/120`;
        if (cancelEditBtn) {
          cancelEditBtn.style.display = 'inline-flex';
        }
      } else if (cancelEditBtn) {
        cancelEditBtn.style.display = 'none';
      }
    });
  </script>
</body>

</html>