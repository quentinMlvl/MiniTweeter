<?php

namespace tweeterapp\view;

use mf\router\Router;
use tweeterapp\model\Tweet;
use tweeterapp\auth\TweeterAuthentification;

class TweeterView extends \mf\view\AbstractView {
  
    /* Constructeur 
    *
    * Appelle le constructeur de la classe parent
    */
    public function __construct( $data ){
        parent::__construct($data);
    }

    /* Méthode renderHeader
     *
     *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
     */ 
    private function renderHeader(){
        $topMenu = $this->renderTopMenu();

        return(
            "<h1>MiniTweeTR</h1>
                <nav id=\"nav-menu\">
                    ${topMenu}
                </nav>"
        );
    }

    /* Méthode renderTopMenu
     *
     *  Retourne le fragment HTML de la barre de navigation
     */ 
    private function renderTopMenu(){

        $router = new Router();
        $app_root = (new \mf\utils\HttpRequest())->root;

        $linkHome = '<a aria-label="Page d\'accueil" href="'.  $router->urlFor('maison') .'"><img src="'.$app_root.'/html/img/home.png"></a>';

        if ((new TweeterAuthentification)->logged_in) {
            $link2 = '<a aria-label="Page Personnelle" href="'.  $router->urlFor('page_personnelle') .'"><img src="'.$app_root.'/html/img/pagePerso.png"></a>';
            $link3 = '<a aria-label="Vos Followers" href="'.  $router->urlFor('affiche_followers') .'"><img src="'.$app_root.'/html/img/followees.png"></a>';
            $link4 = '<a aria-label="Tweets de vos followees" href="'.  $router->urlFor('affiche_followTweet') .'"><img src="'.$app_root.'/html/img/follow.png"></a>';
            $link5 = '<a aria-label="Déconnexion" href="'.  $router->urlFor('logout') .'"><img src="'.$app_root.'/html/img/logout.png"></a>';
        }else {
            $link2 = '<a aria-label="Page de connexion" href="'.  $router->urlFor('affiche_login') .'"><img src="'.$app_root.'/html/img/login.png"></a>';
            $link3 = '<a aria-label="Page d\'inscription" href="'.  $router->urlFor('creer_compte') .'"><img src="'.$app_root.'/html/img/signup.png"></a>';
            $link4 = '';
            $link5 = '';
        }

        return(
            "<ul>
                ${linkHome}
                ${link2}
                ${link3}
                ${link4}
                ${link5}
            </ul>");
    }

    /* Méthode renderBottomMenu 
     *
     * Retourne le fragment HTML du bas pour ajouter un tweet
     * 
     */
    private function renderBottomMenu (){
        if ((new TweeterAuthentification)->logged_in) {
            return("<div class=\"addTweet theme-backcolor1\"><a href=" . (new Router)->urlFor('afficheForm_tweet') . ">Nouveau Tweet</a></div>");
        }else return '';
    }
    
    /* Méthode renderFooter
     *
     * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
     * 
     */
    private function renderFooter(){
        return 'La super app créée en Licence Pro &copy;2020';
    }

    /* Méthode renderHome
     *
     * Vue de la fonctionalité afficher tous les Tweets. 
     *  
     */
    
    protected function renderHome(){
            
        $router = new Router();

        $chaine = "";

        foreach($this->data as $tweet){
            $urlTweet = $router->urlFor('affiche_tweet', [["id",  $tweet->id]]);
            $urlUser = $router->urlFor('affiche_user', [["id",  $tweet->author]]);

            if($tweet->score > 1){
                $scorePluriel = 's';
            }else $scorePluriel = '';

            $chaine .= 
            "<article class='tweet'>
                <a href=\"$urlTweet\">
                    <p>" .$tweet->text . "</p>
                </a>
                <div class='tweet-footer'>
                    <p>" . $tweet->created_at . "</p>
                    <p>" . $tweet->score . " Like$scorePluriel</p>
                    <a href=\"$urlUser\">" . $tweet->author()->first()->fullname ."</a>
                </div>
            </article>";
            }

            return $chaine;

    }
  
    /* Méthode renderUserTweets
     *
     * Vue de la fonctionalité afficher tout les Tweets d'un utilisateur donné. 
     * 
     */
     
    protected function renderUserTweets(){

        $router = new Router();

        $chaine = "<h3>Tweet de " . $this->data->fullname . "</h3>";

        foreach($this->data->tweets()->get() as $tweet){
            $url = $router->urlFor('affiche_tweet', [["id",  $tweet->id]]);
          
            if($tweet->score > 1){
                $scorePluriel = 's';
            }else $scorePluriel = '';

            $chaine .= "<article class='tweet'>
                            <a href=\"$url\">
                                <p class='tweet-text'>" .$tweet->text . "</p>
                            </a>
                            <div class='tweet-footer'>
                                <p>" . $tweet->created_at . "</p>
                                <p>" . $tweet->score . " Like$scorePluriel</p>
                            </div>
                        </article>";
         }

         return $chaine;
        
    }
  
    /* Méthode renderViewTweet 
     * 
     * Rréalise la vue de la fonctionnalité affichage d'un tweet
     *
     */
    
    protected function renderViewTweet(){         
        $router = new Router();
        $url = $router->urlFor('affiche_user', [["id",  $this->data->author]]);
        if($this->data->score > 1){
            $scorePluriel = 's';
        }else $scorePluriel = '';
        
        return("
        <article class='tweet'>
            <p>" .$this->data->text . "</p>
            <div class='tweet-footer'>
                <p>" . $this->data->created_at . "</p>
                <p>". $this->data->score . " Like$scorePluriel</p>
                <a class='tweet-author' href=\"$url\">" . $this->data->author()->first()->fullname . "</a>
            </div>
        <article>
        ");
        
    }


    /* Méthode renderPostTweet
     *
     * Realise la vue de régider un Tweet
     *
     */
    protected function renderPostTweet(){

        $urlPost = (new Router())->urlFor('enregistrer_tweet');

         return(
            '<form method="POST" class="forms" id="tweet-form" action="' . $urlPost . '">
                <textarea id="story" name="story" placeholder="Votre Tweet"></textarea>
                <input type="submit" value="Envoyer le Tweet">
            </form>'
         );
        
    }

    /* Méthode renderLogin
     *
     * Realise la vue de Connexion
     *
     */
    
    protected function renderLogin(){

        $urlPost = (new Router())->urlFor('check_login');
        return(
            '<form method="POST" class="forms" action="' . $urlPost . '">
                <div class="form-item">
                    <label for="username">Nom d"utilisateur : </label>
                    <input type="text" name="username" id="username" required />
                </div>
                <div class="form-item">
                    <label for="password">Mot de passe : </label>
                    <input type="password" name="password" id="password" required />
                </div>
                <input type="submit" value="Connexion">
            </form>'
        );
    }

    /* Méthode renderFollowers
     *
     * Realise la vue des followers de l'utilisateur
     *
     */
    protected function renderFollowers(){

        $router = new Router();
        $chaine = "<h2>Vous suivez : </h2>";
        
        if (count($this->data)) {
            foreach ($this->data as $user) {
                $url = $router->urlFor('affiche_user', [["id", $user->id]]);
                $chaine .= "<div class=\"follower\"><a href=\"$url\">$user->fullname <em>@$user->username</em></a></div>";
            }
        }else {
            $chaine = "Vous n'avez pas de followers";
        }

        return($chaine);
    }

    /* Méthode renderFollowers
     *
     * Realise la vue des followers de l'utilisateur
     *
     */
    protected function renderFollowerTweet(){

        $chaine = "<h2>Vos follows</h2>";

        $chaine .= $this->renderHome();

        return($chaine);
    }

    /* Méthode renderPagePerso
     *
     * Realise la vue de la page perso de l'utilisateur (avec qui le suit)
     *
     */
    protected function renderPagePerso(){

        $nbFollowee = count($this->data);
        $chaine = "<h2>Votre Page Perso</h2>";

        if ($nbFollowee) {

            if($nbFollowee > 1){
                $vousSuit = " personnes vous suivent.";
            }else $vousSuit = " personne vous suit.";

            $chaine .= "<h3>$nbFollowee $vousSuit</h3>";
            foreach ($this->data as $user) {
                $url = (new Router)->urlFor('affiche_user', [["id", $user->id]]);
                $chaine .= "<div class=\"follower\"><a href=\"$url\">$user->fullname <em>@$user->username</em>, vous suit.</a></div>";
            }
        }else {
            $chaine .= "Personne ne vous suit.";
        }

        return $chaine;

    }
    
    /* Méthode renderSignUp
     *
     * Realise la vue du questionnaire de création de compte
     *
     */
    protected function renderSignUp(){

        $urlPost = (new Router())->urlFor('check_signup');
        return(
            '<form method="POST" class="forms" action="' . $urlPost . '">
                <div class="form-item">
                    <label for="username">Nom d\'utilisateur (unique) : </label>
                    <input type="text" name="username" id="username" required />
                </div>
                <div class="form-item">
                    <label for="fullname">Nom complet : </label>
                    <input type="text" name="fullname" id="fullname" required />
                </div>
                <div class="form-item">
                    <label for="password">Mot de passe : </label>
                    <input type="password" name="password" id="password" required />
                </div>
                <div class="form-item">
                    <label for="repeat_password">Répéter votre mot de passe : </label>
                    <input type="password" name="repeat_password" id="repeat_password" required />
                </div>
                <input type="submit" value="Connexion">
            </form>'
        );
    }

    /* Méthode renderError
     *
     * Realise la vue en cas de page inexistante
     *
     */
    protected function renderError(){

        $router = new Router();
        $urlHome = $router->urlFor('maison');

        return(
            "<h1>PAGE NON TROUV&Eacute;E !!!</h1><br>
            <a href=\"${urlHome}\">Retournez à la page d\'accueil<a><br> OU<br>
            <a href=\"javascript:history.go(-1)\">Retour à la page précédente</a>"
        );
    }


    /* Méthode renderBody
     *
     * Retourne la framgment HTML de la balise <body> elle est appelée
     * par la méthode héritée render.
     *
     */
    
    protected function renderBody($selector){

        $header = $this->renderHeader();
        $footer = $this->renderFooter();
        $addTweet = $this->renderBottomMenu();

        switch ($selector) {
            case 'maison':
                $main = $this->renderHome();
                break;
            
            case 'affiche_tweet':
                $main = $this->renderViewTweet();
                break;
            
            case 'affiche_user':
                $main = $this->renderUserTweets();
                break;
            
            case 'afficheForm_tweet':
                $main = $this->renderPostTweet();
                break;
            
            case 'affiche_login':
                $main = $this->renderLogin();
                break;

            case 'affiche_followers':
                $main = $this->renderFollowers();
                break;

            case 'affiche_followerTweet':
                $main = $this->renderFollowerTweet();
                break;

            case 'affiche_signup':
                $main = $this->renderSignUp();
                break;

            case 'affiche_pagePerso':
                $main = $this->renderPagePerso();
                break;
            
            case 'error':
                $main = $this->renderError();
                break;
            
            default:
                $router = new Router();
                $router->executeRoute('maison');
                break;
        }

        return(<<<EOT
<header class="theme-backcolor1">
    ${header}
</header>
<main class="theme-backcolor2">
    <section>
        ${main}
    </section>
    ${addTweet}
</main>
<footer class="theme-backcolor1">
    ${footer}
</footer>

EOT);

        
    }
  
};
