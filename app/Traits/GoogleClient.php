<?php

namespace App\Traits;

use App\Models\User;
use App\Utils\Request;
use Google_Client;
use Exception;
use Google_Service_Oauth2;

trait GoogleClient
{
    protected $redirectHome = '/';
    protected $redirectBack = '/login';
    protected $role_access = [5, 7];
    protected $access_express = 3600;
    protected $refresh_express = 3600 * 24 * 7;
    protected $date_format = 'Y-m-d H:i:s';
    protected function clientGoogle()
    {
        $client_id = $_ENV['GOOGLE_APP_ID'];
        $client_secret = $_ENV['GOOGLE_APP_SECRET'];
        $redirect_uri = $_ENV['GOOGLE_APP_CALLBACK_URL'];
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope('email');
        $client->addScope('profile');
        return $client;
    }

    public function googleCallback(Request $request)
    {
        $code = $request->input('code');
        if (!$code) {
            return redirect($this->redirectBack);
        }
        try {
            $client = $this->clientGoogle();
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token['access_token']);
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            $user = User::where('email', $google_account_info['email'])->first();
            $this->handleLogin($user, $request,$google_account_info['picture']);
        } catch (Exception $e) {
            return redirect($this->redirectBack);
        }
        return redirect($this->redirectHome);
    }

    private function setCookie($name, $value, $exp)
    {
        try {
            setcookie($name, $value, [
                'expires' => time() + $exp,
                'path' => '/',
                // 'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        } catch (Exception $e) {
            error_log('Set Cookie Error: ' . $e->getMessage());
        }
    }

    public function setSession($user)
    {
        $_SESSION['authentication'] = $user;
        $_SESSION['user_id'] = $user->id;
    }


}
