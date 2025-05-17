<?php
    require_once '../config/database.php';
    class Estoque {
        public static function reduzir($produto_id, $quantidade) {
            global $conn;
            $conn->query("UPDATE estoques SET quantidade = quantidade - $quantidade WHERE produto_id = $produto_id");
        }
        public static function temEstoque($produto_id, $quantidade) {
            global $conn;
            $res = $conn->query("SELECT quantidade FROM estoques WHERE produto_id = $produto_id");
            $dados = $res->fetch_assoc();
            return $dados && $dados['quantidade'] >= $quantidade;
        }
    }