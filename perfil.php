<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}
require 'db.php';

// Aqui você pode adicionar a lógica para buscar os dados do usuário,
// processar formulários de alteração de senha, etc.

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Meu Perfil - Notas Tapajós</title>
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
      </div>
      <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
      <nav class="main-menu">
        <span class="user-greeting">Olá, <?= htmlspecialchars(ucfirst(strtolower(explode(' ', $_SESSION['user'])[0]))) ?></span>
        <a href="notas.php">Minhas Notas</a>
        <a href="index.php">Nova Nota</a>
        <a href="perfil.php" class="active">Perfil</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </header>
  <main>
    <h2>Meu Perfil</h2>
    <p>Olá, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>!</p>
    <p>Esta página está em construção. Em breve, você poderá gerenciar suas informações aqui.</p>
  </main>
  <footer>
    <small>Exemplo educacional — PHP + MySQL + JS</small>
  </footer>
  <script src="assets/app.js?v=<?= filemtime('assets/app.js') ?>" defer></script>
</body>
</html>