<?php 
    namespace Artisan;

    use Artisan\{
        MakeController,
        MakeModel,
        MakeMiddleware,
        MakeTrait,
    };
    
    require_once './vendor/artisan/src/MakeController.php';
    require_once './vendor/artisan/src/MakeModel.php';
    require_once './vendor/artisan/src/MakeMiddleware.php';
    require_once './vendor/artisan/src/MakeTrait.php';
    
    class Make {
        private $data;
        private $config;
        private $mapping = [
            'controller' => MakeController::class,
            'model' => MakeModel::class,
            'middleware' => MakeMiddleware::class,
            'trait' => MakeTrait::class,
        ];
    
        public function __construct($data,$config) {
            $this->data = $data;
            $this->config = $config;
        }
    
        public function handle($type) {
            if (isset($this->mapping[$type])) {
                $class = $this->mapping[$type];
                $class::make($this->data,$this->config);
            } else {
                echo "Không rõ yêu cầu!";
            }
        }
    }
    
