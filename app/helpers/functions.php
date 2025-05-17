<?php
    /**
    * Consulta dados de um CEP na API ViaCEP.
    *
    * @param string $cep
    * @return array|null
    */
    function buscarCep($cep) {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        if (strlen($cep) !== 8) {
            return null;
        }
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $response = @file_get_contents($url);
        if (!$response) return null;
        $dados = json_decode($response, true);
        return isset($dados['erro']) ? null : $dados;
    }
    /**
    * Calcula o valor do frete baseado no subtotal.
    *
    * @param float $subtotal
    * @return float
    */
    function calcularFrete($subtotal) {
        if ($subtotal > 200) {
            return 0.00;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }
    /**
    * Formata um valor monetário em reais.
    *
    * @param float $valor
    * @return string
    */
    function formatarReais($valor) {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
    /**
    * Redireciona para uma rota com header.
    *
    * @param string $rota
    */
    function redirecionar($rota) {
        header("Location: index.php?rota={$rota}");
        exit;
    }