<?php 
namespace App\Middleware;
class Kernel{

    public $middlewareBefore = [
        CheckSessionUser::class,
        HandleBefore::class
    ];
    public $middlewareAfter = [
        HandleAfter::class
    ];

    public $middlewareAliases = [
        'auth' => AuthMiddleware::class
    ];
}