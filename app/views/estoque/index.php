<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Gerenciar Estoque</h2>
    <form method="GET" class="row g-2 mb-3">
        <input type="hidden" name="rota" value="estoque">
        <div class="col-md-4">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por produto ou variação" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="ordem" class="form-select">
                <option value="">Ordenar por...</option>
                <option value="nome_asc" <?= ($_GET['ordem'] ?? '') === 'nome_asc' ? 'selected' : '' ?>>Nome (A-Z)</option>
                <option value="nome_desc" <?= ($_GET['ordem'] ?? '') === 'nome_desc' ? 'selected' : '' ?>>Nome (Z-A)</option>
                <option value="qtd_asc" <?= ($_GET['ordem'] ?? '') === 'qtd_asc' ? 'selected' : '' ?>>Estoque (menor)</option>
                <option value="qtd_desc" <?= ($_GET['ordem'] ?? '') === 'qtd_desc' ? 'selected' : '' ?>>Estoque (maior)</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>
    <div id="mensagem-status"></div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Variação</th>
                <th>Estoque Atual</th>
                <th>Atualizar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nome']) ?></td>
                    <td><?= $item['variacao'] ?? 'Sem variação' ?></td>
                    <td><?= $item['quantidade'] ?></td>
                    <td>
                        <form method="POST" action="index.php?rota=estoque_atualizar" onsubmit="return confirmarAtualizacao(this, '<?= htmlspecialchars($item['nome']) ?>', '<?= $item['variacao'] ?? 'Sem variação' ?>')" class="d-flex">
                            <input type="hidden" name="produto_id" value="<?= $item['produto_id'] ?>">
                            <input type="hidden" name="variacao_id" value="<?= $item['variacao_id'] ?? 0 ?>">
                            <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" class="form-control me-2" style="width: 100px;">
                            <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php if ($total_paginas > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= $pagina_atual == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?rota=estoque&pagina=<?= $i ?>&busca=<?= urlencode($_GET['busca'] ?? '') ?>&ordem=<?= urlencode($_GET['ordem'] ?? '') ?>"><?= $i ?></a>
                    </li>
                <?php endfor ?>
            </ul>
        </nav>
    <?php endif ?>
</div>
<script>
    function confirmarAtualizacao(form, produto, variacao) {
        const qtd = form.querySelector('[name="quantidade"]').value;
        const confirmado = confirm(`Tem certeza que deseja atualizar o estoque de "${produto} (${variacao})" para ${qtd} unidade(s)?`);
        const divMensagem = document.getElementById('mensagem-status');
        divMensagem.innerHTML = '';
        if (!confirmado) {
            divMensagem.innerHTML = `<div class="alert alert-warning mt-3">Alteração cancelada por você.</div>`;
            return false;
        }
        localStorage.setItem("estoqueAtualizado", `${produto} (${variacao}) atualizado para ${qtd} unidade(s).`);
        return true;
    }
    document.addEventListener("DOMContentLoaded", function () {
        const mensagem = localStorage.getItem("estoqueAtualizado");
        if (mensagem) {
            const div = document.getElementById('mensagem-status');
            div.innerHTML = `<div class="alert alert-success mt-3">${mensagem}</div>`;
            localStorage.removeItem("estoqueAtualizado");
        }
    });
</script>
<?php require '../app/views/shared/footer.php'; ?>