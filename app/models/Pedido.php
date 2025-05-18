<?php
    require_once '../config/database.php';
    require_once 'Estoque.php';
    require_once 'Cupom.php';
    class Pedido {
        public static function criar($carrinho, $cep, $endereco, $cupom = null) {
            global $conn;
            $subtotal = 0;
            $descricao = '';
            foreach ($carrinho as $chave => $qtd) {
                $partes = explode(':', $chave);
                $produto_id = intval($partes[0]);
                $variacao_id = isset($partes[1]) ? intval($partes[1]) : null;
                $sql = "SELECT p.nome, p.preco";
                if ($variacao_id) {
                    $sql .= ", v.nome AS variacao_nome";
                }
                $sql .= " FROM produtos p";
                if ($variacao_id) {
                    $sql .= " LEFT JOIN variacoes v ON v.id = $variacao_id";
                }
                $sql .= " WHERE p.id = $produto_id";
                $res = $conn->query($sql);
                $produto = $res->fetch_assoc();
                $subtotal += $produto['preco'] * $qtd;
                $linha = "{$qtd}x " . $produto['nome'];
                if (!empty($produto['variacao_nome'])) {
                    $linha .= " (" . $produto['variacao_nome'] . ")";
                }
                $descricao .= $linha . "\n";
            }
            // Frete
            if ($subtotal > 200) {
                $frete = 0;
            } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
                $frete = 15;
            } else {
                $frete = 20;
            }
            // Cupom
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
            $descricao = $conn->real_escape_string(trim($descricao));
            // Insere pedido com produtos_texto
            $conn->query("INSERT INTO pedidos (subtotal, frete, total, status, cep, endereco, produtos_texto) VALUES ($subtotal, $frete, $total, 'pendente', '$cep', '$endereco', '$descricao')");
            $pedido_id = $conn->insert_id;
            // Atualiza estoque
            foreach ($carrinho as $chave => $qtd) {
                $partes = explode(':', $chave);
                $produto_id = intval($partes[0]);
                $variacao_id = isset($partes[1]) ? intval($partes[1]) : null;
                Estoque::reduzir($produto_id, $qtd, $variacao_id);
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
        public static function enviarEmail($pedido_id, $email) {
            global $conn;
            $pedido = $conn->query("SELECT * FROM pedidos WHERE id = $pedido_id")->fetch_assoc();
            // Itens do pedido vêm direto do campo "produtos_texto"
            $produtosTexto = $pedido['produtos_texto'];
            $assunto = "Confirmação do Pedido #$pedido_id";
            $mensagem = "Olá!\n\nSeu pedido foi recebido com sucesso.\n\n";
            $mensagem .= "🧾 Itens do Pedido:\n" . $produtosTexto . "\n\n";
            $mensagem .= "💳 Total: R$ " . number_format($pedido['total'], 2, ',', '.') . "\n";
            $mensagem .= "📍 Endereço: " . $pedido['endereco'] . "\n";
            $mensagem .= "🚚 Frete: R$ " . number_format($pedido['frete'], 2, ',', '.') . "\n\n";
            $mensagem .= "🙏 Obrigado por comprar conosco!";
            $headers = "From: pedidos@mini-erp.com.br\r\n";
            $headers .= "Reply-To: pedidos@mini-erp.com.br";
            mail($email, $assunto, $mensagem, $headers);
        }
        public static function todos() {
            global $conn;
            $sql = "SELECT * FROM pedidos ORDER BY criado_em DESC";
            return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
    }