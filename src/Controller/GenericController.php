<?php

namespace App\Controller;

use App\Lib\ConnexionUtilisateur;
use App\Lib\MessageFlash;

class GenericController
{
    protected static function afficheVue(string $cheminVue, array $parametres = []) : void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . "/../View/$cheminVue"; // Charge la vue
    }

    public static function error(string $code='404', string $message='Cette page n\'est pas disponible.')
    {
        $parametres = [
            "pagetitle" => "Erreur",
            "cheminVueBody" => 'error.php',
            'code' => $code,
            'message' => $message];
        self::afficheVue('view.php',$parametres);
    }

    public static function redirection(string $lienBase){
        header("Location: $lienBase");
        exit();
    }

    public static function connexionRedirect(string $type, string $message){ // pas mis au bon endroit (fait appel à userController dans genericController)
        // permer de vérifier si l'utilisateur est connecter pour le rediriger vers la page de connexion si ce qu'il souhaite faire nécessite d'etre connecté
        if(!ConnexionUtilisateur::estConnecte()){
            MessageFlash::ajouter($type, $message);
            self::redirection('frontController.php?controller=user&action=connexion');
        }
    }


}

