<?php

namespace App\Middleware;

use App\Controllers\LoginController;
use App\Models\User;
use App\Traits\GoogleClient;
use App\Utils\Request as Request;
use Exception;

class CheckSessionUser extends Middleware
{
    use GoogleClient;
    private $login;
    public function __construct()
    {
        $this->login = new LoginController();
    }
    public function handle(Request $request)
    {
        try {
            $refresh_token = $_COOKIE['secure_refresh_token'] ?? null;
            $access_token = $_COOKIE['secure_access_token'] ?? null;
            if (isset($_SESSION['authentication']) && isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $user = User::find($user_id);
                if ($user) {
                    if ($user->status < 0 || (!in_array($user->role_id, $this->role_access))) {
                        session(true)->set('error', 'Tài khoản không có quyền truy cập!');
                        $this->login->logout();
                    } else {
                        $this->login->setSession($user);
                    }
                } else {
                    session(true)->set('error', 'Tài khoản không tồn tại trong hệ thống!');
                    $this->login->logout();
                }
            } elseif ($refresh_token || $access_token) {
                $this->login->handleAccessToken();
            }
        } catch (Exception $e) {
            $this->login->logout();
            session(true)->set('error', 'Phiên đăng nhập đã hết hạn!');
        }
    }
}
