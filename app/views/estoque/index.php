<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Gerenciar Estoque</h2>
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
                        <form method="POST" action="index.php?rota=estoque_atualizar" class="d-flex">
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
</div>
<?php require '../app/views/shared/footer.php'; ?>