***Core Serviços AB***

```
$ git clone https://gitlab.com/mouraagentebrasil/coreab
$ cd servico_client && cp env.example .env
$ php artisan key:generate
```

Informações Úteis: 

Criando eventos e modulos para eventos

```
$ php artisan module:make-event SomethingWasCreated
$ php artisan module:make-listener DoSomething --event=SomethingWasCreate
``