<?php
    class CupomController {
        public function form() {
            require '../app/views/cupons/form.php';
        }
        public function salvar() {
            require_once '../app/models/Cupom.php';
            Cupom::salvar($_POST);
            header('Location: index.php?rota=cupons');
            exit;
        }
        public function validar() {
            header('Content-Type: application/json');
            require_once '../app/models/Cupom.php';
            $cupom = $_POST['cupom'] ?? '';
            $subtotal = floatval($_POST['subtotal'] ?? 0);
            $cupomObj = Cupom::validar($cupom, $subtotal);
            if ($cupomObj) {
                echo json_encode(['valido' => true, 'desconto' => floatval($cupomObj['valor_desconto'])]);
            } else {
                echo json_encode(['valido' => false, 'mensagem' => 'Cupom inválido, expirado ou subtotal abaixo do mínimo.']);
            }
            exit;
        }
    }