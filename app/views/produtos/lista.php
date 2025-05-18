<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Lista de Produtos</h2>
    <form class="row mb-3" method="GET" action="index.php">
        <input type="hidden" name="rota" value="produtos">
        <div class="col-md-4">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="ordenar" class="form-select">
                <option value="nome_asc" <?= ($_GET['ordenar'] ?? '') === 'nome_asc' ? 'selected' : '' ?>>Nome A-Z</option>
                <option value="nome_desc" <?= ($_GET['ordenar'] ?? '') === 'nome_desc' ? 'selected' : '' ?>>Nome Z-A</option>
                <option value="preco_asc" <?= ($_GET['ordenar'] ?? '') === 'preco_asc' ? 'selected' : '' ?>>Preço crescente</option>
                <option value="preco_desc" <?= ($_GET['ordenar'] ?? '') === 'preco_desc' ? 'selected' : '' ?>>Preço decrescente</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-md-2">
            <a href="index.php?rota=produtos" class="btn btn-outline-secondary w-100">Limpar</a>
        </div>
        <div class="col-md-1">
            <a href="index.php?rota=produto_form" class="btn btn-success w-100">Novo</a>
        </div>
    </form>
    <?php if (!empty($produtos)) : ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td>
                            <?php if (!empty($produto['imagem_url'])): ?>
                                <img src="<?= htmlspecialchars($produto['imagem_url']) ?>" alt="Imagem do produto" style="max-height: 80px; max-width: 80px;">
                            <?php else: ?>
                                <span class="text-muted">Sem imagem</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td>
                            <?php
                                require_once '../config/database.php';
                                global $conn;
                                $resVar = $conn->query("SELECT id, nome FROM variacoes WHERE produto_id = " . $produto['id']);
                                $variacoes = $resVar->fetch_all(MYSQLI_ASSOC);
                            ?>
                            <form action="index.php?rota=adicionar_carrinho" method="POST" class="d-inline me-2">
                                <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                                <?php if (!empty($variacoes)): ?>
                                    <select name="variacao_id" class="form-select form-select-sm d-inline w-auto me-2">
                                        <?php foreach ($variacoes as $v): ?>
                                            <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nome']) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php endif ?>
                                <input type="number" name="quantidade" value="1" min="1" class="form-control d-inline w-25 me-2" style="width: 70px;">
                                <button class="btn btn-sm btn-success">Comprar</button>
                            </form>
                            <a href="index.php?rota=produto_editar&id=<?= $produto['id'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                            <?php if (!empty($variacoes)): ?>
                                <?php foreach ($variacoes as $v): ?>
                                    <a href="index.php?rota=variacao_excluir&id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-danger mb-1" onclick="return confirm('Deseja excluir a variação \"<?= htmlspecialchars($v['nome']) ?>\"?')">
                                        ❌ <?= htmlspecialchars($v['nome']) ?>
                                    </a>
                                <?php endforeach ?>
                            <?php else: ?>
                                <a href="index.php?rota=produto_excluir&id=<?= $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <!-- Paginação -->
        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= ($i == $pagina_atual) ? 'active' : '' ?>">
                            <a class="page-link" href="?rota=produtos&pagina=<?= $i ?>&ordenar=<?= urlencode($_GET['ordenar'] ?? '') ?>&busca=<?= urlencode($_GET['busca'] ?? '') ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor ?>
                </ul>
            </nav>
        <?php endif ?>
    <?php else: ?>
        <p>Nenhum produto cadastrado.</p>
    <?php endif ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>