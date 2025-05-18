<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Pedidos Realizados</h2>
    <?php if (!empty($pedidos)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produtos</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td>#<?= $p['id'] ?></td>
                        <td><?= nl2br(htmlspecialchars($p['produtos_texto'])) ?></td>
                        <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                        <td><?= ucfirst($p['status']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum pedido encontrado.</p>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>