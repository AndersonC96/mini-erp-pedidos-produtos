<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Cupons Disponíveis</h2>
    <?php if (!empty($cupons)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Desconto (R$)</th>
                    <th>Subtotal Mínimo (R$)</th>
                    <th>Validade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cupons as $cupom): ?>
                    <tr>
                        <td><?= htmlspecialchars($cupom['codigo']) ?></td>
                        <td><?= number_format($cupom['valor_desconto'], 2, ',', '.') ?></td>
                        <td><?= number_format($cupom['minimo_subtotal'], 2, ',', '.') ?></td>
                        <td><?= date('d/m/Y', strtotime($cupom['validade'])) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum cupom cadastrado.</p>
    <?php endif ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>