<?php

namespace App\Lib;

use App\Model\HTTP\Session;
use App\Model\Repository\UserRepository;

class ConnexionUtilisateur
{
    // L'utilisateur connecté sera enregistré en session associé à la clé suivante
    private static string $cleConnexion = "_utilisateurConnecte";

    public static function connecter(string $loginUtilisateur): void
    {
        Session::getInstance()->enregistrer(ConnexionUtilisateur::$cleConnexion,$loginUtilisateur);
    }

    public static function estConnecte(): bool
    {
        return (Session::getInstance()->lire(ConnexionUtilisateur::$cleConnexion) != null);
    }

    public static function deconnecter(): void
    {
        Session::getInstance()->supprimer(ConnexionUtilisateur::$cleConnexion);
    }

    public static function getLoginUtilisateurConnecte(): ?string
    {

        return Session::getInstance()->lire(ConnexionUtilisateur::$cleConnexion);
    }

    public static function estUtilisateur($id):bool
    {
        return ((new ConnexionUtilisateur())->estConnecte() && (new ConnexionUtilisateur())->getLoginUtilisateurConnecte() == $id);
    }

    public static function estAdministrateur() : bool
    {
        if((new ConnexionUtilisateur())->estConnecte())
        {
            $userRepository = new UserRepository;
            $user = $userRepository->select(self::getLoginUtilisateurConnecte());
            return ($user->getRole() == 'administrateur');
        }
        else
        {
            return false;
        }
    }

    public static function getRole() : string
    {
        $userRepository = new UserRepository;
        $user = $userRepository->select(self::getLoginUtilisateurConnecte());
        return ($user->getRole());
    }
}