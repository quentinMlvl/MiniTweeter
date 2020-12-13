<?php

namespace tweeterapp\auth;

use tweeterapp\model\User;
use mf\auth\Authentification;
use mf\auth\exception\AuthentificationException;

class TweeterAuthentification extends Authentification {

    /*
     * Classe TweeterAuthentification qui définie les méthodes qui dépendent
     * de l'application (liée à la manipulation du modèle User) 
     *
     */

    /* niveaux d'accès de TweeterApp 
     *
     * Le niveau USER correspond a un utilisateur inscrit avec un compte
     * Le niveau ADMIN est un plus haut niveau (non utilisé ici)
     * 
     * Ne pas oublier le niveau NONE un utilisateur non inscrit est hérité 
     * depuis AbstractAuthentification 
     */
    const ACCESS_LEVEL_USER  = 100;   
    const ACCESS_LEVEL_ADMIN = 200;

    /* constructeur */
    public function __construct(){
        parent::__construct();
    }

    /* La méthode createUser 
     * 
     *  Permet la création d'un nouvel utilisateur de l'application
     * 
     *  
     * @param : $username : le nom d'utilisateur choisi 
     * @param : $pass : le mot de passe choisi 
     * @param : $fullname : le nom complet 
     * @param : $level : le niveaux d'accès (par défaut ACCESS_LEVEL_USER)
     * 
     * Algorithme :
     *
     *  Si un utilisateur avec le même nom d'utilisateur existe déjà en BD
     *     - soulever une exception 
     *  Sinon      
     *     - créer un nouvel modèle User avec les valeurs en paramètre 
     *       ATTENTION : Le mot de passe ne doit pas être enregistré en clair.
     * 
     */
    
    public function createUser($username, $pass, $fullname, $level=self::ACCESS_LEVEL_USER) {

        $testUser = User::where('username', '=', $username);
        $testUser->get();

        if(count($testUser->get()) == 0){
            $user = new User();
            $user->username = $username;
            $user->password = $this->hashPassword($pass);
            $user->fullname = $fullname;
            $user->level = $level;
            $user->save();
        }else{
            throw new AuthentificationException("Nom d'utilisateur déjà utilisé");
        }
    }

    /* La méthode loginUser
     *  
     * permet de connecter un utilisateur qui a fourni son nom d'utilisateur 
     * et son mot de passe (depuis un formulaire de connexion)
     *
     * @param : $username : le nom d'utilisateur   
     * @param : $password : le mot de passe tapé sur le formulaire
     *
     * Algorithme :
     * 
     *  - Récupérer l'utilisateur avec l'identifiant $username depuis la BD
     *  - Si aucun de trouvé 
     *      - soulever une exception 
     *  - sinon 
     *      - réaliser l'authentification et la connexion (cf. la class Authentification)
     *
     */
    
    public function loginUser($username, $password){
        $reqUser = User::where('username', '=', $username);
        
        $user = $reqUser->first();

        if($user){
            $this->login($username, $user->password, $password, $user->level);
        }else throw new AuthentificationException("Utilisateur Inconnu");
        

    }

    /* La méthode repeatUser
     *  
     * permet de vérifier que l'utilisateur répête son mot de passe
     * dans le formulaire d'inscription
     *
     * @param : $password : le mot de passe tapé sur le formulaire   
     * @param : $repeat_password : le mot de passe répété
     *
     * Algorithme :
     * 
     *  - Test si les 2 mots de passe passés en paramètre sont identiques
     *  - Sinon 
     *      - soulever une exception 
     * 
     */
    public function repeatPassword($password, $repeat_password){
        if ($password === $repeat_password) {
            return true;
        }else{
            throw new AuthentificationException("Mot de passe différent, Veuillez répéter votre mot de passe");
            return false;
        }
    }

}
