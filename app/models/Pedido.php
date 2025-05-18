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
            $descricao = $conn->real_escape_string(trim($descricao));
            $conn->query("INSERT INTO pedidos (subtotal, frete, total, status, cep, endereco, produtos_texto) VALUES ($subtotal, $frete, $total, 'pendente', '$cep', '$endereco', '$descricao')");
            $pedido_id = $conn->insert_id;
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
        public static function todos($filtros = []) {
            global $conn;
            $where = [];
            $ordem = "criado_em DESC";
            if (!empty($filtros['status'])) {
                $status = $conn->real_escape_string($filtros['status']);
                $where[] = "status = '$status'";
            }
            if (!empty($filtros['busca'])) {
                $busca = $conn->real_escape_string($filtros['busca']);
                $where[] = "produtos_texto LIKE '%$busca%'";
            }
            if (!empty($filtros['ordem'])) {
                switch ($filtros['ordem']) {
                    case 'mais_novo': $ordem = "criado_em DESC"; break;
                    case 'mais_antigo': $ordem = "criado_em ASC"; break;
                    case 'maior_valor': $ordem = "total DESC"; break;
                    case 'menor_valor': $ordem = "total ASC"; break;
                }
            }
            $sql = "SELECT * FROM pedidos";
            if ($where) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY $ordem";
            return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
        public static function todosFiltrado($busca = '', $status = '', $ordenar = '', $pagina = 1, $por_pagina = 10, &$total_paginas = 1) {
            global $conn;
            $offset = ($pagina - 1) * $por_pagina;
            $where = [];
            if ($busca !== '') {
                $busca = $conn->real_escape_string($busca);
                $where[] = "produtos_texto COLLATE utf8mb4_general_ci LIKE '%$busca%'";
            }
            if ($status !== '') {
                $status = $conn->real_escape_string($status);
                $where[] = "status = '$status'";
            }
            $where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
            $order_sql = "ORDER BY criado_em DESC";
            if ($ordenar === 'maior_valor') {
                $order_sql = "ORDER BY total DESC";
                } elseif ($ordenar === 'menor_valor') {
                    $order_sql = "ORDER BY total ASC";
                } elseif ($ordenar === 'mais_antigo') {
                    $order_sql = "ORDER BY criado_em ASC";
                } elseif ($ordenar === 'mais_novo') {
                    $order_sql = "ORDER BY criado_em DESC";
            }
            $resTotal = $conn->query("SELECT COUNT(*) as total FROM pedidos $where_sql");
            $total = $resTotal->fetch_assoc()['total'] ?? 0;
            $total_paginas = ceil($total / $por_pagina);
            $sql = "SELECT * FROM pedidos $where_sql $order_sql LIMIT $offset, $por_pagina";
            $res = $conn->query($sql);
            $dados = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            return [
                'dados' => $dados,
                'total_paginas' => $total_paginas
            ];
        }
        public static function alterarStatus($id, $novo_status) {
            global $conn;
            $novo_status = $conn->real_escape_string($novo_status);
            $conn->query("UPDATE pedidos SET status='$novo_status' WHERE id=$id");
        }
        public static function excluir($id) {
            global $conn;
            $id = intval($id);
            $conn->query("DELETE FROM pedidos WHERE id = $id");
        }
    }