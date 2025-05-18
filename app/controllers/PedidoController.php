<?php
    class PedidoController {
        public function carrinho() {
            require '../app/views/pedidos/carrinho.php';
        }
        public function adicionar() {
            $produto_id = intval($_POST['produto_id']);
            $quantidade = intval($_POST['quantidade']);
            $variacao_id = isset($_POST['variacao_id']) ? intval($_POST['variacao_id']) : null;
            $chave = $variacao_id ? "{$produto_id}:{$variacao_id}" : "{$produto_id}";
            $_SESSION['carrinho'][$chave] = ($_SESSION['carrinho'][$chave] ?? 0) + $quantidade;
            header('Location: index.php?rota=carrinho');
            exit;
        }
        public function finalizar() {
            require_once '../app/models/Pedido.php';
            require_once '../config/database.php';
            global $conn;
            $email = $_POST['email'] ?? null;
            $cep = $_POST['cep'];
            $endereco = $_POST['endereco'];
            $cupom = $_POST['cupom'] ?? null;
            $pedido_id = Pedido::criar($_SESSION['carrinho'], $cep, $endereco, $cupom);
            $mensagem = "Seu pedido #$pedido_id foi finalizado com sucesso!\n\n";
            $mensagem .= "Endereço de entrega: $endereco\n";
            $mensagem .= "CEP: $cep\n\n";
            $mensagem .= "Itens do pedido:\n";
            $subtotal = 0;
            foreach ($_SESSION['carrinho'] as $chave => $qtd) {
                $partes = explode(':', $chave);
                $produto_id = intval($partes[0]);
                $variacao_id = isset($partes[1]) ? intval($partes[1]) : null;
                $sql = "SELECT p.nome, p.preco";
                if ($variacao_id) $sql .= ", v.nome AS variacao_nome";
                $sql .= " FROM produtos p";
                if ($variacao_id) $sql .= " LEFT JOIN variacoes v ON v.id = $variacao_id";
                $sql .= " WHERE p.id = $produto_id";
                $res = $conn->query($sql);
                $produto = $res->fetch_assoc();
                $nome_produto = $produto['nome'];
                if (!empty($produto['variacao_nome'])) {
                    $nome_produto .= " - " . $produto['variacao_nome'];
                }
                $preco = $produto['preco'];
                $total = $preco * $qtd;
                $subtotal += $total;
                $mensagem .= "- $nome_produto | Quantidade: $qtd | Total: R$ " . number_format($total, 2, ',', '.') . "\n";
            }
            if ($subtotal > 200) $frete = 0;
            elseif ($subtotal >= 52 && $subtotal <= 166.59) $frete = 15;
            else $frete = 20;
            $desconto = 0;
            if ($cupom) {
                require_once '../app/models/Cupom.php';
                $cupom_validado = Cupom::validar($cupom, $subtotal);
                if ($cupom_validado) {
                    $desconto = $cupom_validado['valor_desconto'];
                }
            }
            $total_geral = $subtotal + $frete - $desconto;
            $mensagem .= "\nSubtotal: R$ " . number_format($subtotal, 2, ',', '.');
            $mensagem .= "\nFrete: R$ " . number_format($frete, 2, ',', '.');
            if ($desconto > 0) {
                $mensagem .= "\nDesconto: -R$ " . number_format($desconto, 2, ',', '.');
            }
            $mensagem .= "\nTotal do pedido: R$ " . number_format($total_geral, 2, ',', '.');
            $mensagem .= "\n\nObrigado por comprar conosco!";
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $assunto = "Confirmação do Pedido #$pedido_id";
                $headers = "From: pedidos@mini-erp.com.br\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8";
                @mail($email, $assunto, $mensagem, $headers);
            }
            unset($_SESSION['carrinho']);
            require '../app/views/pedidos/sucesso.php';
        }
        public function remover() {
            $chave = $_GET['item'] ?? null;
            if ($chave && isset($_SESSION['carrinho'][$chave])) {
                unset($_SESSION['carrinho'][$chave]);
                $_SESSION['mensagem'] = 'Item removido com sucesso!';
            }
            header('Location: index.php?rota=carrinho');
            exit;
        }
        public function limpar() {
            unset($_SESSION['carrinho']);
            $_SESSION['mensagem'] = 'Carrinho esvaziado com sucesso!';
            header('Location: index.php?rota=carrinho');
            exit;
        }
        public function lista() {
            require_once '../app/models/Pedido.php';
            $busca = $_GET['busca'] ?? '';
            $status = $_GET['status'] ?? '';
            $ordenar = $_GET['ordem'] ?? 'desc'; // corrigido: 'ordem' no formulário
            $pagina = max(1, intval($_GET['pagina'] ?? 1));
            $limite = 10;
            $resultado = Pedido::todosFiltrado($busca, $status, $ordenar, $pagina, $limite); // <-- ORDEM CORRIGIDA
            $pedidos = $resultado['dados'];
            $total_paginas = $resultado['total_paginas'];
            $pagina_atual = $pagina;
            require '../app/views/pedidos/lista.php';
        }
        public function alterarStatus() {
            require_once '../app/models/Pedido.php';
            $id = intval($_POST['pedido_id']);
            $novo_status = $_POST['status'];
            Pedido::alterarStatus($id, $novo_status);
            $_SESSION['mensagem'] = "Status do pedido alterado!";
            header('Location: index.php?rota=pedidos');
            exit;
        }
    }