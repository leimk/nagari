<?php
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Generate App Key
$router->get('/key', function () {
    return str_random(32);
});

$router->get('/foo', function(){
    return 'Hello, GET Method!';
});

$router->post('/bar', function(){
    return 'Hello, POST MEthod!';
});

$router->group(['prefix'=> 'in'], function() use($router){
    $router->post('kirim', function(Request $request){
      

    });

});
