<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Cadastrar Cupom</h2>
    <form method="POST" action="index.php?rota=cupom_salvar">
        <div class="form-group">
            <label>Código do Cupom:</label>
            <input type="text" name="codigo" class="form-control" required>
        </div>
        <div class="form-group mt-3">
            <label>Valor de Desconto (R$):</label>
            <input type="number" step="0.01" name="valor_desconto" class="form-control" required>
        </div>
        <div class="form-group mt-3">
            <label>Subtotal Mínimo (R$):</label>
            <input type="number" step="0.01" name="minimo_subtotal" class="form-control" required>
        </div>
        <div class="form-group mt-3">
            <label>Validade:</label>
            <input type="date" name="validade" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success mt-4">Salvar Cupom</button>
        <a href="index.php?rota=produtos" class="btn btn-secondary mt-4">Voltar</a>
    </form>
</div>
<?php require '../app/views/shared/footer.php'; ?>