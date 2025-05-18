<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2><?= isset($cupom) ? 'Editar Cupom' : 'Cadastrar Cupom' ?></h2>
    <?php if (!empty($_SESSION['mensagem'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensagem'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>
    <form method="POST" action="index.php?rota=cupom_salvar">
        <div class="form-group">
            <label>Código do Cupom:</label>
            <input type="text" name="codigo" class="form-control" required value="<?= htmlspecialchars($cupom['codigo'] ?? '') ?>"<?= isset($cupom) ? 'readonly' : '' ?>>
        </div>
        <div class="form-group mt-3">
            <label>Valor de Desconto (R$):</label>
            <input type="number" step="0.01" name="valor_desconto" class="form-control" required value="<?= htmlspecialchars($cupom['valor_desconto'] ?? '') ?>">
        </div>
        <div class="form-group mt-3">
            <label>Subtotal Mínimo (R$):</label>
            <input type="number" step="0.01" name="minimo_subtotal" class="form-control" required value="<?= htmlspecialchars($cupom['minimo_subtotal'] ?? '') ?>">
        </div>
        <div class="form-group mt-3">
            <label>Validade:</label>
            <input type="date" name="validade" class="form-control" required value="<?= htmlspecialchars($cupom['validade'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-success mt-4">Salvar Cupom</button>
        <a href="index.php?rota=cupons_listar" class="btn btn-secondary mt-4">Voltar</a>
    </form>
</div>
<?php require '../app/views/shared/footer.php'; ?>