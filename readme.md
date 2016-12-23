***L5.2Scaffold***

Com a finalidade de servir de base para novos projetos em Laravel e modularizados, o L5.2Scalfold

Esse o projeto usa o [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules), para modularização.


Instalação e Configuração

Este projeto é instalado da mesma forma do laravel comum e a adição de novos modulos é possivel ser encontrada em 
[nwidart/laravel-modules](https://github.com/nWidart/laravel-modules).

Geração de Crud (No formato API)

É possivel gerar cruds passando o nome da table e o modulo de destino.
```
$ php artisan geracrud [table] [module]
```

Os arquivos gerados
```
Modules\[Modulo]\Entities\[Table].php
Modules\[Modulo]\Repositories\[Table]Repository.php
Modules\[Modulo]\Http\Controller\[Table]Controller.php
````

Obs. No momento da geração ele também grava as rotas respectivas aos metodos gerados, para isso é preciso que o arquivo de rotas do modulo esteja previamente configurado. Confome o exemplo abaixo:

```
Route::group(['middleware' => ['web','cors'], 'prefix' => 'example_module', 'namespace' => 'Modules\ExampleModule\Http\Controllers'], function()
{

    //[CODE]
});
```

As rotas serão adicionadas acima do //[CODE].



