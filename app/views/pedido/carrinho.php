<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Carrinho</h2>
    <?php
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
                    require_once '../config/database.php';
                    foreach ($carrinho as $produto_id => $qtd):
                        $res = $conn->query("SELECT nome, preco FROM produtos WHERE id = $produto_id");
                        $produto = $res->fetch_assoc();
                        $total = $produto['preco'] * $qtd;
                        $subtotal += $total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= $qtd ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($total, 2, ',', '.') ?></td>
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
            <p><strong>Subtotal:</strong> R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
            <p><small>* O frete será calculado ao finalizar</small></p>
            <button type="submit" class="btn btn-success mt-3">Finalizar Pedido</button>
        </form>
    <?php else: ?>
        <p>Seu carrinho está vazio.</p>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>