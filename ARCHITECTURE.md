# Develix Core — Arquitetura

## Objetivo

O Develix Core é a base reutilizável para os sistemas da Develix.

Ele deve fornecer recursos genéricos, sem regras específicas de negócio.

## O que pertence ao Core

- Roteamento
- Controller base
- Model base
- Conexão com banco
- Autoload
- Sessões
- Autenticação
- Permissões
- Validação
- Upload genérico
- Logs
- Helpers
- Layouts
- Componentes reutilizáveis
- Tratamento de erros

## O que não pertence ao Core

- Produtos
- Pedidos
- Clientes
- Vendas
- Cupons
- Checkout
- Arquivos comerciais
- Regras específicas de qualquer aplicação

## Aplicações

Cada produto da Develix deve existir em um repositório próprio.

Exemplos:

- develix-market
- develix-crm
- develix-prontuario
- develix-social

Essas aplicações podem utilizar o Develix Core como dependência.

## Regra principal

Se um recurso puder ser usado por vários sistemas, ele pode pertencer ao Core.

Se um recurso existir apenas por causa de um produto específico, ele pertence à aplicação.