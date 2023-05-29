<?php


$api = app('Dingo\Api\Routing\Router');

use App\Http\Controllers\Home;

$api->group([
    'middleware' => 'cors',
    'version' => 'v1',
], function ($api) {
    $api->group([
        'middleware' => ['throttle:60', 'api'],
        // 'limit' => config('api.rate_limits.sign.limit'),#接口访问限制
        // 'expires' => config('api.rate_limits.sign.expires'), #接口过期时间
    ], function ($api) {
        # 无需校验token的接口
        $api->group(['prefix' => 'v1'], function ($api) {
            $api->get('index', [Home\ApiController::class, 'index'])->name('users.index');
            // 需要 token 验证的接口
            $api->group(['middleware' => ['jwt']], function ($api) {
                $api->post('index', [Home\ApiController::class, 'index'])->name('users.index');
            });
        });
    });
});
