<?php
    class ProdutoController {
        public function index() {
            require_once '../app/models/Produto.php';
            // Captura parâmetros GET
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $busca = $_GET['busca'] ?? '';
            $ordenar = $_GET['ordenar'] ?? 'nome_asc';
            $limite = 9;
            $offset = ($pagina - 1) * $limite;
            // Obtém produtos com filtro, busca e ordenação
            $produtos = Produto::paginar($limite, $offset, $ordenar, $busca);
            $total_registros = Produto::contarTodos($busca);
            $total_paginas = ceil($total_registros / $limite);
            $pagina_atual = $pagina;
            require '../app/views/produtos/lista.php';
        }
        public function form() {
            require '../app/views/produtos/form.php';
        }
        public function salvar() {
            require_once '../app/models/Produto.php';
            $dados = $_POST;
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
        public function editar() {
            require_once '../config/database.php';
            global $conn;
            $id = intval($_GET['id']);
            $res = $conn->query("SELECT * FROM produtos WHERE id = $id");
            $produto = $res->fetch_assoc();
            $variacoes = $conn->query("SELECT * FROM variacoes WHERE produto_id = $id")->fetch_all(MYSQLI_ASSOC);
            $estoques = [];
            foreach ($variacoes as $v) {
                $res = $conn->query("SELECT quantidade FROM estoques WHERE produto_id = $id AND variacao_id = {$v['id']}");
                $row = $res->fetch_assoc();
                $estoques[$v['id']] = $row['quantidade'] ?? 0;
            }
            $res = $conn->query("SELECT quantidade FROM estoques WHERE produto_id = $id AND variacao_id IS NULL");
            $estoque_simples = $res->fetch_assoc()['quantidade'] ?? '';
            require '../app/views/produtos/form.php';
        }
        public function atualizar() {
            require_once '../app/models/Produto.php';
            $dados = $_POST;
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
            Produto::atualizar($dados);
            header('Location: index.php?rota=produtos');
            exit;
        }
        public function excluir() {
            require_once '../app/models/Produto.php';
            $id = intval($_GET['id']);
            Produto::excluir($id);
            $_SESSION['mensagem'] = "Produto excluído com sucesso!";
            header('Location: index.php?rota=produtos');
            exit;
        }
        public function excluirVariacao() {
            require_once '../config/database.php';
            global $conn;
            $id = intval($_GET['id']);
            $conn->query("DELETE FROM estoques WHERE variacao_id = $id");
            $conn->query("DELETE FROM variacoes WHERE id = $id");
            $_SESSION['mensagem'] = "Variação excluída com sucesso!";
            header('Location: index.php?rota=produtos');
            exit;
        }
    }