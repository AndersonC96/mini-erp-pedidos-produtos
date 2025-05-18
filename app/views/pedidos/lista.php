<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Pedidos Realizados</h2>
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
                                    <option value="cancelado" <?= $p['status'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                    <option value="entregue" <?= $p['status'] == 'entregue' ? 'selected' : '' ?>>Entregue</option>
                                    <option value="enviado" <?= $p['status'] == 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                    <option value="pendente" <?= $p['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                </select>
                                <button class="btn btn-sm btn-primary">Salvar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum pedido encontrado.</p>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>