<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary mb-0">🎟️ Cupons Cadastrados</h2>
        <a href="index.php?rota=cupons" class="btn btn-success">➕ Novo Cupom</a>
    </div>
    <form method="GET" class="card card-body shadow-sm mb-4">
        <input type="hidden" name="rota" value="cupons_listar">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">🔍 Buscar por código</label>
                <input type="text" name="busca" class="form-control" placeholder="Ex: PROMO10" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">📊 Ordenar por</label>
                <select name="ordenar" class="form-select">
                    <option value="">Selecionar</option>
                    <option value="valor_maior" <?= ($_GET['ordenar'] ?? '') === 'valor_maior' ? 'selected' : '' ?>>Maior Desconto</option>
                    <option value="valor_menor" <?= ($_GET['ordenar'] ?? '') === 'valor_menor' ? 'selected' : '' ?>>Menor Desconto</option>
                    <option value="validade_menor" <?= ($_GET['ordenar'] ?? '') === 'validade_menor' ? 'selected' : '' ?>>Mais Próximo do Vencimento</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">🔎 Filtrar</button>
            </div>
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
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Desconto</th>
                        <th>Subtotal Mínimo</th>
                        <th>Validade</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cupons as $c): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['codigo']) ?></strong></td>
                            <td><span class="badge bg-success">R$ <?= number_format($c['valor_desconto'], 2, ',', '.') ?></span></td>
                            <td><span class="badge bg-secondary">R$ <?= number_format($c['minimo_subtotal'], 2, ',', '.') ?></span></td>
                            <td><span class="badge bg-light text-dark"><?= date('d/m/Y', strtotime($c['validade'])) ?></span></td>
                            <td class="text-center">
                                <a href="index.php?rota=cupom_editar&codigo=<?= urlencode($c['codigo']) ?>" class="btn btn-sm btn-warning me-1">
                                    ✏️ Editar
                                </a>
                                <a href="index.php?rota=cupom_excluir&codigo=<?= urlencode($c['codigo']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir este cupom?')">
                                    🗑️ Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">Nenhum cupom encontrado.</div>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>