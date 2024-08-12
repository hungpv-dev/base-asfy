<?php

namespace App\Utils;

use App\Middleware\Kernel;

class Route
{

    private static $routes = array();
    private static $currentMiddleware = [];
    private static $countNames = 0;
    public static $names = array();

    private static $keyName = null;

    /**
     * Function used to add a new route
     * @param string $expression    Route string or expression
     * @param callable $function    Function to call if route with allowed method is found
     * @param string|array $method  Either a string of allowed method or an array with string values
     *
     */

    public static function add($expression, $function, $method = 'get')
    {
        array_push(self::$routes, array(
            'expression' => self::convertPattern($expression),
            'function' => $function,
            'method' => $method,
            'middleware' => self::$currentMiddleware
        ));

        self::$keyName = count(self::$routes) - 1;

        return new static;
    }

    public static function name($name)
    {
        if (isset(self::$names[$name])) {
            $data = self::$names[$name];
            $currentData = self::$routes[self::$keyName];
            if (in_array($currentData['method'], $data['method'])) {
                echo "Name <b>$name</b> và method này đã tồn tại";
                die();
            } else {
                self::$names[$name]['method'][] = self::$routes[self::$keyName]['method'];
            }
        } else {
            self::$names[$name] = [
                'key' => self::$keyName,
                'method' => [self::$routes[self::$keyName]['method']],
            ];
        }
    }

    public static function names($name)
    {
        $count = self::$keyName - self::$countNames;
        for ($i = self::$keyName; $i >= $count; $i--) {
            self::$keyName = $i;
            $nameRoute = $name . '.';
            if (is_array(self::$routes[$i]['function'])) {
                $nameRoute .= self::$routes[$i]['function'][1];
            } else {
                $nameRoute .= self::$routes[$i]['function'];
            }
            self::name($nameRoute);
        }
    }

    public static function resource($expression, $function)
    {
        self::$keyName = count(self::$routes) - 1;
        self::$countNames = 6;
        self::mount($expression, function () use ($function) {
            self::get('/', [$function, 'index']);
            self::get('/{id}', [$function, 'show']);
            self::get('/create', [$function, 'create']);
            self::post('/create', [$function, 'store']);
            self::get('/update/{id}', [$function, 'edit']);
            self::put('/update/{id}', [$function, 'update']);
            self::delete('/{id}', [$function, 'destroy']);
        });
        return new static();
    }

    public static function apiResource($expression, $function)
    {
        self::$keyName = count(self::$routes) - 1;
        self::$countNames = 4;
        self::mount($expression, function () use ($function) {
            self::get('/', [$function, 'index']);
            self::get('/{id}', [$function, 'show']);
            self::post('/create', [$function, 'store']);
            self::put('/update/{id}', [$function, 'update']);
            self::delete('/{id}', [$function, 'destroy']);
        });
        return new static();
    }

    public static function getAll()
    {
        return self::$routes;
    }

    public static function middeware($midd)
    {
        if (is_array($midd)) {
            self::$currentMiddleware = $midd;
        } else {
            self::$currentMiddleware[] = $midd;
        }
        return new static;
    }

    public static function group($function)
    {
        $currentRoutes = self::$routes;
        $middGroup = self::$currentMiddleware;
        $function();
        $newRoutes = array_slice(self::$routes, count($currentRoutes));
        foreach ($newRoutes as &$route) {
            $route['middleware'] = $middGroup;
        }
        self::$routes = array_merge($currentRoutes, $newRoutes);
        self::$currentMiddleware = [];
    }
    public static function mount($path, $function)
    {
        $currentRoutes = self::$routes;
        $middGroup = self::$currentMiddleware;
        $function();
        $newRoutes = array_slice(self::$routes, count($currentRoutes));
        foreach ($newRoutes as &$route) {
            $route['expression'] = $path . $route['expression'];
            $route['middleware'] = $middGroup;
        }

        self::$routes = array_merge($currentRoutes, $newRoutes);
        self::$currentMiddleware = [];
    }
    public static function get($expression, $callback)
    {
        return self::add($expression, $callback, 'GET');
    }

    public static function post($expression, $callback)
    {
        return self::add($expression, $callback, 'POST');
    }

