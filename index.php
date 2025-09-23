<?php
require 'db.php';

function e($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$q = $_GET['q'] ?? ''; // pega o texto digitado no campo "q" da URL
if ($q) {
  // Se tem busca, usa LIKE no SQL
  $stmt = pdo()->prepare("SELECT * FROM notes WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC");
  $stmt->execute(["%$q%", "%$q%"]);
} else {
  // Se não tem busca, mostra todas as notas
  $stmt = pdo()->query("SELECT * FROM notes ORDER BY created_at DESC");
}
// --- depois de carregar $notes do banco
$notes = $stmt->fetchAll();
$noteCount = count($notes); // <-- define a variável para evitar o warning

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notas - SSR + CSR</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        <button type="submit">Salvar</button>
      </form>
    </section>

    <section class="controls">
      <button id="sort-toggle" aria-pressed="false">Ordenar: mais recentes</button>
    </section>

    <section class="notes">

      <form method="get" class="search-form">
        <input type="text" name="q" placeholder="Buscar nota..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit">Buscar</button>
      </form>


      <h2>Lista de notas</h2>
      <!-- Lista inicial SSR -->
      <ul id="notes-list">
        <?php foreach ($notes as $n): ?>
          <li data-id="<?= (int) $n['id'] ?>">
            <article>
              <!-- Removido a imagem da nota -->
              <h3><?= e($n['title']) ?></h3>
              <p><?= nl2br(e($n['content'])) ?></p>
              <time datetime="<?= e($n['created_at']) ?>">Criado em: <?= e($n['created_at']) ?></time>
              <div class="note-actions">
                <button class="edit-note" data-id="<?= (int) $n['id'] ?>">
                  <i class="fas fa-edit"></i> Editar
                </button>
                <button class="delete-note" data-id="<?= (int) $n['id'] ?>">
                  <i class="fas fa-trash"></i> Excluir
                </button>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </main>

  <footer>
    <small>Exemplo educacional — PHP + MySQL + JS</small>
  </footer>

  <script src="assets/app.js" defer></script>
</body>

</html>