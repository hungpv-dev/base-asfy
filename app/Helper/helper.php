<?php

if (!function_exists('view')) {
    function view($view, $data = [])
    {
        $blade = new Blade();
        $blade->render($view, $data);
    }
}
if (!function_exists('csrf_token')) {
    function csrf_token(){
        
        if (session()->has("csrf_token")) {
            $csrfToken = session()->get("csrf_token");
        } else {
            $csrfToken = bin2hex(random_bytes(32));
        }

        session()->set("csrf_token", $csrfToken);

        return $csrfToken;
    }
}
if (!function_exists('route')) {
    function route($route, $params = [])
    {
        $routes = \App\Utils\Route::$names;
        if (!isset($routes[$route])) {
            echo "Route $route không tồn tại!";
        }
        $path = $routes[$route];

        if (is_array($params)) {
            if (!empty($params)) {
                foreach ($params as $value) {
                    $path = preg_replace('/\([^\)]+\)/', $value, $path, 1);
                }
            }
        } else {
            $path = preg_replace('/\([^\)]+\)/', $params, $path, 1);
        }

        return $path;
    }
}
if (!function_exists('http_status_code')) {
    function http_message_code($code)
    {
        $statusCodes = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            419 => 'Page expired | CSRF token invalide',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        ];
        return $statusCodes[$code] ?? 'Trạng thái khong xác định';
    }
}
if (!function_exists('about')) {
    function about($status)
    {
        try {
            // Đặt mã trạng thái HTTP
            http_response_code($status);

            // Check status trang để chuyển hướng
            $status = http_response_code();
            $request = new \App\Utils\Request();
            if ($request->ajax()) {
                return $request->response([
                    'message' => http_message_code($status)
                ], $status);
            } else {
                view("errors.$status");
            }
        } catch (RuntimeException $e) {
            view("errors.default", compact('status'));
        }

        exit;
    }
}
if (!function_exists('session')) {
    function session($flush = false)
    {
        return new Session($flush);
    }
}
if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            echo '<pre>';
            \Symfony\Component\VarDumper\VarDumper::dump($var);
            echo '</pre>';
        }
        die();
    }
}
if (!function_exists('old')) {
    function old($key)
    {
        return $_SESSION['flush']['form']['value'][$key] ?? false;
    }
}
if (!function_exists('back')) {
    function back()
    {
        session()->set('previous_url', array_values(session()->get('previous_url')));
        if (session()->has('previous_url')) {
            $key = count(session()->get('previous_url')) - 2;
        } else {
            $key = count(session()->get('previous_url')) - 1;
        }
        $previous_url = session()->get('previous_url')[$key];
        return redirect($previous_url);
    }
}
if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 302)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        http_response_code($statusCode);
        header("Location: " . $url);
        exit();
    }
}
if (!function_exists('compact')) {
    function compact(...$variables)
    {
        $result = [];
        foreach ($variables as $varName) {
            if (isset($GLOBALS[$varName])) {
                $result[$varName] = $GLOBALS[$varName];
            }
        }
        return $result;
    }
}
if (!function_exists('asset')) {
    function asset($path)
    {
        return $_ENV['BASE_URL'] . '/public/assets/' . $path;
    }
}
if (!function_exists('getRootUrl')) {
    function getRootUrl()
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https://" : "http://";
        return $http . $_SERVER["HTTP_HOST"];
    }
}
if (!function_exists('logError')) {
    function logError(Exception $e)
    {
        $traceInfo = getBacktraceInfo();

        $errorMessage = sprintf(
            "Error: %s\nIn file: %s\nOn line: %d",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        if ($traceInfo['class'] || $traceInfo['function']) {
            $errorMessage .= "\nContext: ";
            if ($traceInfo['class']) {
                $errorMessage .= "class " . $traceInfo['class'] . "::";
            }
            if ($traceInfo['function']) {
                $errorMessage .= "function " . $traceInfo['function'];
            }
        }

        if ($traceInfo['file'] && $traceInfo['line']) {
            $errorMessage .= sprintf("\nCaught in file: %s\nOn line: %d", $traceInfo['file'], $traceInfo['line']);
        }

        if (!empty($traceInfo['context'])) {
            $errorMessage .= "\nContext Variables: " . implode(', ', $traceInfo['context']);
        }

        $logFilePath = $_ENV['BASE_URL'] . '/storage/logs/error_log_' . date('d-m-Y') . '.log';
        error_log($errorMessage, 3, $logFilePath);
    }

    function getBacktraceInfo()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $traceInfo = isset($backtrace[1]) ? $backtrace[1] : [];

        $class = $traceInfo['class'] ?? null;
        $function = $traceInfo['function'] ?? null;
        $file = $traceInfo['file'] ?? null;
        $line = $traceInfo['line'] ?? null;

        $context = [];
        foreach ($backtrace as $trace) {
            if (!empty($trace['args'])) {
                foreach ($trace['args'] as $arg) {
                    $context[] = var_export($arg, true);
                }
            }
        }

        return [
            'class' => $class,
            'function' => $function,
            'file' => $file,
            'line' => $line,
            'context' => $context,
        ];
    }
}
if (!function_exists('getProtocol')) {
    function getProtocol()
    {
        if (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        ) {
            return 'https://';
        }
        return 'http://';
    }
}
if (!function_exists('currentUrl')) {

    function currentUrl($param = true)
    {
        $protocol = getProtocol();
        $pageURL = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if ($param === true) {
            return $pageURL;
        } else {
            return preg_replace('/\?.*$/', '', $pageURL);
        }
    }
}
if (!function_exists('dateFormat')) {
    function dateFormat($time = null, $format = 'd-m-Y')
    {
        if ($time === null || $time == '' || $time == '0000-00-00' || $time == '0000-00-00 00:00:00') {
            return '';
        }
        return date($format, strtotime($time));
    }
}
if (!function_exists('getTimeAgo')) {
    function getTimeAgo($time, $type = '')
    {
        if ($time == '' || $time == '0000-00-00' || $time == '0000-00-00 00:00:00') {
            return 'Chưa hoạt động';
        }
        if (preg_match('/[-:]/', $time)) {
            $time = strtotime($time);
        }
        $time_diff = time() - $time;
        $rs = '';
        $num = '';
        $unit = '';
        if ($time_diff <= 5) {
            return 'Vừa xong';
        } elseif ($time_diff < 60) {
            $num = $time_diff;
            $unit = 's';
        } elseif ($time_diff < 3600) {
            $num = floor($time_diff / 60);
            $unit = 'm';
        } elseif ($time_diff < 3600 * 24) {
            $num = floor($time_diff / 3600);
            $unit = 'h';
        } elseif ($time_diff < 30 * 3600 * 24) {
            $num = floor($time_diff / (3600 * 24));
            $unit = 'd';
        } elseif ($time_diff < 365 * 3600 * 24) {
            $num = floor($time_diff / (30 * 3600 * 24));
            $unit = 'mo';
        } else {
            $num = floor($time_diff / (365 * 3600 * 24));
            $unit = 'y';
        }
        if ($type == '') {
            $rs = $num . $unit;
        } else {
            if ($unit == 's') {
                $unit = 'giây';
            } elseif ($unit == 'm') {
                $unit = 'phút';
            } elseif ($unit == 'h') {
                $unit = 'giờ';
            } elseif ($unit == 'd') {
                $unit = 'ngày';
            } elseif ($unit == 'mo') {
                $unit = 'tháng';
            } elseif ($unit == 'y') {
                $unit = 'năm';
            }
            $rs = $num . ' ' . $unit . ' trước';
        }
        return $rs;
    }
}
if (!function_exists('getDayAgo')) {
    function getDayAgo($time)
    {
        if ($time == '0000-00-00 00:00:00' || $time == '') {
            return 0;
        }
        if (strpos($time, ':') !== false) {
            $time = strtotime($time);
        }
        $time_range = time() - $time;
        return (int)floor($time_range / (3600 * 24));
    }
}
if (!function_exists('getDateStart2End')) {
    function getDateStart2End($dateRequest, $format = 'Y-m-d'): array
    {
        $start_date = '';
        $end_date = '';
        try {
            if (preg_match('# đến #ui', $dateRequest)) {
                $date = explode(' đến ', $dateRequest);
                $start_date = (new DateTime($date[0]))->format($format);
                $end_date = (new DateTime($date[1]))->format($format);
            } elseif (preg_match('# to #ui', $dateRequest)) {
                $date = explode(' to ', $dateRequest);
                $start_date = (new DateTime($date[0]))->format($format);
                $end_date = (new DateTime($date[1]))->format($format);
            } else {
                $start_date = (new DateTime($dateRequest))->format($format);
                $end_date = (new DateTime($dateRequest))->format($format);
            }
            return [
                'start' => $start_date,
                'end' => $end_date
            ];
        } catch (Exception $e) {
            return [];
        }
    }
}
