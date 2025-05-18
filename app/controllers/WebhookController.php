<?php
    class WebhookController {
        public function receber() {
            require_once '../app/models/Pedido.php';
            $json = file_get_contents('php://input');
            $dados = json_decode($json, true);
            if (!isset($dados['id']) || !isset($dados['status'])) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID e status são obrigatórios']);
                return;
            }
            $id = intval($dados['id']);
            $status = strtolower($dados['status']);
            if ($status === 'cancelado') {
                Pedido::excluir($id);
                echo json_encode(['mensagem' => 'Pedido cancelado e removido']);
            } else {
                Pedido::alterarStatus($id, $status);
                echo json_encode(['mensagem' => 'Status atualizado']);
            }
        }
    }