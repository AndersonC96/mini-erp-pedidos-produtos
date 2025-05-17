<?php
    class EstoqueController {
        public function index() {
            require_once '../app/models/Produto.php';
            require_once '../app/models/Estoque.php';
            $produtos = Produto::todosComEstoque();
            require '../app/views/estoque/index.php';
        }
        public function atualizar() {
            require_once '../config/database.php';
            global $conn;
            $produto_id = intval($_POST['produto_id']);
            $variacao_id = isset($_POST['variacao_id']) ? intval($_POST['variacao_id']) : "NULL";
            $quantidade = intval($_POST['quantidade']);
            $sql = "UPDATE estoques SET quantidade = $quantidade WHERE produto_id = $produto_id AND ";
            $sql .= ($variacao_id === 0 || $variacao_id === "NULL") ? "variacao_id IS NULL" : "variacao_id = $variacao_id";
            $conn->query($sql);
            header('Location: index.php?rota=estoque');
            exit;
        }
    }