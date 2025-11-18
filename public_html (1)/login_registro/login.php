<?php
// IMPORTAÇÃO BANCO DE DADOS //
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']); 

    // 🔐 LOGIN DO ADMIN SEM PRECISAR CONSULTAR O BANCO
    if ($email === "admin@gmail.com" && $senha === "admin1234") {
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = "ADMIN";
        $_SESSION["admin"] = true;

        header("Location: ../dashboard/dashboard.html");
        exit();
    }

    // === LOGIN DOS USUÁRIOS COMUNS (BANCO DE DADOS) ===
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($senha === $row['senha']) { 
            $_SESSION["id"] = $row['id'];
            $_SESSION["loggedin"] = true;

            header("Location: ../servicos/servicos.html");
            exit();
        } else {
            $error = "Nome ou senha inválidos.";
        }
    } else {
        $error = "Nome ou senha inválidos.";
    }
}
///////////////////////////////////////////////////////////// FIM DE IMPORTAÇÃO /////////////////////////////////////////////////////////////////////////////
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberApp</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <div class="container">
        
        <div class="logo">
            <img src="logo2.png" alt="Logo BarberApp">
        </div>

        <div class="titulo">
            <h1>BarberApp</h1>
        </div>

        <div class="subtitulo">
            <h4>Agenda Online</h4>
        </div>

        <!--FORMULARIO BANCO DE DADOS-->
        <div>
            <form method="post" action="" class="formulario">
                <br>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" class="btn">Acessar</button>
                <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            </form>
        </div>   
        <div class="registrar-se">
            <a href="registro.php">Registrar-se</a>
        </div>
    </div>

</body>
</html>
