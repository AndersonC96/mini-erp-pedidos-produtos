<?php
    class WebhookController {
        public function receber() {
            require_once '../app/models/Pedido.php';
            $dados = json_decode(file_get_contents('php://input'), true);
            $id = $dados['id'] ?? null;
            $status = $dados['status'] ?? null;
            if ($id && $status) {
                if ($status === 'cancelado') {
                    Pedido::remover($id);
                } else {
                    Pedido::atualizarStatus($id, $status);
                }
            }
            http_response_code(200);
            echo json_encode(['ok' => true]);
        }
    }