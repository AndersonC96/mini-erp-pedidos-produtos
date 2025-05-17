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
    }