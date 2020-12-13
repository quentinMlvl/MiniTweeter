<?php

namespace tweeterapp\control;

use mf\router\Router;
use tweeterapp\model\User;
use tweeterapp\model\Follow;
use tweeterapp\view\TweeterView;
use tweeterapp\auth\TweeterAuthentification;
use mf\auth\exception\AuthentificationException;
use tweeterapp\model\Tweet;

class TweeterAdminController extends \mf\control\AbstractController {
        
    public function __construct(){
        parent::__construct();
    }

    public function login(){
        (new TweeterView(null))->render('affiche_login');
    }   

    public function checkLogin(){

        $auth = new TweeterAuthentification();

        $router = new Router();

        
        if (isset($_POST['username'], $_POST['password'])){
            
            try {
                $auth->loginUser($_POST['username'], $_POST['password']);
            } catch (AuthentificationException $e) {
                echo $e->getMessage();
            }

            if ($auth->logged_in) {
                $router->executeRoute('affiche_followers');
            }else $router->executeRoute('affiche_login');

        }else  $router->executeRoute('affiche_login');
    }

    public function logout(){
        (new TweeterAuthentification())->logout();
        (new Router())->executeRoute('maison');
    }

    public function signup(){
        (new TweeterView(null))->render('affiche_signup');        
    }

    public function checkSignUp(){
        $auth = new TweeterAuthentification();
        
        $router = new Router();
        
        if (isset($_POST['username'], $_POST['fullname'], $_POST['password'], $_POST['repeat_password'])){
            
            try {
                if ($auth->repeatPassword($_POST['password'],$_POST['repeat_password'])) {
                    $auth->createUser($_POST['username'], $_POST['password'], $_POST['fullname']);
                    $auth->loginUser($_POST['username'], $_POST['password']);
                    
                    if ($auth->logged_in) {
                        $router->executeRoute('maison');
                    }
                }
            } catch (AuthentificationException $e) {
                echo $e->getMessage();
                $router->executeRoute('creer_compte');
            }

        }else $router->executeRoute('creer_compte');
    }

}
