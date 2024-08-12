<?php
namespace App\Controllers;

use App\Commons\Controller;
use App\Utils\Request;

class UserController extends Controller{

    public function __construct(){
    }

    # [GET] /  =>  Danh sách dữ liệu 
    public function index(){
        echo 'index';
        // about(404);

    }

    # [GET] /create  =>  Hiện thị form thêm 
    public function create(){
        echo '123';
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request){
        dd($request->all());
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id){
        dd($id);
    }   

    # [GET] /update/{id}  =>  Hiển thị form cập nhật 
    public function edit($id){
        dd($id);
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id,Request $request){
        dd($id,$request->all());
    }

    # [DELETE] /{id}  =>  Xóa 1 bản ghi 
    public function destroy($id){
        dd($id);
    }
    
}
