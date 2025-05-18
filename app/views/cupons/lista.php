<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Cupons Cadastrados</h2>
    <form method="GET" class="row mb-4 g-2">
        <input type="hidden" name="rota" value="cupons_listar">
        <div class="col-md-4">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por código" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="ordenar" class="form-select">
                <option value="">Ordenar por</option>
                <option value="valor_maior" <?= ($_GET['ordenar'] ?? '') === 'valor_maior' ? 'selected' : '' ?>>Maior Desconto</option>
                <option value="valor_menor" <?= ($_GET['ordenar'] ?? '') === 'valor_menor' ? 'selected' : '' ?>>Menor Desconto</option>
                <option value="validade_menor" <?= ($_GET['ordenar'] ?? '') === 'validade_menor' ? 'selected' : '' ?>>Mais Próximo do Vencimento</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100">Filtrar</button>
        </div>
    </form>
    <?php if (!empty($_SESSION['mensagem'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensagem'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>
    <?php if (!empty($cupons)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Desconto</th>
                    <th>Subtotal Mínimo</th>
                    <th>Validade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cupons as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['codigo']) ?></td>
                        <td>R$ <?= number_format($c['valor_desconto'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($c['minimo_subtotal'], 2, ',', '.') ?></td>
                        <td><?= date('d/m/Y', strtotime($c['validade'])) ?></td>
                        <td>
                            <a href="index.php?rota=cupom_editar&codigo=<?= urlencode($c['codigo']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="index.php?rota=cupom_excluir&codigo=<?= urlencode($c['codigo']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este cupom?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum cupom encontrado.</p>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>