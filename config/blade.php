<?php


use eftec\bladeone\BladeOne;

class Blade
{
    private $templatePath = __DIR__ . '../../resources/views';
    private $convertedPath = __DIR__ . '../../storage/framework/views';

    private $blade;

    public function __construct()
    {
        $this->blade = new BladeOne($this->templatePath, $this->convertedPath);
        $this->clearCompiledViews();
        $this->handleConfig();
    }
    protected function handleConfig()
    {
        // Csrf token;
        $csrfToken = csrf_token();
        $this->blade->directive('csrf', function () use ($csrfToken) {
            return "<input type='hidden' name='csrf_token' value='" . htmlspecialchars($csrfToken) . "'>";
        });

        // Đăng ký directive @error
        $this->blade->directive('error', function ($key) {
            return "<?php
                if(isset(\$_SESSION['flush']['form']['errors'][$key][0])): 
                    \$message = \$_SESSION['flush']['form']['errors'][$key][0];
            ?>";
        });
        
        // Đăng ký directive @error
        $this->blade->directive('enderror', function () {
            return "<?php endif; ?>";
        });
    }

    public function render($view, $data = [])
    {

        echo $this->blade->run($view, $data);
    }

    private function clearCompiledViews()
    {
        // Xác định đường dẫn đến thư mục biên dịch

        // Kiểm tra xem thư mục có tồn tại không
        if (is_dir($this->convertedPath)) {
            // Lấy tất cả file trong thư mục
            $files = glob($this->convertedPath . '/*');

            // Xóa từng file
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // Xóa file
                }
            }
        }
    }
}
