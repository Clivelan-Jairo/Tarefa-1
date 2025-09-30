<?php
session_start();
if (isset($_SESSION['user'])) {
  header('Location: notas.php');
  exit;
}
require 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['user'] ?? '';
  $pass = $_POST['pass'] ?? '';

  if (empty($username) || empty($pass)) {
      $error = 'Usuário e senha são obrigatórios!';
  } else {
      $stmt = pdo()->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
      $stmt->execute([$username, $username]);
      $user = $stmt->fetch();

      if ($user && password_verify($pass, $user['password'])) {
          // Login bem-sucedido
          $_SESSION['user'] = $user['username'];
          $_SESSION['user_id'] = $user['id']; // Muito importante para as outras funcionalidades
          header('Location: notas.php');
          exit;
      } else {
          $error = 'Usuário ou senha inválidos!';
      }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Bloco de Notas</title>
  <link rel="stylesheet" href="assets/style.css?v=<?= filemtime('assets/style.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
  <main class="login-main">
    <div class="login-container">
      <div class="login-header">
        <h1>Notas Tapajós</h1>
        <p>Bem-vindo de volta! Faça login para continuar.</p>
      </div>
      <form method="post" class="login-form">
        <?php if ($error): ?><div class="login-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="field with-icon">
          <i class="fas fa-user"></i>
          <input type="text" name="user" placeholder="Usuário" required autofocus>
        </div>
        <div class="field with-icon">
          <i class="fas fa-lock"></i>
          <input type="password" name="pass" id="password" placeholder="Senha" required>
          <i class="fas fa-eye toggle-password"></i>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
        <div class="login-links">
          <a href="recuperar.php">Esqueceu a senha?</a>
          <a href="cadastro.php">Criar nova conta</a>
        </div>
      </form>
    </div>
  </main>
</body>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const togglePassword = document.querySelector('.toggle-password');

    if (passwordInput && togglePassword) {
      togglePassword.addEventListener('click', () => {
        // Alterna o tipo do input
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        // Alterna o ícone do olho
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
      });
    }
  });
</script>
</html>
