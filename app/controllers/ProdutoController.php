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
            // Processar upload de imagem
            $imagem_url = $_POST['imagem_url'] ?? '';
            if (!empty($_FILES['imagem_arquivo']['tmp_name'])) {
                $nome_tmp = $_FILES['imagem_arquivo']['tmp_name'];
                $ext = pathinfo($_FILES['imagem_arquivo']['name'], PATHINFO_EXTENSION);
                $nome_final = uniqid('img_') . '.' . $ext;
                $destino = 'public/uploads/' . $nome_final;
                if (!is_dir('public/uploads')) {
                    mkdir('public/uploads', 0755, true);
                }
                if (move_uploaded_file($nome_tmp, $destino)) {
                    $imagem_url = $destino;
                }
            }
            $dados['imagem_url'] = $imagem_url;
            Produto::salvar($dados);
            header('Location: index.php?rota=produtos');
            exit;
        }
    }