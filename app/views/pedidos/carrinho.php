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
        <form method="POST" action="index.php?rota=finalizar_pedido" id="form-finalizar">
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
                <tbody id="carrinho-tabela">
                    <?php foreach ($carrinho as $chave => $qtd): ?>
                        <?php
                            $partes = explode(':', $chave);
                            $produto_id = intval($partes[0]);
                            $variacao_id = isset($partes[1]) ? intval($partes[1]) : null;
                            // Consultar estoque da variação ou produto
                            if ($variacao_id) {
                                $estoque_res = $conn->query("SELECT quantidade FROM estoques WHERE produto_id = $produto_id AND variacao_id = $variacao_id");
                                $estoque = $estoque_res->fetch_assoc()['quantidade'] ?? 0;
                            } else {
                                $estoque_res = $conn->query("SELECT quantidade FROM estoques WHERE produto_id = $produto_id AND variacao_id IS NULL");
                                $estoque = $estoque_res->fetch_assoc()['quantidade'] ?? 0;
                            }
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
                        <tr data-key="<?= htmlspecialchars($chave) ?>" data-estoque="<?= $estoque ?>">
                            <td><?= htmlspecialchars($nome_produto) ?></td>
                            <td>
                                <input type="number" min="1" max="<?= $estoque ?>" value="<?= $qtd ?>" class="form-control form-control-sm qtd-input" style="max-width: 80px;">
                                <span class="badge bg-light text-dark estoque-info" style="font-size:12px;">(Estoque: <?= $estoque ?>)</span>
                            </td>
                            <td><?= formatarReais($produto['preco']) ?></td>
                            <td class="total-item"><?= formatarReais($total) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-remover">🗑</button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <div class="mb-3">
                <label>CEP para entrega:</label>
                <input type="text" name="cep" class="form-control" id="campo-cep" required maxlength="9" pattern="\d{5}-?\d{3}">
            </div>
            <div class="mb-3">
                <label>Endereço completo:</label>
                <textarea name="endereco" class="form-control" id="campo-endereco" required></textarea>
            </div>
            <div class="mb-3">
                <label>Cupom (opcional):</label>
                <div class="input-group">
                    <input type="text" name="cupom" class="form-control" id="campo-cupom">
                    <button type="button" class="btn btn-outline-primary" id="btn-aplicar-cupom">Aplicar</button>
                </div>
            </div>
            <div id="mensagem-cupom" class="mb-2 text-info"></div>
            <p><strong>Subtotal:</strong> <span id="valor-subtotal"><?= formatarReais($subtotal) ?></span></p>
            <p><strong>Frete:</strong> <span id="valor-frete">-</span></p>
            <p id="linha-desconto" style="display: none;"><strong>Desconto:</strong> <span id="valor-desconto"></span></p>
            <p><strong>Total:</strong> <span id="valor-total">-</span></p>
            <button type="button" class="btn btn-success mt-3" onclick="confirmarFinalizacao()">Finalizar Pedido</button>
            <a href="index.php?rota=limpar_carrinho" class="btn btn-outline-danger mt-3 ms-2">🧹 Esvaziar Carrinho</a>
        </form>
    <?php else: ?>
        <p>Seu carrinho está vazio.</p>
    <?php endif; ?>
