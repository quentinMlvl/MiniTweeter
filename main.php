<?php

/* pour le chargement automatique des classes d'Eloquent (dans le répertoire vendor) */
require_once('vendor/autoload.php');


/*Pour l'autoload de nos classes*/
require_once('src/mf/utils/AbstractClassLoader.php');
require_once('src/mf/utils/ClassLoader.php');

use mf\router\Router;
use tweeterapp\model\Like;
use tweeterapp\model\User;
use tweeterapp\model\Tweet;

$loader = new mf\utils\ClassLoader('src');
$loader->register();

session_start();

/*Config et Connexion à la BDD*/
$config = parse_ini_file('conf/config.ini');

$db = new Illuminate\Database\Capsule\Manager();

/* une instance de connexion  */
$db->addConnection($config); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */



/* Routage */

use tweeterapp\view\TweeterView;
use tweeterapp\auth\TweeterAuthentification;

$router = new Router();


/* TwitterController */
$router->addRoute('maison',
                  '/home/',
                  '\tweeterapp\control\TweeterController',
                  'viewHome');
                  
$router->setDefaultRoute('/home/');

$router->addRoute('affiche_tweet', '/tweet/', '\tweeterapp\control\TweeterController', 'viewTweet');
$router->addRoute('affiche_user', '/user/', '\tweeterapp\control\TweeterController', 'viewUserTweets');
$router->addRoute('afficheForm_tweet', '/post/', '\tweeterapp\control\TweeterController', 'viewPostTweet', TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('enregistrer_tweet', '/send/', '\tweeterapp\control\TweeterController', 'viewSendTweet', TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('affiche_followers', '/followers/', '\tweeterapp\control\TweeterController', 'viewFollowers', TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('affiche_followTweet', '/follow_tweet/', '\tweeterapp\control\TweeterController', 'viewFollowTweet', TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('page_personnelle', '/page_personnelle/', '\tweeterapp\control\TweeterController', 'viewPagePerso', TweeterAuthentification::ACCESS_LEVEL_USER);

/* TwitterAdminController */
$router->addRoute('affiche_login', '/login/', '\tweeterapp\control\TweeterAdminController', 'login');
$router->addRoute('check_login', '/check_login/', '\tweeterapp\control\TweeterAdminController', 'checkLogin');
$router->addRoute('logout', '/logout/', '\tweeterapp\control\TweeterAdminController', 'logout', TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('creer_compte', '/signup/', '\tweeterapp\control\TweeterAdminController', 'signup');
$router->addRoute('check_signup', '/check_signup/', '\tweeterapp\control\TweeterAdminController', 'checkSignUp');

TweeterView::addStyleSheet("html/style.css");


$router->run();