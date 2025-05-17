<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Carrinho</h2>
    <?php
        require_once '../config/database.php';
        require_once '../app/helpers/functions.php';
        global $conn;
        $carrinho = $_SESSION['carrinho'] ?? [];
        $subtotal = 0;
    ?>
    <?php if (!empty($carrinho)): ?>
        <form method="POST" action="index.php?rota=finalizar_pedido">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($carrinho as $chave => $qtd):
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
                            $nome_produto = $produto['nome'];
                            if (!empty($produto['variacao_nome'])) {
                                $nome_produto .= ' - ' . $produto['variacao_nome'];
                            }
                            $total = $produto['preco'] * $qtd;
                            $subtotal += $total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($nome_produto) ?></td>
                        <td><?= $qtd ?></td>
                        <td><?= formatarReais($produto['preco']) ?></td>
                        <td><?= formatarReais($total) ?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <div class="mb-3">
                <label>CEP para entrega:</label>
                <input type="text" name="cep" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Endereço completo:</label>
                <textarea name="endereco" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label>Cupom (opcional):</label>
                <input type="text" name="cupom" class="form-control">
            </div>
            <p><strong>Subtotal:</strong> <?= formatarReais($subtotal) ?></p>
            <p><small>* O frete será calculado ao finalizar</small></p>
            <button type="submit" class="btn btn-success mt-3">Finalizar Pedido</button>
        </form>
    <?php else: ?>
        <p>Seu carrinho está vazio.</p>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>