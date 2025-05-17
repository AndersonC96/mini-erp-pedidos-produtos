<?php
    require_once '../config/database.php';
    require_once 'Estoque.php';
    require_once 'Cupom.php';
    class Pedido {
        public static function criar($carrinho, $cep, $endereco, $cupom = null) {
            global $conn;
            $subtotal = 0;
            foreach ($carrinho as $chave => $qtd) {
                $partes = explode(':', $chave);
                $produto_id = intval($partes[0]);
                $res = $conn->query("SELECT preco FROM produtos WHERE id = $produto_id");
                $produto = $res->fetch_assoc();
                $subtotal += $produto['preco'] * $qtd;
            }
            if ($subtotal > 200) {
                $frete = 0;
            } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
                $frete = 15;
            } else {
                $frete = 20;
            }
            $desconto = 0;
            if ($cupom) {
                $dadosCupom = Cupom::validar($cupom, $subtotal);
                if ($dadosCupom) {
                    $desconto = $dadosCupom['valor_desconto'];
                }
            }
            $total = $subtotal + $frete - $desconto;
            $cep = $conn->real_escape_string($cep);
            $endereco = $conn->real_escape_string($endereco);
            $conn->query("INSERT INTO pedidos (subtotal, frete, total, status, cep, endereco) VALUES ($subtotal, $frete, $total, 'pendente', '$cep', '$endereco')");
            $pedido_id = $conn->insert_id;
            foreach ($carrinho as $chave => $qtd) {
                $partes = explode(':', $chave);
                $produto_id = intval($partes[0]);
                $variacao_id = isset($partes[1]) ? intval($partes[1]) : 'NULL';
                $conn->query("INSERT INTO pedido_itens (pedido_id, produto_id, variacao_id, quantidade) VALUES ($pedido_id, $produto_id, $variacao_id, $qtd)");
                Estoque::reduzir($produto_id, $qtd, $variacao_id !== 'NULL' ? $variacao_id : null);
            }
            return $pedido_id;
        }
        public static function remover($id) {
            global $conn;
            $conn->query("DELETE FROM pedidos WHERE id = $id");
        }
        public static function atualizarStatus($id, $status) {
            global $conn;
            $status = $conn->real_escape_string($status);
            $conn->query("UPDATE pedidos SET status = '$status' WHERE id = $id");
        }
    }