<?php
    require_once '../config/database.php';
    class Cupom {
        public static function salvar($dados) {
            global $conn;
            $codigo = $conn->real_escape_string($dados['codigo']);
            $valor = floatval($dados['valor_desconto']);
            $minimo = floatval($dados['minimo_subtotal']);
            $validade = $conn->real_escape_string($dados['validade']);
            $res = $conn->query("SELECT * FROM cupons WHERE codigo = '$codigo'");
            if ($res->num_rows > 0) {
                $sql = "UPDATE cupons SET valor_desconto = $valor, minimo_subtotal = $minimo, validade = '$validade' WHERE codigo = '$codigo'";
            } else {
                $sql = "INSERT INTO cupons (codigo, valor_desconto, minimo_subtotal, validade) VALUES ('$codigo', $valor, $minimo, '$validade')";
            }
            $conn->query($sql);
        }
        public static function validar($codigo, $subtotal) {
            global $conn;
            $codigo = $conn->real_escape_string($codigo);
            $res = $conn->query("SELECT * FROM cupons WHERE codigo = '$codigo' AND validade >= CURDATE() AND minimo_subtotal <= $subtotal LIMIT 1");
            return $res->fetch_assoc();
        }
        public static function todos() {
            global $conn;
            $filtros = [];
            $ordem_sql = "validade DESC";
            if (!empty($_GET['busca'])) {
                $busca = $conn->real_escape_string($_GET['busca']);
                $filtros[] = "codigo LIKE '%$busca%'";
            }
            if (!empty($_GET['ordenar'])) {
                switch ($_GET['ordenar']) {
                    case 'valor_maior':
                        $ordem_sql = "valor_desconto DESC";
                        break;
                    case 'valor_menor':
                        $ordem_sql = "valor_desconto ASC";
                        break;
                    case 'validade_maior':
                        $ordem_sql = "validade DESC";
                        break;
                    case 'validade_menor':
                        $ordem_sql = "validade ASC";
                        break;
                }
            }
            $sql = "SELECT * FROM cupons";
            if ($filtros) {
                $sql .= " WHERE " . implode(" AND ", $filtros);
            }
            $sql .= " ORDER BY $ordem_sql";
            $res = $conn->query($sql);
            return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        }
    }