<?php
    class EstoqueController {
        public function index() {
            require_once '../app/models/Produto.php';
            require_once '../app/models/Estoque.php';
            global $conn;
            $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
            $ordem = $_GET['ordem'] ?? '';
            $pagina = max(1, intval($_GET['pagina'] ?? 1));
            $por_pagina = 10;
            $offset = ($pagina - 1) * $por_pagina;
            $where = '';
            if ($busca !== '') {
                $busca = $conn->real_escape_string($busca);
                $where = "WHERE p.nome LIKE '%$busca%' OR v.nome LIKE '%$busca%'";
            }
            $order_by = 'p.nome ASC';
            if ($ordem === 'nome_desc') $order_by = 'p.nome DESC';
            elseif ($ordem === 'qtd_asc') $order_by = 'e.quantidade ASC';
            elseif ($ordem === 'qtd_desc') $order_by = 'e.quantidade DESC';
            $sql = "SELECT p.id AS produto_id, p.nome, v.id AS variacao_id, v.nome AS variacao, e.quantidade FROM produtos p LEFT JOIN variacoes v ON v.produto_id = p.id LEFT JOIN estoques e ON e.produto_id = p.id AND (e.variacao_id = v.id OR (v.id IS NULL AND e.variacao_id IS NULL)) $where ORDER BY $order_by LIMIT $por_pagina OFFSET $offset";
            $produtos = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            $sql_total = "SELECT COUNT(*) AS total FROM produtos p LEFT JOIN variacoes v ON v.produto_id = p.id LEFT JOIN estoques e ON e.produto_id = p.id AND (e.variacao_id = v.id OR (v.id IS NULL AND e.variacao_id IS NULL)) $where";
            $total_resultado = $conn->query($sql_total)->fetch_assoc()['total'] ?? 0;
            $total_paginas = ceil($total_resultado / $por_pagina);
            $pagina_atual = $pagina;
            require '../app/views/estoque/index.php';
        }
        public function atualizar() {
            require_once '../config/database.php';
            global $conn;
            $produto_id = intval($_POST['produto_id']);
            $variacao_id = isset($_POST['variacao_id']) ? intval($_POST['variacao_id']) : "NULL";
            $quantidade = intval($_POST['quantidade']);
            $sql = "UPDATE estoques SET quantidade = $quantidade WHERE produto_id = $produto_id AND ";
            $sql .= ($variacao_id === 0 || $variacao_id === "NULL") ? "variacao_id IS NULL" : "variacao_id = $variacao_id";
            $conn->query($sql);
            header('Location: index.php?rota=estoque');
            exit;
        }
    }