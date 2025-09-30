<?php
require 'db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['user'] ?? '');
    $password = $_POST['pass'] ?? '';
    $password2 = $_POST['pass2'] ?? '';

    if (empty($email) || empty($username) || empty($password)) {
        $error = 'Todos os campos são obrigatórios.';
    } elseif ($password !== $password2) {
        $error = 'As senhas não coincidem.';
    } elseif (strlen($password) < 8) {
        $error = 'A senha deve ter no mínimo 8 caracteres.';
    } else {
        try {
            $stmt = pdo()->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$email, $username, $hashed_password]);
            $success = 'Conta criada com sucesso! Você já pode fazer o login.';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Código de erro para violação de chave única (usuário/email já existe)
                $error = 'Este e-mail ou nome de usuário já está em uso.';
            } else {
                $error = 'Ocorreu um erro ao criar a conta. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criar Conta - Bloco de Notas</title>
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
        <p>Crie sua conta para começar.</p>
      </div>
      <form method="post" class="login-form">
        <?php if ($error): ?><div class="login-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="login-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <div class="field with-icon">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" id="email" placeholder="E-mail" required>
          <span class="status-icon"></span>
          <div id="email-feedback" class="feedback-message"></div>
        </div>

        <div class="field with-icon">
          <i class="fas fa-user"></i>
          <input type="text" name="user" id="username" placeholder="Usuário" required autofocus>
          <span class="status-icon"></span>
        </div>
        <div class="field with-icon">
          <i class="fas fa-lock"></i>
          <input type="password" name="pass" id="password" placeholder="Senha" required>
          <span class="status-icon"></span>
          <div id="password-strength" class="feedback-message"></div>
        </div>

        <div class="field with-icon">
          <i class="fas fa-lock"></i>
          <input type="password" name="pass2" id="confirm-password" placeholder="Repita a senha" required>
          <span class="status-icon"></span>
          <div id="password-match-feedback" class="feedback-message"></div>
        </div>

        <button type="submit" class="btn btn-primary" id="register-button">Cadastrar</button>
        <div class="login-links">
          <a href="login.php">Já tenho uma conta</a>
        </div>
      </form>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('.login-form');
      const emailInput = document.getElementById('email');
      const usernameInput = document.getElementById('username');
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirm-password');
      const emailFeedback = document.getElementById('email-feedback');
      const passwordStrength = document.getElementById('password-strength');
      const passwordMatchFeedback = document.getElementById('password-match-feedback');
      const registerButton = document.getElementById('register-button');

      // Função auxiliar para definir o status visual do campo (borda, ícone)
      function setStatus(field, status, iconHtml = '') {
          field.classList.remove('error', 'warning', 'success');
          if (status) field.classList.add(status);
          field.querySelector('.status-icon').innerHTML = iconHtml;
      }

      // Função auxiliar para limpar o estado de sucesso de um campo
      function clearPreviousSuccessState(inputElement) {
        const parentField = inputElement.closest('.field');
        if (parentField && parentField.classList.contains('success')) {
          setStatus(parentField, null); // Remove todas as classes de status e o ícone
          parentField.querySelector('.feedback-message').classList.remove('active'); // Garante que o tooltip suma
        }
      }

      function validateEmail() {
        const email = emailInput.value.trim();
        const parentField = emailInput.closest('.field');
        emailFeedback.classList.remove('error', 'success', 'active'); // Limpa classes de feedback anteriores

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === '') {
          emailFeedback.textContent = '';
          setStatus(parentField, null);
          return false;
        } else if (!emailRegex.test(email)) {
          emailFeedback.textContent = 'E-mail inválido. Formato esperado: usuario@dominio.com';
          emailFeedback.classList.add('error', 'active');
          setStatus(parentField, 'error', '<i class="fas fa-times-circle"></i>');
          return false;
        }
        // Em caso de sucesso, não mostra o tooltip, apenas o ícone e a borda.
        emailFeedback.classList.remove('active'); // Garante que o tooltip não apareça
        setStatus(parentField, 'success', '<i class="fas fa-check-circle"></i>');
        return true;
      }

      function checkPasswordStrength() {
        const password = passwordInput.value;
        const parentField = passwordInput.closest('.field');
        passwordStrength.classList.remove('error', 'warning', 'success', 'active'); // Limpa classes de feedback anteriores

        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++; // Caracteres especiais

        if (password.length === 0) {
          passwordStrength.textContent = '';
          setStatus(parentField, null);
          return false;
        } else if (strength <= 2) {
          passwordStrength.textContent = 'Senha fraca';
          passwordStrength.classList.add('error', 'active');
          setStatus(parentField, 'error', '<i class="fas fa-exclamation-circle"></i>');
        } else if (strength <= 4) {
          passwordStrength.textContent = 'Senha média';
          passwordStrength.classList.add('warning', 'active');
          setStatus(parentField, 'warning', '<i class="fas fa-exclamation-circle"></i>');
        } else {
          passwordStrength.classList.remove('active'); // Garante que o tooltip não apareça
          setStatus(parentField, 'success', '<i class="fas fa-check-circle"></i>');
        }
        return strength > 2; // Considera média ou forte como aceitável para submissão
      }

      function comparePasswords() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const parentField = confirmPasswordInput.closest('.field');
        passwordMatchFeedback.classList.remove('error', 'success', 'active'); // Limpa classes de feedback anteriores

        if (password.length === 0 && confirmPassword.length === 0) {
            passwordMatchFeedback.textContent = '';
            setStatus(parentField, null);
            return false;
        } else if (confirmPassword.length > 0 && password !== confirmPassword) {
          passwordMatchFeedback.textContent = 'As senhas não coincidem.';
          passwordMatchFeedback.classList.add('error', 'active');
          setStatus(parentField, 'error', '<i class="fas fa-times-circle"></i>');
          return false;
        } else if (confirmPassword.length > 0 && password === confirmPassword) {
          passwordMatchFeedback.classList.remove('active'); // Garante que o tooltip não apareça
          setStatus(parentField, 'success', '<i class="fas fa-check-circle"></i>');
        } else {
          passwordMatchFeedback.textContent = '';
          setStatus(parentField, null);
        }
        return password === confirmPassword;
      }

      function validateUsername() {
        const username = usernameInput.value.trim();
        const parentField = usernameInput.closest('.field');
        if (username === '') {
            setStatus(parentField, null);
            return false;
        }
        setStatus(parentField, 'success'); // Apenas marca como preenchido, sem ícone
        return true;
      }

      // Função que verifica o estado atual das validações sem alterar a UI
      function checkAllValidations() {
        const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim());
        const isUsernameValid = usernameInput.value.trim() !== '';
        const password = passwordInput.value;
        const isPasswordStrongEnough = password.length >= 8 && /[a-z]/.test(password) && /[A-Z]/.test(password) && /[0-9]/.test(password);
        const doPasswordsMatch = password === confirmPasswordInput.value && password !== '';
        return isEmailValid && isUsernameValid && isPasswordStrongEnough && doPasswordsMatch;
      }

      function validateForm(event) {
        // Se o formulário não for válido, previne o envio e força a exibição de todos os feedbacks de erro.
        if (!checkAllValidations()) {
          event.preventDefault();
          // Força a revalidação visual de todos os campos para mostrar os erros
          validateEmail();
          validateUsername();
          checkPasswordStrength();
          comparePasswords();
          alert('Por favor, corrija os campos destacados antes de continuar.');
        }
      }

      // --- Lógica para limpar o feedback de sucesso ao avançar ---

      // Limpa o campo de e-mail quando o usuário foca no nome de usuário
      usernameInput.addEventListener('focus', () => {
        clearPreviousSuccessState(emailInput);
      });

      // Limpa o campo de senha quando o usuário foca na confirmação de senha
      confirmPasswordInput.addEventListener('focus', () => {
        clearPreviousSuccessState(passwordInput);
      });

      // Limpa o campo de nome de usuário quando o usuário foca na senha
      passwordInput.addEventListener('focus', () => {
        clearPreviousSuccessState(usernameInput);
      });

      // Adiciona listeners para validação em tempo real
      emailInput.addEventListener('input', validateEmail);
      usernameInput.addEventListener('input', validateUsername);
      passwordInput.addEventListener('input', () => {
        checkPasswordStrength();
        comparePasswords();
      });
      confirmPasswordInput.addEventListener('input', comparePasswords);
      form.addEventListener('submit', validateForm);

      // Executa as validações iniciais caso os campos já venham preenchidos (ex: autofill do navegador)
      validateEmail();
      validateUsername();
      checkPasswordStrength();
      comparePasswords();
    });
  </script>
</body>
</html>
