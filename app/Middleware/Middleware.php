<?php

namespace App\Middleware;

use App\Utils\Request;

abstract class Middleware
{
    abstract public function handle(Request $request);
}
