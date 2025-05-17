<?php
    class ProdutoController {
        public function index() {
            require_once '../app/models/Produto.php';
            $produtos = Produto::todos();
            require '../app/views/produtos/lista.php';
        }
        public function form() {
            require '../app/views/produtos/form.php';
        }
        public function salvar() {
            require_once '../app/models/Produto.php';
            $dados = $_POST;
            Produto::salvar($dados);
            header('Location: index.php?rota=produtos');
            exit;
        }
    }