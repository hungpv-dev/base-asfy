<?php

use App\Utils\Route;

require_once __DIR__ . '../../routes/web.php';

Route::middeware('auth')->mount('/api',function(){
    require_once __DIR__ . '../../routes/api.php';
});

Route::run();