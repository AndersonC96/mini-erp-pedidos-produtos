<?php
    // Controllers
    require_once '../app/controllers/ProdutoController.php';
    require_once '../app/controllers/PedidoController.php';
    require_once '../app/controllers/CupomController.php';
    require_once '../app/controllers/WebhookController.php';
    // Roteamento
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
        default:
            http_response_code(404);
            echo "<h1>404 - Página não encontrada</h1>";
    }