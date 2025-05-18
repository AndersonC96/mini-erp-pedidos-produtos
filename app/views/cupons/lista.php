<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Cupons Disponíveis</h2>
    <a href="index.php?rota=cupons" class="btn btn-success mb-3">Novo Cupom</a>
    <?php if (!empty($cupons)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Desconto (R$)</th>
                    <th>Subtotal Mínimo (R$)</th>
                    <th>Validade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cupons as $cupom): ?>
                    <tr>
                        <td><?= htmlspecialchars($cupom['codigo']) ?></td>
                        <td><?= number_format($cupom['valor_desconto'], 2, ',', '.') ?></td>
                        <td><?= number_format($cupom['minimo_subtotal'], 2, ',', '.') ?></td>
                        <td><?= date('d/m/Y', strtotime($cupom['validade'])) ?></td>
                        <td>
                            <a href="index.php?rota=cupom_editar&codigo=<?= urlencode($cupom['codigo']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="index.php?rota=cupom_excluir&codigo=<?= urlencode($cupom['codigo']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir este cupom?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum cupom cadastrado.</p>
    <?php endif ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>