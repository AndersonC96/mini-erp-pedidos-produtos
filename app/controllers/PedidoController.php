<?php
    class PedidoController {
        public function carrinho() {
            require '../app/views/pedidos/carrinho.php';
        }
        public function adicionar() {
            $produto_id = $_POST['produto_id'];
            $quantidade = $_POST['quantidade'];
            $_SESSION['carrinho'][$produto_id] = ($_SESSION['carrinho'][$produto_id] ?? 0) + $quantidade;
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