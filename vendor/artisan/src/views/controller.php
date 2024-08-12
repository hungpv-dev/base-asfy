<?php 

$classDefinition = <<<PHP
<?php
namespace $namespace;

use App\Commons\Controller;
use App\Utils\Request;

class $className extends Controller{

    public function __construct(){
    
    }

    # [GET] /  =>  Danh sách dữ liệu 
    public function index(){
    
    }

    # [GET] /create  =>  Hiện thị form thêm 
    public function create(){
    
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request \$request){
    
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show(\$id){
    
    }

    # [GET] /update/{id}  =>  Hiển thị form cập nhật 
    public function edit(\$id){
    
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update(\$id,Request \$request){
    
    }

    # [DELETE] /{id}  =>  Xóa 1 bản ghi 
    public function destroy(\$id){
    
    }
    
}

PHP;

echo $classDefinition;