<?php
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {
    return response()->json([
        'service_name' => 'PHP Service App',
        'status' => 'Running',
    ]);
});

$router->get('/hello-lumen', function () {
    return "<h1>Lumen</h1><p>Hi good developer, thank for using Lumen</p>";
});

$router->get('/hello-lumen/{name}', function ($name) {
    return "<h1>Lumen</h1><p>Hi <b>" . $name . "</b>, thank for using Lumen</p>";
});

$router->get('/scores', ['middleware' => 'login', function () {
    return "<h1>Selamat</h1><p>Nilai anda 100</p>";
}]);

$router->get('users', 'UsersController@index');
$router->get('users/{userId}', 'UsersController@show');

$router->get('posts', 'PostsController@index');
$router->get('posts/{id}', 'PostsController@show');
$router->post('posts', 'PostsController@store');
$router->put('posts/{id}', 'PostsController@update');
$router->delete('posts/{id}', 'PostsController@destroy');

$router->get('/products', 'ProductsController@index');
$router->post('/products', 'ProductsController@store');
$router->get('/product/{id}', 'ProductsController@show');
$router->put('/product/{id}', 'ProductsController@update');
$router->delete('/product/{id}', 'ProductsController@destroy');