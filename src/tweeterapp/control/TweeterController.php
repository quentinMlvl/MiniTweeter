<?php

namespace tweeterapp\control;

use DateTime;
use mf\router\Router;
use tweeterapp\model\User;
use tweeterapp\model\Tweet;
use tweeterapp\model\Follow;
use tweeterapp\view\TweeterView;
use tweeterapp\auth\TweeterAuthentification;

/* Classe TweeterController :
 *  
 * Réalise les algorithmes des fonctionnalités suivantes: 
 *
 *  - afficher la liste des Tweets 
 *  - afficher un Tweet
 *  - afficher les tweet d'un utilisateur 
 *  - afficher la le formulaire pour poster un Tweet
 *  - afficher la liste des utilisateurs suivis 
 *  - évaluer un Tweet
 *  - suivre un utilisateur
 *   
 */

class TweeterController extends \mf\control\AbstractController {


    /* Constructeur :
    * 
    * Appelle le constructeur parent
    *
    * c.f. la classe \mf\control\AbstractController
    * 
    */
    
    public function __construct(){
        parent::__construct();
    }


    /* Méthode viewHome : 
    * 
    * Réalise la fonctionnalité : afficher la liste de Tweet
    * 
    */
    
    public function viewHome(){

        /* Algorithme :
         *  
         *  1 Récupérer tout les tweet en utilisant le modèle Tweet
         *  2 Parcourir le résultat 
         *      afficher le text du tweet, l'auteur et la date de création
         *  3 Retourner un block HTML qui met en forme la liste
         * 
         */

        $reqParam = Tweet::select()->orderBy('created_at', 'DESC');

        $tweets = $reqParam->get();
        
        $vue = new TweeterView($tweets);

        $vue->render('maison');

    }


    /* Méthode viewTweet : 
    *  
    * Réalise la fonctionnalité afficher un Tweet
    *
    */
    
    public function viewTweet(){

        /* Algorithme : 
        *  
        *  1 L'identifiant du Tweet en question est passé en paramètre (id) 
        *      d'une requête GET 
        *  2 Récupérer le Tweet depuis le modèle Tweet
        *  3 Afficher toutes les informations du tweet 
        *      (text, auteur, date, score)
        *  4 Retourner un block HTML qui met en forme le Tweet
        * 
        *  Erreurs possibles : (*** à implanter ultérieurement ***)
        *    - pas de paramètre dans la requête
        *    - le paramètre passé ne correspond pas a un identifiant existant
        *    - le paramètre passé n'est pas un entier 
        * 
        */
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $reqParam = Tweet::select()->where('id', '=', $id);
            if($reqParam->first()){
                $tweet =$reqParam->first();
                $vue = new TweeterView($tweet);
                $vue->render('affiche_tweet');
            }else {
                $vue = new TweeterView("");
                $vue->render('error');
            };
        }
    }


    /* Méthode viewUserTweets :
    *
    * Réalise la fonctionnalité afficher les tweet d'un utilisateur
    *
    */
    
    public function viewUserTweets(){

        /*
        *
        *  1 L'identifiant de l'utilisateur en question est passé en 
        *      paramètre (id) d'une requête GET 
        *  2 Récupérer l'utilisateur et ses Tweets depuis le modèle 
        *      Tweet et User
        *  3 Afficher les informations de l'utilisateur 
        *      (non, login, nombre de suiveurs) 
        *  4 Afficher ses Tweets (text, auteur, date)
        *  5 Retourner un block HTML qui met en forme la liste
        *
        *  Erreurs possibles : (*** à implanter ultérieurement ***)
        *    - pas de paramètre dans la requête
        *    - le paramètre passé ne correspond pas a un identifiant existant
        *    - le paramètre passé n'est pas un entier 
        * 
        */

        $id = $_GET['id'];
        $reqParam = User::select()->where('id', '=', $id);

        
        if($reqParam->first()){
            $user =$reqParam->first();
            $vue = new TweeterView($user);
            $vue->render('affiche_user');
        }else {
            $vue = new TweeterView("");
            $vue->render('error');
        };
        
    }

    /* Méthode viewPostTweet :
    *
    * Réalise la fonctionnalité affiche le formulaire de création d'un tweet
    *
    */
    public function viewPostTweet(){

        $vue = new TweeterView(null);

        $vue->render('afficheForm_tweet');

    }

    /* Méthode viewSendTweet :
    *
    * Réalise la fonctionnalité enregistre le tweet depuis le formulaire
    *
    */
    public function viewSendTweet(){
        $router = new Router();
        if ($_POST['story']) {
            $thisUser = User::where('username', '=', (new TweeterAuthentification())->user_login)->first();

            $newTweet = new Tweet();
            $newTweet->text= filter_var($_POST['story'], FILTER_SANITIZE_SPECIAL_CHARS);
            $newTweet->author=$thisUser->id;
            $newTweet->created_at = (new DateTime())->format('Y-m-d H:i:s');

            $newTweet->save();

            $thisTweet = Tweet::orderBy('id', 'desc')->first();

            $_GET['id'] = $thisTweet->id;
            $router->executeRoute('affiche_tweet');
        }else $router->executeRoute('afficheForm_tweet');
    }


    /* Méthode viewFollowers :
    *
    * Réalise la fonctionnalité affiche les personnes que l'utilisateur suit
    *
    */
    public function viewFollowers(){
        $auth = new TweeterAuthentification();

        $reqUser = User::select()->where('username', '=', $auth->user_login);

        $reqFollowers = Follow::select()->where('followee', '=', $reqUser->first()->id)->get();

        $tabFollowers = [];

        foreach ($reqFollowers as $follower) {
            $tabFollowers[] = $follower->user()->first();
        }

        $vue = new TweeterView($tabFollowers);

        $vue->render('affiche_followers');

    }

    /* Méthode viewFollowTweet :
    *
    * Réalise la fonctionnalité affiche les tweets des personnes que l'utilisateur suit
    *
    */
    public function viewFollowTweet(){
        $auth = new TweeterAuthentification();

        
        $user = User::where('username', '=', $auth->user_login)->first();

        $reqFollowees = $user->followTweets()->get();


        $followeesTweets = [];

        foreach ($reqFollowees as $followee) {
            
            foreach ($followee->tweets as $tweet) {
                array_push($followeesTweets,$tweet);
            }

        }

        /* Tri des tweets par date de creation décroissante */
        usort($followeesTweets, function($object1, $object2){
            return $object1->created_at < $object2->created_at;
        });
        
        $vue = new TweeterView($followeesTweets);

        $vue->render('affiche_followerTweet');

    }

    /* Méthode viewPagePerso :
    *
    * Réalise la fonctionnalité affiche la page personnelle de l'utilisateur
    * Avec les personnes qui le suivent
    *
    */
    public function viewPagePerso()
    {

        $auth = new TweeterAuthentification();
        
        $reqUser = User::select()->where('username', '=', $auth->user_login);
        
        $reqFollowees = Follow::select()->where('follower', '=', $reqUser->first()->id)->get();

        $tabFollowees = [];
        
        foreach ($reqFollowees as $followees){
            $tabFollowees[] = $followees->userFollowee()->first();
        };
        
        (new TweeterView($tabFollowees))->render('affiche_pagePerso');
    }


}
