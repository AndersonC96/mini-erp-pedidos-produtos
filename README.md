# 🛒 Mini ERP - Pedidos, Produtos, Cupons e Estoque

Sistema simples desenvolvido em PHP puro com padrão MVC para gerenciamento de produtos, pedidos, cupons e estoque. Projeto realizado como parte de um teste técnico.

## 🚀 Tecnologias Utilizadas

- PHP 8+
- MySQL
- Bootstrap 5 (UI responsiva)
- JavaScript (Fetch API, validações e interações)
- HTML5 / CSS3
- Sessão PHP para controle de carrinho
- Envio de e-mail com função `mail()`
- Consumo da API ViaCEP

## 📦 Funcionalidades

- CRUD de Produtos com suporte a variações (ex: tamanhos, cores)
- Controle de Estoque por produto e por variação
- Adição ao Carrinho com controle de estoque
- Cálculo de frete conforme regras:
  - Subtotal entre R$52 e R$166,59: R$15
  - Subtotal acima de R$200: Frete grátis
  - Demais casos: R$20
- Aplicação de Cupons com regras de validade e subtotal mínimo
- Finalização do Pedido com envio de e-mail e consumo da API ViaCEP
- Webhook para alteração e exclusão de pedidos via integração externa
- Listagem e gerenciamento de Pedidos e Cupons com filtros e paginação

---

## ⚙️ Instalação

1. **Clone o repositório:**
```bash
git clone https://github.com/seu-usuario/mini-erp.git
cd mini-erp
```

2. **Configure o banco de dados MySQL:**

- Crie um banco chamado mini_erp (ou outro nome de sua preferência)
- Importe o SQL abaixo:
```bash
CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  imagem_url VARCHAR(255)
);

CREATE TABLE variacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  nome VARCHAR(255) NOT NULL,
  FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE estoques (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  variacao_id INT DEFAULT NULL,
  quantidade INT NOT NULL DEFAULT 0,
  FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
  FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE CASCADE
);

CREATE TABLE cupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(50) NOT NULL UNIQUE,
  valor_desconto DECIMAL(10,2) NOT NULL,
  minimo_subtotal DECIMAL(10,2) NOT NULL,
  validade DATE NOT NULL
);

CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produtos_texto TEXT,
  total DECIMAL(10,2),
  status VARCHAR(20) DEFAULT 'pendente',
  cep VARCHAR(9),
  endereco TEXT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

3. **Configure o acesso ao banco:**

Edite o arquivo config/database.php:
```bash
<?php
    $conn = new mysqli('localhost', 'usuario', 'senha', 'mini_erp');
    if ($conn->connect_error) {
        die('Erro ao conectar ao banco: ' . $conn->connect_error);
    }
?>
```

4. Inicie o servidor local:

Se estiver usando PHP nativo:
```bash
php -S localhost:8000 -t public/
```
> Ou acesse via XAMPP em `http://localhost/mini-erp-pedidos-produtos/public/index.php`.

## 🧪 Webhook

O sistema expõe um endpoint index.php?rota=webhook que aceita POST com os seguintes campos:
`id`: ID do pedido
`status`: novo status (finalizado, cancelado, etc.)

### Comportamento:

Se `status = cancelado`, o pedido é excluído
Senão, o status do pedido é atualizado

## 📸 Demonstração

- Cadastro de Produtos
![Logo do Projeto](public/public/uploads/logo.png)
- Carrinho

- Aplicação de cupom

- Pedidos e cupons

