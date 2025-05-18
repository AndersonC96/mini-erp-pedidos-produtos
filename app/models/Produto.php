<?php
    require_once '../config/database.php';
    class Produto {
        public static function todos() {
            global $conn;
            $sql = "SELECT * FROM produtos";
            return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
        public static function salvar($dados) {
            global $conn;
            $nome = $conn->real_escape_string($dados['nome']);
            $imagem = $conn->real_escape_string($dados['imagem_url'] ?? '');
            $preco = floatval($dados['preco']);
            $conn->query("INSERT INTO produtos (nome, imagem_url, preco) VALUES ('$nome', '$imagem', $preco)");
            $produto_id = $conn->insert_id;
            if (!empty($dados['variacoes'])) {
                foreach ($dados['variacoes'] as $index => $variacao_nome) {
                    $variacao_nome = $conn->real_escape_string($variacao_nome);
                    $conn->query("INSERT INTO variacoes (produto_id, nome) VALUES ($produto_id, '$variacao_nome')");
                    $variacao_id = $conn->insert_id;
                    $estoque = intval($dados['estoques'][$index]);
                    $conn->query("INSERT INTO estoques (produto_id, variacao_id, quantidade) VALUES ($produto_id, $variacao_id, $estoque)");
                }
            } else {
                $estoque = intval($dados['estoque'] ?? 0);
                $conn->query("INSERT INTO estoques (produto_id, variacao_id, quantidade) VALUES ($produto_id, NULL, $estoque)");
            }
        }
        public static function atualizar($dados) {
            global $conn;
            $id = intval($dados['id']);
            $nome = $conn->real_escape_string($dados['nome']);
            $imagem = $conn->real_escape_string($dados['imagem_url'] ?? '');
            $preco = floatval($dados['preco']);
            $conn->query("UPDATE produtos SET nome = '$nome', imagem_url = '$imagem', preco = $preco WHERE id = $id");
            $conn->query("DELETE FROM estoques WHERE produto_id = $id");
            $conn->query("DELETE FROM variacoes WHERE produto_id = $id");
            if (!empty($dados['variacoes'])) {
                foreach ($dados['variacoes'] as $index => $variacao_nome) {
                    $variacao_nome = $conn->real_escape_string($variacao_nome);
                    $conn->query("INSERT INTO variacoes (produto_id, nome) VALUES ($id, '$variacao_nome')");
                    $variacao_id = $conn->insert_id;
                    $estoque = intval($dados['estoques'][$index]);
                    $conn->query("INSERT INTO estoques (produto_id, variacao_id, quantidade) VALUES ($id, $variacao_id, $estoque)");
                }
            } else {
                $estoque = intval($dados['estoque'] ?? 0);
                $conn->query("INSERT INTO estoques (produto_id, variacao_id, quantidade) VALUES ($id, NULL, $estoque)");
            }
        }
        public static function todosComEstoque() {
            global $conn;
            $sql = "SELECT p.id AS produto_id, p.nome, v.id AS variacao_id, v.nome AS variacao, e.quantidade FROM produtos p LEFT JOIN variacoes v ON v.produto_id = p.id LEFT JOIN estoques e ON e.produto_id = p.id AND (e.variacao_id = v.id OR (v.id IS NULL AND e.variacao_id IS NULL)) ORDER BY p.nome, v.nome";
            return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
        public static function excluir($id) {
            global $conn;
            $id = intval($id);
            $res = $conn->query("SELECT imagem_url FROM produtos WHERE id = $id");
            if ($res && $res->num_rows > 0) {
                $imagem = $res->fetch_assoc()['imagem_url'];
                if ($imagem && strpos($imagem, 'public/uploads/') === 0 && file_exists($imagem)) {
                    unlink($imagem);
                }
            }
            $conn->query("DELETE FROM estoques WHERE produto_id = $id");
            $conn->query("DELETE FROM variacoes WHERE produto_id = $id");
            $conn->query("DELETE FROM produtos WHERE id = $id");
        }
        public static function paginar($limite, $offset, $ordenar = 'nome_asc', $busca = '') {
            global $conn;
            $busca = $conn->real_escape_string($busca);
            $ordem_sql = match ($ordenar) {
                'nome_asc' => 'nome ASC',
                'nome_desc' => 'nome DESC',
                'preco_asc' => 'preco ASC',
                'preco_desc' => 'preco DESC',
                default => 'nome ASC',
            };
            $where = !empty($busca) ? "WHERE nome LIKE '%$busca%'" : "";
            $sql = "SELECT * FROM produtos $where ORDER BY $ordem_sql LIMIT $limite OFFSET $offset";
            return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
        public static function contarTodos($busca = '') {
            global $conn;
            $busca = $conn->real_escape_string($busca);
            $where = !empty($busca) ? "WHERE nome LIKE '%$busca%'" : "";
            $sql = "SELECT COUNT(*) AS total FROM produtos $where";
            $res = $conn->query($sql);
            return $res->fetch_assoc()['total'] ?? 0;
        }
    }