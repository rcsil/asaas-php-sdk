# 📦 Asaas PHP SDK (Não Oficial)

[![Latest Version](https://img.shields.io/packagist/v/rcsil/asaas-php-sdk.svg)](https://packagist.org/packages/rcsil/asaas-php-sdk)
[![Tests](https://github.com/rcsil/asaas-php-sdk/actions/workflows/tests.yml/badge.svg)](https://github.com/rcsil/asaas-php-sdk/actions)
[![Downloads](https://img.shields.io/packagist/dt/rcsil/asaas-php-sdk.svg)](https://packagist.org/packages/rcsil/asaas-php-sdk)
[![License](https://img.shields.io/github/license/rcsil/asaas-php-sdk)](LICENSE)

---

Bem-vindo ao **Asaas PHP SDK**, uma biblioteca **não oficial** desenvolvida para facilitar a integração com a **API Asaas** utilizando PHP.

> ⚠️ **Atenção**  
> Este SDK **não é oficial**, não é mantido ou endossado pela Asaas.  
> O uso deste projeto é totalmente por sua conta e risco.

---

## ⚖️ Aviso de Responsabilidade

Ao utilizar este SDK, você concorda que:

- **Não nos responsabilizamos por erros, bugs, falhas, perdas financeiras ou danos diretos/indiretos** decorrentes do uso deste código.
- O uso desta biblioteca é **completamente voluntário** e feito **por sua própria responsabilidade**.
- O usuário deve validar e testar cuidadosamente cada funcionalidade antes de utilizar em produção.
- Atualizações na API Asaas podem tornar este SDK parcial ou totalmente incompatível.

---

## 🚧 Status do Projeto

Este projeto está em evolução contínua.  
Pull requests, sugestões e issues são sempre bem-vindas!

---

## 📥 Instalação

Instale via Composer:

```bash
composer require rcsil/asaas-php-sdk
```

---

## 🚀 Uso rápido: Clientes

```php
use Asaas\AsaasClient;

$asaas = new AsaasClient('sua-api-key', [
    // Opcional: "sandbox" para usar o ambiente de testes
    'environment' => 'production',
]);

// Listar clientes com paginação/filtros aceitos pela API
$customers = $asaas->clients()->list([
    'offset' => 0,
    'limit'  => 10,
]);

// Buscar um cliente específico
$customer = $asaas->clients()->get('cus_123456789');

// Criar um cliente
$created = $asaas->clients()->create([
    'name'    => 'Fulano de Tal',
    'email'   => 'fulano@example.com',
    'cpfCnpj' => '00000000000',
    'phone'   => '5511999999999',
]);

// Atualizar dados do cliente
$updated = $asaas->clients()->update('cus_123456789', [
    'mobilePhone' => '5511988887777',
]);

// Remover cliente
$deleted = $asaas->clients()->delete('cus_123456789');

// Notificações do cliente
$notifications = $asaas->clients()->notifications('cus_123456789');
```

**Observações**
- A chave de API é obrigatória e não pode ser vazia.
- Para mudar o ambiente, passe `'environment' => 'sandbox'` no segundo parâmetro do `AsaasClient`.
- Todas as operações retornam arrays com o conteúdo da resposta JSON da API (ou detalhes do erro em caso de falha).
