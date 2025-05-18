<?php require '../app/views/shared/header.php'; ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h3 class="card-title text-primary mb-3">🎒 Meu Carrinho</h3>
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
        <?php if (!empty($_SESSION['mensagem'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensagem'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>
        <form method="POST" action="index.php?rota=finalizar_pedido" id="form-finalizar">
            <div class="table-responsive mb-3">
                <table class="table table-hover align-middle shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th style="width: 120px;">Qtd</th>
                            <th>Preço</th>
                            <th>Total</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="carrinho-tabela">
                        <?php foreach ($carrinho as $chave => $qtd): ?>
                            <?php
                                $partes = explode(':', $chave);
                                $produto_id = intval($partes[0]);
                                $variacao_id = isset($partes[1]) ? intval($partes[1]) : null;
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
                                    <input type="number" min="1" max="<?= $estoque ?>" value="<?= $qtd ?>" class="form-control form-control-sm qtd-input" style="width: 70px;">
                                    <span class="badge bg-light text-dark d-block mt-1 estoque-info" style="font-size:12px;">Estoque: <?= $estoque ?></span>
                                </td>
                                <td><?= formatarReais($produto['preco']) ?></td>
                                <td class="total-item"><?= formatarReais($total) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover">🖑</button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <hr class="my-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">📍 CEP para entrega</label>
                    <input type="text" name="cep" class="form-control" id="campo-cep" required maxlength="9" pattern="\d{5}-?\d{3}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">📦 Endereço completo</label>
                    <textarea name="endereco" class="form-control" id="campo-endereco" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">📧 E-mail para envio do pedido</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">🏷 Cupom (opcional)</label>
                    <div class="input-group">
                        <input type="text" name="cupom" class="form-control" id="campo-cupom" placeholder="Digite seu cupom...">
                        <button type="button" class="btn btn-outline-primary" id="btn-aplicar-cupom">Aplicar</button>
                    </div>
                </div>
            </div>
            <div id="mensagem-cupom" class="mt-2 text-info"></div>
            <hr class="my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3">
                    <p class="mb-1"><strong>Subtotal:</strong> <span id="valor-subtotal"><?= formatarReais($subtotal) ?></span></p>
                    <p class="mb-1"><strong>Frete:</strong> <span id="valor-frete">-</span></p>
                    <p class="mb-1" id="linha-desconto" style="display: none;"><strong>Desconto:</strong> <span id="valor-desconto"></span></p>
                    <p class="fs-5"><strong>Total:</strong> <span id="valor-total">-</span></p>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-success btn-lg" onclick="confirmarFinalizacao()">✅ Finalizar Pedido</button>
                    <a href="index.php?rota=limpar_carrinho" class="btn btn-outline-danger btn-lg ms-2">🪟 Esvaziar</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
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
    function confirmarFinalizacao() {
        if (confirm('Tem certeza de que deseja finalizar o pedido?')) {
            document.getElementById('form-finalizar').submit();
        }
    }
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
        let frete = 0;
        if (subtotal > 200) frete = 0;
        else if (subtotal >= 52 && subtotal <= 166.59) frete = 15;
        else frete = 20;
        document.getElementById('valor-subtotal').innerText = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        document.getElementById('valor-frete').innerText = `R$ ${frete.toFixed(2).replace('.', ',')}`;
        let desconto = forceDesconto;
        if (desconto === null) {
            const descontoText = document.getElementById('valor-desconto').innerText.replace('- R$','').replace(',','.');
            desconto = parseFloat(descontoText) || 0;
        }
        const total = subtotal + frete - desconto;
        document.getElementById('valor-total').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
    }
    recalcularTotais();
</script>
<?php require '../app/views/shared/footer.php'; ?>