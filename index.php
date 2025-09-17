<?php
require_once __DIR__ . '/db.php';
$pdo = pdo();

// Busca inicial SSR
$stmt = $pdo->query("SELECT id, title, content, created_at, image FROM notes ORDER BY created_at DESC");
$notes = $stmt->fetchAll();
$noteCount = count($notes);

function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notas - SSR + CSR</title>
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <header>
    <h1>Bloco de Notas</h1>
    <p id="note-count">Total de notas: <?= $noteCount ?></p>
  </header>

  <main>
    <section class="new-note">
      <h2>Criar nova nota</h2>
      <form id="note-form">
        <div class="field">
          <label for="title">Título</label>
          <input id="title" name="title" type="text" maxlength="120" required>
          <small id="title-count">0/120</small>
        </div>
        <div class="field">
          <label for="content">Conteúdo</label>
          <textarea id="content" name="content" rows="4" required></textarea>
        </div>
        <div class="field">
          <label for="image">Imagem (URL opcional)</label>
          <input id="image" name="image" type="text" maxlength="255">
        </div>
        <button type="submit">Salvar</button>
      </form>
    </section>

    <section class="controls">
      <button id="sort-toggle" aria-pressed="false">Ordenar: mais recentes</button>
    </section>

    <section class="notes">
      <h2>Lista de notas</h2>
      <!-- Lista inicial SSR -->
      <ul id="notes-list">
        <?php foreach ($notes as $n): ?>
          <li data-id="<?= (int)$n['id'] ?>">
            <article>
              <img src="<?= e($n['image'] ?? 'assets/default-image.jpg') ?>" alt="Nota" class="note-image">
              <h3><?= e($n['title']) ?></h3>
              <p><?= nl2br(e($n['content'])) ?></p>
              <time datetime="<?= e($n['created_at']) ?>">Criado em: <?= e($n['created_at']) ?></time>
              <button class="edit-note" data-id="<?= (int)$n['id'] ?>">
                <i class="fas fa-edit"></i> Editar
              </button>
              <button class="delete-note" data-id="<?= (int)$n['id'] ?>">
                <i class="fas fa-trash"></i> Excluir
              </button>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
      <p id="empty" <?= count($notes) ? 'hidden' : '' ?>>Sem notas ainda. Que tal criar a primeira? ✨</p>
    </section>
  </main>

  <footer>
    <small>Exemplo educacional — PHP + MySQL + JS</small>
  </footer>

  <script src="assets/app.js" defer></script>
</body>
</html>
