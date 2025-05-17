<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2><?= isset($produto) ? 'Editar Produto' : 'Cadastro de Produto' ?></h2>
    <form method="POST" action="index.php?rota=<?= isset($produto) ? 'produto_atualizar' : 'produto_salvar' ?>" enctype="multipart/form-data">
        <?php if (isset($produto)): ?>
            <input type="hidden" name="id" value="<?= $produto['id'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" required value="<?= $produto['nome'] ?? '' ?>">
        </div>
        <div class="form-group mt-3">
            <label>Preço:</label>
            <input type="number" step="0.01" name="preco" class="form-control" required value="<?= $produto['preco'] ?? '' ?>">
        </div>
        <div id="variacoes-container" class="mt-3">
            <label>Variações (opcional):</label>
            <?php if (!empty($variacoes)): ?>
                <?php foreach ($variacoes as $index => $v): ?>
                    <div class="input-group mb-2">
                        <input type="text" name="variacoes[]" class="form-control" value="<?= htmlspecialchars($v['nome']) ?>">
                        <input type="number" name="estoques[]" class="form-control" value="<?= $estoques[$v['id']] ?? 0 ?>">
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <div class="input-group mb-2">
                    <input type="text" name="variacoes[]" class="form-control" placeholder="Ex: Tamanho M">
                    <input type="number" name="estoques[]" class="form-control" placeholder="Estoque">
                </div>
            <?php endif ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="adicionarVariacao()">+ Adicionar Variação</button>
        <div class="form-group mt-3">
            <label>Estoque (caso não use variação):</label>
            <input type="number" name="estoque" class="form-control" value="<?= $estoque_simples ?? '' ?>">
        </div>
        <div class="form-group mt-3">
            <label>Imagem do Produto:</label>
            <div class="mb-2">
                <input type="text" name="imagem_url" class="form-control" placeholder="URL da imagem (https://...)" value="<?= $produto['imagem_url'] ?? '' ?>">
            </div>
            <div>
                <input type="file" name="imagem_arquivo" accept="image/*" class="form-control">
                <small class="form-text text-muted">Se você selecionar um arquivo, ele será usado no lugar do link acima.</small>
            </div>
        </div>
        <button type="submit" class="btn btn-success mt-4"><?= isset($produto) ? 'Atualizar' : 'Salvar Produto' ?></button>
        <a href="index.php?rota=produtos" class="btn btn-secondary mt-4">Voltar</a>
    </form>
</div>
<script>
    function adicionarVariacao() {
        const container = document.getElementById('variacoes-container');
        const grupo = document.createElement('div');
        grupo.className = 'input-group mb-2';
        grupo.innerHTML = `
            <input type="text" name="variacoes[]" class="form-control" placeholder="Ex: Nova Variação">
            <input type="number" name="estoques[]" class="form-control" placeholder="Estoque">
        `;
        container.appendChild(grupo);
    }
</script>
<?php require '../app/views/shared/footer.php'; ?>