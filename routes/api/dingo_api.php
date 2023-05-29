<?php


$api = app('Dingo\Api\Routing\Router');

use App\Http\Controllers\Home;

//dingo api explame
// $api->version('v1', function ($api) {
//     $api->group(['middleware' => 'cors'], function ($api) {
//         $api->group(['prefix' => 'v1'], function ($api) {
//             $api->resources('index', [Home\ApiController::class, 'index'])->name('users.index');
//         });
//     });
// });

$api->group([
    'version' => 'v1',
    'prefix'  => 'api',
    // 'domain' => env('APP_DOMAIN'),
    // 'namespace' => 'App\\Http\\Controllers'
], function ($api) {
    $api->version(['version' => 'v1'], ['middleware' => ['cors']], function ($api) {
        $api->group(['prefix' => 'v1'], function ($api) {
            $api->get('index', [Home\ApiController::class, 'index'])->name('users.index');
        });
    });
});


// $api->version('v1', function ($api) {
//     // $api->version('v1', ['middleware' => ['throttle:60', 'api']], function ($api) {
//     //     $api->post('index', [Home\ApiController::class, 'index'])->name('users.index');
//     // });
//
//     $api->group(['middleware' => 'cors'], function ($api) {
//         $api->group(['prefix' => 'v1'], function ($api) {
//             $api->get('index', [Home\ApiController::class, 'index'])->name('users.index');
//         });
//     });
// });

