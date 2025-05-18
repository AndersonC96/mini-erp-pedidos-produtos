<?php
    require_once '../config/database.php';
    class Cupom {
        public static function salvar($dados) {
            global $conn;
            $codigo = $conn->real_escape_string($dados['codigo']);
            $valor = floatval($dados['valor_desconto']);
            $minimo = floatval($dados['minimo_subtotal']);
            $validade = $conn->real_escape_string($dados['validade']);
            $conn->query("INSERT INTO cupons (codigo, valor_desconto, minimo_subtotal, validade) VALUES ('$codigo', $valor, $minimo, '$validade')");
        }
        public static function validar($codigo, $subtotal) {
            global $conn;
            $codigo = $conn->real_escape_string($codigo);
            $res = $conn->query("SELECT * FROM cupons WHERE codigo = '$codigo' AND validade >= CURDATE() AND minimo_subtotal <= $subtotal LIMIT 1");
            return $res->fetch_assoc();
        }
        public static function todos() {
            global $conn;
            $sql = "SELECT * FROM cupons ORDER BY validade DESC";
            $res = $conn->query($sql);
            return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        }
    }