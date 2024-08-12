<?php

namespace Artisan;

class MakeTrait
{
    public static $pathTrait = "app/Traits/";
    public static function make($data,$config)
    {
        $path = self::$pathTrait . $data . ".php";
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $content = self::setContent($path);

        if (!file_exists($path)) {
            file_put_contents($path, $content);
            echo "Đã tạo file: " . $path;
        } else {
            echo "File đã tồn tại: " . $path;
        }
    }

    public static function setContent($path)
    {
        $lastSlashPos = strrpos($path, '/');

        $namespace = ucfirst(substr($path, 0, $lastSlashPos));
        $namespace = str_replace('/', '\\', $namespace);

        $fileName = substr($path, $lastSlashPos + 1);

        $className = pathinfo($fileName, PATHINFO_FILENAME);
        $data = [
            'namespace' => $namespace,
            'className' => $className,
        ];

        ob_start();
        extract($data);
        include 'views/trait.php';
        return ob_get_clean();
    }
}
