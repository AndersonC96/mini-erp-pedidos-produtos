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
    /**
    * Registra uma mensagem no log do sistema.
    *
    * @param string $mensagem
    * @param string $arquivo
    */
    function registrarLog($mensagem, $arquivo = '../storage/logs/app.log') {
        $linha = "[" . date('Y-m-d H:i:s') . "] " . $mensagem . PHP_EOL;
        file_put_contents($arquivo, $linha, FILE_APPEND);
    }
    /**
    * Simula o envio de e-mail e grava log.
    *
    * @param string $para
    * @param string $assunto
    * @param string $mensagem
    */
    function simularEnvioEmail($para, $assunto, $mensagem) {
        $log = "Para: $para\nAssunto: $assunto\nMensagem:\n$mensagem\n----------------------\n";
        file_put_contents('../storage/emails/mail.log', $log, FILE_APPEND);
    }