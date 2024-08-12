<?php

namespace App\Middleware;

use App\Utils\Request as Request;

class HandleAfter extends Middleware
{
    public function handle(Request $request)
    {
        // Xóa session flush
        $request->session()->clearFlush();
    }
}
