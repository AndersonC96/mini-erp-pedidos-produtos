<?php
    require_once '../app/controllers/ProdutoController.php';
    require_once '../app/controllers/PedidoController.php';
    require_once '../app/controllers/CupomController.php';
    require_once '../app/controllers/WebhookController.php';
    require_once '../app/controllers/EstoqueController.php';
    $rota = $_GET['rota'] ?? 'produtos';
    switch ($rota) {
        case 'produtos':
            (new ProdutoController())->index();
            break;
        case 'produto_form':
            (new ProdutoController())->form();
            break;
        case 'produto_salvar':
            (new ProdutoController())->salvar();
            break;
        case 'carrinho':
            (new PedidoController())->carrinho();
            break;
        case 'adicionar_carrinho':
            (new PedidoController())->adicionar();
            break;
        case 'finalizar_pedido':
            (new PedidoController())->finalizar();
            break;
        case 'webhook':
            (new WebhookController())->receber();
            break;
        case 'cupons':
            (new CupomController())->form();
            break;
        case 'cupom_salvar':
            (new CupomController())->salvar();
            break;
        case 'estoque':
            (new EstoqueController())->index();
            break;
        case 'estoque_atualizar':
            (new EstoqueController())->atualizar();
            break;
        case 'produto_editar':
            (new ProdutoController())->editar();
            break;
        case 'produto_atualizar':
            (new ProdutoController())->atualizar();
            break;
        default:
            http_response_code(404);
            echo "<h1>404 - Página não encontrada</h1>";
    }