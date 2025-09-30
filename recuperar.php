<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar Senha - Bloco de Notas</title>
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
        <p>Insira seu usuário para recuperar a senha.</p>
      </div>
      <form method="post" class="login-form">
        <div class="field with-icon">
          <i class="fas fa-envelope"></i>
          <input type="text" name="user" placeholder="Usuário ou e-mail" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary">Enviar instruções</button>
        <div class="login-links">
          <a href="login.php">Lembrou a senha? Voltar ao login</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
