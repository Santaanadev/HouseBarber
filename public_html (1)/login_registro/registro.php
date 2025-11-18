<?php
require_once "config.php";

$msg_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);

    // Validação da confirmação de senha
    if ($senha !== $confirmar_senha) {
        $msg_erro = 'As senhas não coincidem.';
    } else {
        // Verificar se o email já existe
        $sql_check = "SELECT id FROM usuarios WHERE email = ?";
        $stmt_check = $conexao->prepare($sql_check);
        if (!$stmt_check) {
            $msg_erro = 'Erro na preparação da consulta.';
        } else {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $msg_erro = 'Esse e-mail já está cadastrado.';
            } else {
                // Inserir novo usuário
                $sql = "INSERT INTO usuarios (nome, senha, email) VALUES (?, ?, ?)";
                $stmt = $conexao->prepare($sql);
                if (!$stmt) {
                    $msg_erro = 'Erro na preparação da consulta.';
                } else {
                    $stmt->bind_param("sss", $nome, $senha, $email);
                    if ($stmt->execute()) {
                        header("Location: login.php");
                        exit;
                    } else {
                        $msg_erro = 'Erro ao cadastrar usuário. Tente novamente.';
                    }
                    $stmt->close();
                }
            }
            $stmt_check->close();
        }
    }
    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrar-se - BarberApp</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="registro.css" />

</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logo2.png" alt="Logo BarberApp" />
        </div>
        <div class="titulo">
            <h1>Criar Conta</h1>
        </div>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="formulario" novalidate>
            <input type="text" name="nome" placeholder="Nome Completo" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="senha" placeholder="Senha" required />
            <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Confirmar senha" required />
            <button type="submit" class="btn">Registrar</button>
            <?php if (!empty($msg_erro)): ?>
                <div class="msg-erro"><?php echo $msg_erro; ?></div>
            <?php endif; ?>
        </form>

        <div class="registrar-se">
            <a href="login.php">Já tem conta? Faça login</a>
        </div>
    </div>
</body>
</html>