    public static function put($expression, $callback)
    {
        return self::add($expression, $callback, 'PUT');
    }
    public static function delete($expression, $callback)
    {
        return self::add($expression, $callback, 'DELETE');
    }
    public static function patch($expression, $callback)
    {
        return self::add($expression, $callback, 'PATCH');
    }
    private static function convertPattern($expression)
    {
        // Biến đổi các kí tự {} thành ([a-zA-Z0-9-]+) -> để vị trí đó có thể để bất từ kí tự nào
        // ([a-zA-Z0-9-]+) : Chấp nhận các kí tự a-z, A-Z, 0-9, -
        return preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9-]+)', $expression);
    }
    private static function routeMatchFound(Request $request)
    {
        if ($request->ajax()) {
            $request->response([
                'message' => 'Trang không tồn tại'
            ], 404);
        } else {
            about(404);
        }
    }
    private static function methodMatchFound(Request $request)
    {
        if ($request->ajax()) {
            $request->response([
                'message' => 'Phương thức không hợp lệ'
            ], 404);
        } else {
            about(404);
        }
    }
    public static function listMiddeware($type = 'aliases')
    {
        $kennel = new Kernel();
        $listMiddeware = [];
        if ($type == 'aliases') {
            $listMiddeware = $kennel->middlewareAliases;
        } else if ($type == 'before') {
            $listMiddeware = $kennel->middlewareBefore;
        } else if ($type == 'after') {
            $listMiddeware = $kennel->middlewareAfter;
        }
        return $listMiddeware;
    }
    private static function handleGlobalMiddleware($middlewareGlobal)
    {
        foreach ($middlewareGlobal as $class) {
            if (class_exists($class)) {
                $md = new $class;
                $md->handle(new Request());
            }
        }
    }

    private static function handelNames(){
        $nameInit = [];
        foreach(self::$names as $key => $name){
            $index = $name['key'];
            $path = rtrim(self::$routes[$index]['expression'],'/');
            $nameInit[$key] = $path;
        }
        self::$names = $nameInit;
    }
    public static function run()
    {
        self::handelNames();

        $middlewareBefore = self::listMiddeware('before');
        $listMiddeware = self::listMiddeware('aliases');
        $middlewareAfter = self::listMiddeware('after');

        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $path = $parsed_url['path'];
        $path = urldecode($path);

        $method = $_SERVER['REQUEST_METHOD'];

        $method_match_found = true;
        $route_match_found = true;
        $callback = null;
        $data = [];
        $middleware = [];
        foreach (self::$routes as $route) {
            if (substr($route['expression'], 0, 1) !== '/') {
                $route['expression'] = '/' . $route['expression'];
            }
            $route['expression'] = $route['expression'] !== '/' ? rtrim($route['expression'], '/') : '/';

            $pattern = '/^' . str_replace('/', '\/', self::convertPattern($route['expression'])) . '$/';

            if (preg_match($pattern, $path, $matches)) {
                $route_match_found = false;
                if ((string)$route['method'] === $method) {
                    $method_match_found = false;
                    $callback = $route['function'];
                    $expression = $route['expression'];
                    $middleware = $route['middleware'];
                    $data = array_slice($matches, 1);
                    if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $expression, $keys)) {
                        $keys = $keys[1];
                        if (count($keys) === count($data)) {
                            $data = array_combine($keys, $data);
                        }
                    }
                }
            }
        }

        self::handleGlobalMiddleware($middlewareBefore);

        if (!empty($middleware)) {
            foreach ($middleware as $alia) {
                $class = $listMiddeware[$alia];
                if (class_exists($class)) {
                    $md = new $class;
                    $md->handle(new Request());
                }
            }
        }

        if ($route_match_found) {
            self::routeMatchFound(new Request());
            die();
        }

        if ($method_match_found) {
            self::methodMatchFound(new Request());
            die();
        }


        if (is_array($callback)) {
            $class = $callback[0];
            $action = $callback[1];
            try {
                $reflection = new \ReflectionMethod($class, $action);
                $parameters = $reflection->getParameters();
                $args = [];
                foreach ($parameters as $param) {
                    if ($param->getType() && $param->getType()->getName() === 'App\Utils\Request') {
                        $args[] = new Request();
                    } else {
                        $args[] = array_shift($data);
                    }
                }
                call_user_func_array([new $class, $action], $args);
            } catch (\ReflectionException $e) {
                self::methodMatchFound(new Request());
            }
        } else if (is_callable($callback)) {
            call_user_func_array($callback, $data);
        }

        self::handleGlobalMiddleware($middlewareAfter);

        die();
    }
}
