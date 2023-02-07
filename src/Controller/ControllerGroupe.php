<?php

namespace App\Controller;

use App\Lib\ConnexionUtilisateur;
use App\Lib\MessageFlash;
use App\Model\DataObject\Groupe;
use App\Model\Repository\GroupeRepository;
use App\Model\Repository\UserRepository;

class ControllerGroupe extends GenericController
{

    public static function readAll() : void
    {
        if (isset($_POST['title']) AND !empty($_POST['title'])){
            $recherche= strtolower(htmlspecialchars($_POST['title']));
            $groupes = (new GroupeRepository())->search($recherche);
        }
        else{
            $groupes = (new GroupeRepository())->selectAll();
        }
        $privilegeUser = (new UserRepository())->getPrivilege(ConnexionUtilisateur::getLoginUtilisateurConnecte());


        $parametres = array(
            'pagetitle' => 'Liste Groupes',
            'cheminVueBody' => 'groupe/list.php',
            'groupes' => $groupes,
            'privilegeUser' => $privilegeUser
        );
        self::afficheVue('view.php', $parametres);
    }


    public static function read(){
        self::connexionRedirect('warning', 'Connectez-vous pour voir les membres');

        $nomGroupe = htmlspecialchars($_GET['nomGroupe']);
        $groupe = (new GroupeRepository())->select($nomGroupe);

        $params=[
            'pagetitle' => 'Details groupe',
            'cheminVueBody' => '/groupe/detail.php',
            'groupe' => $groupe
        ];
        self::afficheVue('view.php', $params);
    }

    public static  function create(){
        $params = [
            'pagetitle' => 'créez votre groupe',
            'cheminVueBody' => '/groupe/create.php'
        ];
        self::afficheVue('view.php', $params);
    }

    public static function created(){
        self::connexionRedirect('warning', 'Connectez-vous pour créer un groupe');

        $nomGroup = htmlspecialchars($_POST['nomGroupe']);
        $idUser = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        $groupe = new Groupe($nomGroup, $idUser);
        (new GroupeRepository())->sauvegarder($groupe);
        self::redirection('frontController.php?controller=groupe&action=readAll');
    }

    public static function addUserToGroupe(){
        self::connexionRedirect('warning', 'Veuillez vous connecter');

        $nomGroupe = htmlspecialchars($_GET['nomGroupe']);
        $groupe = (new GroupeRepository())->select($nomGroupe);
        if (ConnexionUtilisateur::getLoginUtilisateurConnecte() == $groupe->getIdResponsable()) {
            $action = 'frontController.php?controller=groupe&action=usersAddedToGroupe&nomGroupe='.$nomGroupe;

            if(isset($_POST['filtre'])){
                $users = (new UserRepository())->search(htmlspecialchars($_POST['filtre']));
            }
            else{
                $users = (new UserRepository())->selectAll();
            }

            $idMembres = $groupe->getIdMembres();

            foreach ($users as $user){
                $idUser = $user->getId();
                if(in_array($idUser, $idMembres))
                {
                    $index = array_search($user, $users);
                    unset($users[$index]);
                }
            }
            $params = [
                'pagetitle' => 'ajouter membres groupe',
                'cheminVueBody' => 'question/listPourAjouter.php',
                'action' => $action,
                'users' => $users,
                'privilegeUser' => ConnexionUtilisateur::estAdministrateur()?'Administrateur':'Responsable'
            ];
            self::afficheVue('view.php', $params);
        }
        else{
            MessageFlash::ajouter('warning', 'Vous ne disposez pas des droits pour gérer les membres');
            self::redirection('frontController.php?controller=groupe&action=read&nomGroupe='.$nomGroupe);
        }
    }

    public static function usersAddedToGroupe()
    {
        self::connexionRedirect('warning', 'Veuillez vous connecter');

        $nomGroupe = htmlspecialchars($_GET['nomGroupe']);
        $groupe = (new GroupeRepository())->select($nomGroupe);
        if (ConnexionUtilisateur::getLoginUtilisateurConnecte() == $groupe->getIdResponsable()) {
            if (isset($_POST['user'])) {
                foreach ($_POST['user'] as $idUser) {
                    $groupe->addUser(htmlspecialchars($idUser));
                }
            }
        }

        (new GroupeRepository())->update($groupe);

        $params = [
            'pagetitle' => 'detail groupe',
            'cheminVueBody' => '/groupe/detail.php',
            'groupe' => $groupe
        ];
        self::afficheVue('view.php', $params);
    }
}