</div>
<script>
// --- 1. Validação de Estoque ao alterar quantidade ---
document.querySelectorAll('.qtd-input').forEach(input => {
    input.addEventListener('change', function () {
        const tr = this.closest('tr');
        const maxEstoque = parseInt(tr.dataset.estoque);
        let novaQtd = parseInt(this.value);
        if (novaQtd > maxEstoque) {
            alert('Quantidade solicitada maior que o estoque disponível!');
            this.value = maxEstoque;
            novaQtd = maxEstoque;
        } else if (novaQtd < 1) {
            this.value = 1;
            novaQtd = 1;
        }
        const key = tr.dataset.key;
        fetch(`index.php?rota=atualizar_qtd`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `item=${encodeURIComponent(key)}&quantidade=${novaQtd}`
        }).then(() => {
            recalcularTotais();
        });
    });
});
// --- 2. Aplicação de Cupom com AJAX e atualização do total ---
function aplicarCupom() {
    const cupom = document.getElementById('campo-cupom').value.trim();
    const subtotal = parseFloat(document.getElementById('valor-subtotal').innerText.replace('R$','').replace(',','.'));
    if (cupom === '') {
        document.getElementById('mensagem-cupom').innerText = '';
        document.getElementById('linha-desconto').style.display = 'none';
        recalcularTotais();
        return;
    }
    fetch(`index.php?rota=validar_cupom`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `cupom=${encodeURIComponent(cupom)}&subtotal=${subtotal}`
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.valido) {
            document.getElementById('mensagem-cupom').innerText = 'Cupom aplicado com sucesso!';
            document.getElementById('linha-desconto').style.display = 'block';
            document.getElementById('valor-desconto').innerText = `- R$ ${parseFloat(data.desconto).toFixed(2).replace('.', ',')}`;
            recalcularTotais(parseFloat(data.desconto));
        } else {
            document.getElementById('mensagem-cupom').innerText = data.mensagem || 'Cupom inválido!';
            document.getElementById('linha-desconto').style.display = 'none';
            recalcularTotais(0);
        }
    })
    .catch(() => {
        document.getElementById('mensagem-cupom').innerText = 'Erro ao validar cupom!';
        document.getElementById('linha-desconto').style.display = 'none';
        recalcularTotais(0);
    });
}
document.getElementById('campo-cupom').addEventListener('blur', aplicarCupom);
document.getElementById('btn-aplicar-cupom').addEventListener('click', aplicarCupom);
// --- 3. Busca de endereço por CEP (ViaCEP) ---
document.getElementById('campo-cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res => res.json())
        .then(data => {
            if (!data.erro) {
                const endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                document.getElementById('campo-endereco').value = endereco;
            }
        });
    }
});
// --- Remover item com confirmação ---
document.querySelectorAll('.btn-remover').forEach(btn => {
    btn.addEventListener('click', function () {
        const tr = this.closest('tr');
        const nome = tr.querySelector('td').innerText;
        if (confirm(`Deseja realmente remover "${nome}" do carrinho?`)) {
            const key = tr.dataset.key;
            window.location.href = `index.php?rota=remover_item&item=${encodeURIComponent(key)}`;
        }
    });
});
// --- Confirmação antes de finalizar ---
function confirmarFinalizacao() {
    if (confirm('Tem certeza de que deseja finalizar o pedido?')) {
        document.getElementById('form-finalizar').submit();
    }
}
// --- Recalcula totais, considerando desconto se informado ---
function recalcularTotais(forceDesconto = null) {
    const rows = document.querySelectorAll('#carrinho-tabela tr');
    let subtotal = 0;
    rows.forEach(row => {
        const preco = parseFloat(row.querySelector('td:nth-child(3)').innerText.replace('R$','').replace(',','.'));
        const qtd = parseInt(row.querySelector('input.qtd-input').value);
        const total = preco * qtd;
        subtotal += total;
        row.querySelector('.total-item').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
    });
    // Frete
    let frete = 0;
    if (subtotal > 200) frete = 0;
    else if (subtotal >= 52 && subtotal <= 166.59) frete = 15;
    else frete = 20;
    document.getElementById('valor-subtotal').innerText = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('valor-frete').innerText = `R$ ${frete.toFixed(2).replace('.', ',')}`;
    // Desconto
    let desconto = forceDesconto;
    if (desconto === null) {
        // Caso o desconto ainda não foi retornado, tentar extrair da tela
        const descontoText = document.getElementById('valor-desconto').innerText.replace('- R$','').replace(',','.');
        desconto = parseFloat(descontoText) || 0;
    }
    const total = subtotal + frete - desconto;
    document.getElementById('valor-total').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
}
// --- Inicialização ---
recalcularTotais();
</script>
<?php require '../app/views/shared/footer.php'; ?>