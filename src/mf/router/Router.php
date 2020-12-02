<?php

namespace mf\router;

use mf\auth\Authentification;

class Router extends AbstractRouter {

    public function run()
    {

        if(array_key_exists($this->http_req->path_info, self::$routes)){
            $url = $this->http_req->path_info;
        }else {
            $url = self::$aliases['default'];
        };

        if (!((new Authentification())->checkAccessRight(self::$routes[$url][2]))) {
            $url = self::$aliases['default'];
        }
        $ctrl_name = self::$routes[$url][0];
        $method_name = self::$routes[$url][1];          
        $ctrl = new $ctrl_name();
        $ctrl->$method_name();
        
    }

    public function urlFor($route_name, $param_list = [])
    {
        if(isset(self::$aliases[$route_name])){
            $url_alias = self::$aliases[$route_name];
            
            $url = $this->http_req->script_name . $url_alias;
            
            if($param_list != null){
                $url = $url . "?";
                foreach($param_list as $key => $param){
                    $url = $url . $param[0] . "=" . $param[1];
                    if($key != (count($param_list)-1)){
                        $url .= "&";
                    }
                }
            }

            return($url);
        }

    }
    
    public function setDefaultRoute($url)
    {
        self::$aliases['default'] = $url;
    }

    public function addRoute($name, $url, $ctrl, $mth, $lvl = Authentification::ACCESS_LEVEL_NONE)
    {
        self::$routes[$url] = [$ctrl, $mth, $lvl];
        self::$aliases[$name] = $url;

    }

    public static function executeRoute($alias){
        if(isset(self::$aliases[$alias])){
            $url = self::$aliases[$alias];
        }else{
            $url = self::$aliases['default'];
        }

        if (!((new Authentification())->checkAccessRight(self::$routes[$url][2]))) {
            $url = self::$aliases['default'];
        }
        $ctrl_name = self::$routes[$url][0];
        $method_name = self::$routes[$url][1];   
        $ctrl = new $ctrl_name();
        $ctrl->$method_name();
    }
}