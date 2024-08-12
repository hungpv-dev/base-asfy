<?php

namespace App\Controllers;

use App\Commons\Controller;
use App\Models\User;
use App\Traits\GoogleClient;
use App\Utils\Request;
use DB;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;

class LoginController extends Controller
{
    use GoogleClient;

    public function showFormLogin()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $client = $this->clientGoogle();
        $url = $client->createAuthUrl();
        return view('auth.login', compact('url'));
    }

    public function login(Request $request){
        $rules = [
            'username' => 'required',
            'password' => 'required|min:8|max:20',
        ];

        $messages = [
            'required' => ':attribute là trường bắt buộc!',
            'min' => ':attribute tối thiểu :min kí tự',
            'max' => ':attribute tối đa :max kí tự',
        ];

        $attributes = [
            'username' => 'Tài khoản',
            'password' => 'Mật khẩu',
        ];

        $validate = $request->validate($rules,$messages,$attributes);

        if($request->ajax()){
            return $request->response($validate->errors(),422);
        }
        if($validate->fails()){
            $validate->flush();
            return back();
        }

        $username = $request->input('username');
        $user = User::where('username', '=', $username)->first();
        $this->handleLogin($user,$request,NULL);
    }

    protected function handleLogin($user, $request, $avatar)
    {
        $error = '';
        if ($user) {
            if(!$avatar){
                $password = $request->input('password');
                if(!password_verify($password, $user->password)){
                    session(true)->set('error','Tài khoản hoặc mật khẩu không chính xác!');
                    return $request->back();
                }
            }
            if ($user->status >= 0) {
                if (in_array($user->role_id, $this->role_access)) {
                    $accessPayload = [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'username' => $user->username,
                        "exp" => time() + $this->access_express
                    ];
                    $refreshPayload = [
                        'user_id' => $user->id,
                        "exp" => time() + $this->refresh_express
                    ];
                    $accessToken = JWT::encode($accessPayload, $_ENV['SECURITY_KEY'], 'HS256');
                    $refreshToken = JWT::encode($refreshPayload, $_ENV['SECURITY_KEY'], 'HS256');
                    if($avatar){
                        $user->avatar = $avatar;
                        $user->save();
                    }
                    $this->setCookie('secure_access_token', $accessToken, $this->access_express);
                    $this->setCookie('secure_refresh_token', $refreshToken, $this->refresh_express);
                    $this->saveLoginSession($accessToken, $refreshToken, $user->id, $request);
                    $this->setSession($user);
                    return redirect('/');
                } else {
                    $error = 'Tài khoản của bạn không có quyền truy cập!';
                }
            } else {
                $error = 'Tài khoản đã bị khoá không thể đăng nhập.';
            }
        } else {
            $error = 'Tài khoản không tồn tại trên hệ thống vui lòng đăng nhập bằng tài khoản đã được đăng ký.';
        }
        session(true)->set('error', $error);
        return redirect('/login');
    }

    private function saveLoginSession($accessToken, $refreshToken, $user_id, $request)
    {
        try {
            $data = [
                'user_id' => $user_id,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'ip' => $request->getRealIPAddress(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_at' => date($this->date_format),
                'expired_at' => date($this->date_format, time() + $this->refresh_express)
            ];
            DB::table('user_login_sessions')->insert($data);
        } catch (Exception $e) {
            error_log('Save Login Session Error: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        try{
            if (isset($_COOKIE['secure_access_token'])) {
                $accessToken = $_COOKIE['secure_access_token'];
                $payload = (array) JWT::decode($accessToken, new Key($_ENV['SECURITY_KEY'], 'HS256'));
                $userId = $payload['user_id'];
                DB::table('user_login_sessions')->where('user_id', $userId)->where('access_token', $accessToken)->delete();
            }
            unset($_SESSION['authentication']);
            // setcookie('secure_access_token', '', time() - 42000, '/', $_SERVER['HTTP_HOST'], true, true);
            // setcookie('secure_refresh_token', '', time() - 42000, '/', $_SERVER['HTTP_HOST'], true, true);
            setcookie('secure_access_token', '', time() - 42000, '/');
            setcookie('secure_refresh_token', '', time() - 42000, '/');
        }catch(ExpiredException $e){
            session(true)->set('error','Phiên đăng nhập đã hết hạn!');
        }
        return redirect('/login');
    }

    public function handleAccessToken()
    {
        try {
            if (isset($_COOKIE['secure_access_token'])) {
                $payload = (array) JWT::decode($_COOKIE['secure_access_token'], new Key($_ENV['SECURITY_KEY'], 'HS256'));
                $this->refreshAccessToken($payload);
            } else {
                $this->handleRefreshToken();
            }
        } catch (ExpiredException $e) {
            $this->handleRefreshToken();
        } catch (InvalidArgumentException $e) {
            error_log('Handle Access Token Error: Invalid argument');
            session(true)->set('error', 'Phiên đăng nhập đã hết hạn');
            $this->logout();
        } catch (Exception $e) {
            error_log('Handle Access Token Error: ' . $e->getMessage());
            session(true)->set('error', 'Phiên đăng nhập đã hết hạn');
            $this->logout();
        }
    }

    public function refreshAccessToken($payload)
    {
        try {
            $newAccessPayload = [
                'user_id' => $payload['user_id'],
                'email' => $payload['email'],
                'username' => $payload['username'],
                "exp" => time() + $this->access_express,
            ];
            $newAccessToken = JWT::encode($newAccessPayload, $_ENV['SECURITY_KEY'], 'HS256');
            $this->setCookie('secure_access_token', $newAccessToken, $this->access_express);
            $user = User::find($payload['user_id']);
            $this->setSession($user);
        } catch (Exception $e) {
            session(true)->set('error', 'Phiên đăng nhập hết hạn');
            $this->logout();
            error_log('Refresh Access Token Error: ' . $e->getMessage());
        }
    }

    public function handleRefreshToken()
    {
        try {
            $token = $_COOKIE['secure_refresh_token'] ?? null;
            if ($token) {
                $refreshPayload = (array) JWT::decode($_COOKIE['secure_refresh_token'], new Key($_ENV['SECURITY_KEY'], 'HS256'));
                $this->generateNewAccessToken($refreshPayload, $token);
            } else {
                session(true)->set('error', 'Phiên đăng nhập hết hạn');
                $this->logout();
            }
        } catch (InvalidArgumentException $e) {
            error_log('Handle Refresh Token Error: Invalid argument');
            session(true)->set('error', 'Phiên đăng nhập hết hạn');
            $this->logout();
        } catch (Exception $e) {
            error_log('Handle Refresh Token Error: ' . $e->getMessage());
            session(true)->set('error', 'Phiên đăng nhập hết hạn');
            $this->logout();
        }
    }
    private function generateNewAccessToken($refreshPayload, $token)
    {
        try {
            $user = User::find($refreshPayload['user_id']);
            $userLog = DB::table('user_login_sessions')->where('user_id',$refreshPayload['user_id'])->where('refresh_token',$token)->first();
            if ($user && $userLog && $user->status >= 0) {
                $refreshPayload['exp'] = time() + $this->access_express;
                $refreshPayload['email'] = $user->email;
                $refreshPayload['username'] = $user->username;
                $accessToken = JWT::encode($refreshPayload, $_ENV['SECURITY_KEY'], 'HS256');
                $this->setCookie('secure_access_token', $accessToken, $this->access_express);
                $this->setSession($user);
            } else {
                session(true)->set('error', 'Phiên đăng nhập hết hạn');
                $this->logout();
            }
        } catch (Exception $e) {
            error_log('Generate New Access Token Error: ' . $e->getMessage());
            $this->logout();
            session(true)->set('error', 'Phiên đăng nhập hết hạn');
        }
    }
}
