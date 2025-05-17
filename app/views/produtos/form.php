<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Cadastro de Produto</h2>
    <form method="POST" action="index.php?rota=produto_salvar">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="form-group mt-3">
            <label>Preço:</label>
            <input type="number" step="0.01" name="preco" class="form-control" required>
        </div>
        <div id="variacoes-container" class="mt-3">
            <label>Variações (opcional):</label>
            <div class="input-group mb-2">
                <input type="text" name="variacoes[]" class="form-control" placeholder="Ex: Tamanho M">
                <input type="number" name="estoques[]" class="form-control" placeholder="Estoque">
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-outline-primary" onclick="adicionarVariacao()">+ Adicionar Variação</button>

        <div class="form-group mt-3">
            <label>Estoque (caso não use variação):</label>
            <input type="number" name="estoque" class="form-control">
        </div>

        <button type="submit" class="btn btn-success mt-4">Salvar Produto</button>
        <a href="index.php?rota=produtos" class="btn btn-secondary mt-4">Voltar</a>
    </form>
</div>
<script>
    function adicionarVariacao() {
        const container = document.getElementById('variacoes-container');
        const grupo = document.createElement('div');
        grupo.className = 'input-group mb-2';
        grupo.innerHTML = `
            <input type="text" name="variacoes[]" class="form-control" placeholder="Ex: Tamanho G">
            <input type="number" name="estoques[]" class="form-control" placeholder="Estoque">
        `;
        container.appendChild(grupo);
    }
</script>
<?php require '../app/views/shared/footer.php'; ?>