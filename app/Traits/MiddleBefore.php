<?php 
namespace App\Traits;

use App\Utils\Request;

trait MiddleBefore
{
    protected function previousUrl(Request $request){

        $previous_url = $request->session()->get('previous_url') ?? [];

        if (count($previous_url) === 0 || (end($previous_url) !== $request->uri())) {
            $previous_url[] = $request->uri();
        } 
        if(count($previous_url) > 20){
            $previous_url = array_slice($previous_url, -20, 20, true);
        }

        $request->session()->set('previous_url',$previous_url); 
    }

    protected function csrfToken(Request $request){
        if($request->method() !== 'GET'){
            if($request->ajax()){
                $csrfToken = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : NULL;
            }else{
                $csrfToken = $request->input('csrf_token') ?? NULL;
            }
            if (!session()->has("csrf_token") || !hash_equals(session()->get("csrf_token"), $csrfToken)) {
                about(419);
            }else{
                if(!$request->ajax()){
                    session()->remove("csrf_token");
                }
            }
        }
    }
}