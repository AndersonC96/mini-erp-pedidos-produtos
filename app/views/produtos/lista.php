<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Lista de Produtos</h2>
    <a href="index.php?rota=produto_form" class="btn btn-primary mb-3">Novo Produto</a>
    <?php if (!empty($produtos)) : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td>
                            <form action="index.php?rota=adicionar_carrinho" method="POST" class="d-inline me-2">
                                <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                                <input type="number" name="quantidade" value="1" min="1" class="form-control d-inline w-25 me-2" style="width: 70px; display: inline-block;">
                                <button class="btn btn-sm btn-success">Comprar</button>
                            </form>
                            <a href="index.php?rota=produto_editar&id=<?= $produto['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum produto cadastrado.</p>
    <?php endif ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>