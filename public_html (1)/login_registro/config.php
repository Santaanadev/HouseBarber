<?php
$db_host = 'srv1965.hstgr.io';
$db_user = 'u855521630_admin';
$db_password = 'Housebarber511.aa';  
$db_name = 'u855521630_housebarber_db';

$conexao = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conexao->connect_errno) {
    die("Erro ao conectar ao banco de dados: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");
?>
