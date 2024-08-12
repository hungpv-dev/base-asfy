<?php

namespace Artisan;

class MakeController
{
    public static $pathController = "app/Controllers/";
    public static function make($data,$config)
    {
        $path = self::$pathController . $data . ".php";
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $content = self::setContent($path,$config);

        if (!file_exists($path)) {
            file_put_contents($path, $content);
            echo "Đã tạo file: " . $path;
        } else {
            echo "File đã tồn tại: " . $path;
        }
    }

    public static function setContent($path,$config)
    {
        $lastSlashPos = strrpos($path, '/');

        $namespace = ucfirst(substr($path, 0, $lastSlashPos));
        $namespace = str_replace('/', '\\', $namespace);

        $fileName = substr($path, $lastSlashPos + 1);

        $className = pathinfo($fileName, PATHINFO_FILENAME);
        $data = [
            'namespace' => $namespace,
            'className' => $className,
            'config' => $config,
        ];

        ob_start();
        extract($data);
        include 'views/controller.php';
        return ob_get_clean();
    }
}
