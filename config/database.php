<?php
    $host = 'localhost';
    $dbname = 'mini_erp';
    $usuario = 'root';
    $senha = '';
    $conn = new mysqli($host, $usuario, $senha, $dbname);
    if ($conn->connect_error) {
        die("Erro ao conectar com o banco de dados: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");