<?php

namespace App\Middleware;

use App\Utils\Request as Request;

class AuthMiddleware extends Middleware
{
    public function handle(Request $request)
    {
        if (!isset($_SESSION['authentication'])) {
            if ($request->ajax()) {
                $request->response([
                    'messages' => 'Vui lòng đăng nhập',
                ], 401);
                die();
            } else {
                return redirect('/login');
            }
        }
    }
}
