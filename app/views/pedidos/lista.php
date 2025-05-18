<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Pedidos Realizados</h2>
    <form method="GET" class="row mb-4 g-2">
        <input type="hidden" name="rota" value="pedidos">
        <div class="col-md-4">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por produto" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Filtrar por status</option>
                <?php foreach (["pendente", "finalizado", "cancelado"] as $s): ?>
                    <option value="<?= $s ?>" <?= (($_GET['status'] ?? '') === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="ordem" class="form-select">
                <option value="">Ordenar por</option>
                <option value="mais_novo" <?= ($_GET['ordem'] ?? '') === 'mais_novo' ? 'selected' : '' ?>>Mais recentes</option>
                <option value="mais_antigo" <?= ($_GET['ordem'] ?? '') === 'mais_antigo' ? 'selected' : '' ?>>Mais antigos</option>
                <option value="maior_valor" <?= ($_GET['ordem'] ?? '') === 'maior_valor' ? 'selected' : '' ?>>Maior valor</option>
                <option value="menor_valor" <?= ($_GET['ordem'] ?? '') === 'menor_valor' ? 'selected' : '' ?>>Menor valor</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100">Aplicar Filtros</button>
        </div>
    </form>
    <?php if (!empty($_SESSION['mensagem'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensagem'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>
    <?php if (!empty($pedidos)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produtos</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td>#<?= $p['id'] ?></td>
                        <td><?= nl2br(htmlspecialchars($p['produtos_texto'])) ?></td>
                        <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                        <td><?= ucfirst($p['status']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                        <td>
                            <form method="POST" action="index.php?rota=pedido_alterar_status" class="d-flex">
                                <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                <select name="status" class="form-select form-select-sm me-2">
                                    <?php
                                        $opcoes = ['Pendente', 'Finalizado', 'Cancelado'];
                                        foreach ($opcoes as $opcao):
                                            $selecionado = strtolower($p['status']) === strtolower($opcao) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $opcao ?>" <?= $selecionado ?>><?= $opcao ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-primary">Salvar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($total_paginas > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= $pagina_atual == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?rota=pedidos&pagina=<?= $i ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&busca=<?= urlencode($_GET['busca'] ?? '') ?>&ordem=<?= urlencode($_GET['ordem'] ?? '') ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php else: ?>
        <p>Nenhum pedido encontrado.</p>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>