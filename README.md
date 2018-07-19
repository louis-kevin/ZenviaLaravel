# ZenviaLaravel

Pacote para enviar sms com o zenvia

## Instalação

- composer require louisk/zenvia
- php artisan vendor:publish --provider=Louis\Zenvia\Providers\ZenviaServiceProvider
- Adicione os seguintes itens no seu .env
```
ZENVIA_ACCOUNT=XXXXXX
ZENVIA_PASSWORD=XXXXX
ZENVIA_FROM=XXXXX
```

## Testando Credenciais
Para testar se tudo está funcionando, vá em seu terminal e rode o seguinte comando
```
php artisan zenvia:sms 5541999999999 teste
```
Você deverá receber um sms neste momento

## Utilização

### Facade
Para utilizar de forma rápida, usando o facade, você deverá utilizar da seguinte maneira
- Para envio para um numero
```php
  \Zenvia::sendMessage('5541999999999', 'Mensagem Teste');
```

- Para envio para um ou mais numeros (Esta função aceita String, Array ou Collection)
```php
  \Zenvia::sendMessage(['5541999999999', '5541999999999'], 'Mensagem Teste');
```

### Manualmente
Para utilizar o zenvia com mais opções de configurações, você pode instanciar a service

```php
try {
    $zenvia = new Zenvia(config('zenvia.account'), config('zenvia.password'));

    $zenvia->setNumber('5541999999999')
        ->setNumber(['5541999999999', '5541999999999'])
        ->setNumber(collect(['5541999999999', '5541999999999']))
        ->setText('Mensagem Teste')
        ->send();
} catch (AuthenticationNotFoundedException $e) {
    // Some code
} catch (FieldMissingException $e) {
    // Some code
} catch (RequestException $e) {
    // Some code
}
```



