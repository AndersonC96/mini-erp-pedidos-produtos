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
        public function listar() {
            require_once '../app/models/Cupom.php';
            $cupons = Cupom::todos();
            require '../app/views/cupons/lista.php';
        }
        public function editar() {
            require_once '../config/database.php';
            global $conn;
            $codigo = $conn->real_escape_string($_GET['codigo']);
            $res = $conn->query("SELECT * FROM cupons WHERE codigo = '$codigo'");
            $cupom = $res->fetch_assoc();
            require '../app/views/cupons/form.php';
        }
        public function excluir() {
            require_once '../config/database.php';
            global $conn;
            $codigo = $conn->real_escape_string($_GET['codigo']);
            $conn->query("DELETE FROM cupons WHERE codigo = '$codigo'");
            $_SESSION['mensagem'] = "Cupom excluído com sucesso!";
            header('Location: index.php?rota=cupons_listar');
            exit;
        }
    }