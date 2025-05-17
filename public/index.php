<?php
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    header('Content-Type: text/html; charset=utf-8');
    // Autoload
    require_once '../config/database.php';
    require_once '../app/helpers/functions.php';
    // Rotas
    try {
        require_once '../routes/web.php';
    } catch (Throwable $e) {
        http_response_code(500);
        echo "<h1>Erro interno do servidor</h1>";
        if (ini_get('display_errors')) {
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }