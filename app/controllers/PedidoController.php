<?php
    class PedidoController {
        public function carrinho() {
            require '../app/views/pedidos/carrinho.php';
        }
        public function adicionar() {
            $produto_id = intval($_POST['produto_id']);
            $quantidade = intval($_POST['quantidade']);
            $variacao_id = isset($_POST['variacao_id']) ? intval($_POST['variacao_id']) : null;
            $chave = $variacao_id ? "{$produto_id}:{$variacao_id}" : "{$produto_id}";
            $_SESSION['carrinho'][$chave] = ($_SESSION['carrinho'][$chave] ?? 0) + $quantidade;
            header('Location: index.php?rota=carrinho');
            exit;
        }
        public function finalizar() {
            require_once '../app/models/Pedido.php';
            $pedido = Pedido::criar($_SESSION['carrinho'], $_POST['cep'], $_POST['endereco'], $_POST['cupom'] ?? null);
            unset($_SESSION['carrinho']);
            require '../app/views/pedidos/sucesso.php';
        }
    }