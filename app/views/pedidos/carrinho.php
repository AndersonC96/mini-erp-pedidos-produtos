<?php require '../app/views/shared/header.php'; ?>
<div class="container mt-4">
    <h2>Carrinho</h2>
    <?php if (!empty($_SESSION['mensagem'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['mensagem'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>
    <?php
        require_once '../config/database.php';
        require_once '../app/helpers/functions.php';
        require_once '../app/models/Cupom.php';
        global $conn;
        $carrinho = $_SESSION['carrinho'] ?? [];
        $subtotal = 0;
        $frete = 0;
        $desconto = 0;
        $mensagem_cupom = '';
    ?>
    <?php if (!empty($carrinho)): ?>
        <form method="POST" action="index.php?rota=finalizar_pedido">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrinho as $chave => $qtd): ?>
                        <?php
                            $partes = explode(':', $chave);
                            $produto_id = intval($partes[0]);
                            $variacao_id = isset($partes[1]) ? intval($partes[1]) : null;
                            $sql = "SELECT p.nome, p.preco";
                            if ($variacao_id) $sql .= ", v.nome AS variacao_nome";
                            $sql .= " FROM produtos p";
                            if ($variacao_id) $sql .= " LEFT JOIN variacoes v ON v.id = $variacao_id";
                            $sql .= " WHERE p.id = $produto_id";
                            $res = $conn->query($sql);
                            $produto = $res->fetch_assoc();
                            $nome_produto = $produto['nome'];
                            if (!empty($produto['variacao_nome'])) {
                                $nome_produto .= ' - ' . $produto['variacao_nome'];
                            }
                            $total = $produto['preco'] * $qtd;
                            $subtotal += $total;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($nome_produto) ?></td>
                            <td>
                                <form method="POST" action="index.php?rota=atualizar_qtd" class="d-flex align-items-center">
                                    <input type="hidden" name="item" value="<?= htmlspecialchars($chave) ?>">
                                    <input type="number" name="quantidade" value="<?= $qtd ?>" min="1" class="form-control form-control-sm me-2" style="max-width: 80px;">
                                    <button class="btn btn-sm btn-outline-secondary" title="Atualizar">🔄</button>
                                </form>
                            </td>
                            <td><?= formatarReais($produto['preco']) ?></td>
                            <td><?= formatarReais($total) ?></td>
                            <td>
                                <a href="index.php?rota=remover_item&item=<?= urlencode($chave) ?>" class="btn btn-sm btn-danger" title="Remover item">🗑</a>
                            </td>
<                       /tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <?php
                // Calcular frete
                if ($subtotal > 200) {
                    $frete = 0;
                } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
                    $frete = 15;
                } else {
                    $frete = 20;
                }
                // Cupom automático (verifica se já foi preenchido)
                $cupom_digitado = $_POST['cupom'] ?? '';
                if (!empty($cupom_digitado)) {
                    $dadosCupom = Cupom::validar($cupom_digitado, $subtotal);
                    if ($dadosCupom) {
                        $desconto = $dadosCupom['valor_desconto'];
                        $mensagem_cupom = "Cupom aplicado com sucesso!";
                    } else {
                        $mensagem_cupom = "Cupom inválido ou não aplicável.";
                    }
                }
                $total = $subtotal + $frete - $desconto;
            ?>
            <div class="mb-3">
                <label>CEP para entrega:</label>
                <input type="text" name="cep" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Endereço completo:</label>
                <textarea name="endereco" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label>Cupom (opcional):</label>
                <input type="text" name="cupom" class="form-control" value="<?= htmlspecialchars($cupom_usado) ?>" onchange="this.form.submit()">
            </div>
            <?php if ($mensagem_cupom): ?>
                <div class="alert alert-info"><?= htmlspecialchars($mensagem_cupom) ?></div>
            <?php endif; ?>
            <p><strong>Subtotal:</strong> <?= formatarReais($subtotal) ?></p>
            <p><strong>Frete:</strong> <?= formatarReais($frete) ?></p>
            <?php if ($desconto > 0): ?>
                <p><strong>Desconto:</strong> -<?= formatarReais($desconto) ?></p>
            <?php endif; ?>
            <p><strong>Total:</strong> <?= formatarReais($total) ?></p>
            <button type="submit" class="btn btn-success mt-3">Finalizar Pedido</button>
            <a href="index.php?rota=limpar_carrinho" class="btn btn-outline-danger mt-3 ms-2">🧹 Esvaziar Carrinho</a>
        </form>
    <?php else: ?>
        <p>Seu carrinho está vazio.</p>
    <?php endif; ?>
</div>
<?php require '../app/views/shared/footer.php'; ?>