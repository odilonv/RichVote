<?php

namespace App\Controller;

use App\Lib\ConnexionUtilisateur;
use App\Lib\MessageFlash;
use App\Lib\MotDePasse;
use App\Lib\VerificationEmail;
use App\Model\DataObject\User;
use App\Model\Repository\DemandeUserRepository;
use App\Model\Repository\GroupeRepository;
use App\Model\Repository\PropositionRepository;
use App\Model\Repository\QuestionRepository;
use App\Model\Repository\UserRepository;


class ControllerUser extends GenericController
{
    public static function accueil()
    {
        self::afficheVue('view.php',[
            "pagetitle" => "Accueil",
            "cheminVueBody" => 'user/accueil.php'
        ]);
    }

    public static function inscription()
    {

        //$identifiant = $_POST['identifiant'];
        //$mdp = $_POST['motDePasse'];
        self::afficheVue('view.php',[
            "pagetitle" => "Inscription",
            "cheminVueBody" => 'user/inscription.php'
        ]);
    }
//
//       public static function created() : void
//    {
//        $intitule = $_POST['titreQuestion'];
//        $nbSections = $_POST['nbSections'];
//
//        $question = new Question(null, $intitule, 'description');
//        $question = (new QuestionRepository)->sauvegarder($question);
//
//        for($i=0; $i<$nbSections; $i++){
//            $section = new Section(null, $question->getId(), "section n°$i", 'description');
//            (new SectionRepository())->sauvegarder($section);
//        }
//
//        $parametres = array(
//            'pagetitle' => 'continuer la création de la question',
//            'cheminVueBody' => 'question/update.php',
//            'question' => (new QuestionRepository())->select($question->getId())
//        );
//
//        self::afficheVue('view.php', $parametres);
//    }


    public static function connexion()
    {
        if((new ConnexionUtilisateur())->estConnecte()){
            MessageFlash::ajouter('info', 'Vous êtes déjà connecté.');
            self::redirection('frontController.php?controller=user&action=read&id='. (new ConnexionUtilisateur())->getLoginUtilisateurConnecte());
        }
        else{
            self::afficheVue('view.php',[
                "pagetitle" => "Connexion",
                "cheminVueBody" => 'user/connexion.php'
            ]);
        }

    }

    public static function connected()
    {
        $id = htmlspecialchars($_POST['id']);
        $mdp =  htmlspecialchars($_POST['mdp']);

        $userRepository = (new UserRepository());
        /** @var User $user */
        $user = $userRepository->select($id);

        $parametres=[];
        if($user != null
            &&( $userRepository->checkCmdp($user->getMdpHache(),$userRepository->setMdpHache($mdp)) ||
             MotDePasse::verifier($mdp, $user->getMdpHache()))
        )
        {
            if(!$user->isVerified())
            {
                $parametres = array(
                    'pagetitle' => 'Valider l\'inscription.',
                    'cheminVueBody' => 'user/validationEmail.php',
                    'idUser'=> $user->getId()
                );
            }
            else
            {
                $connexion = (new ConnexionUtilisateur());
                $connexion->connecter($id);

                MessageFlash::ajouter('info', 'Connecté.');
                self::redirection('frontController.php?controller=user&action=accueil');

            }

        }
        else
        {
            if($user == null)
            {
                $parametres = array(
                    'pagetitle' => 'Erreur',
                    'cheminVueBody' => 'user/connexion.php',
                    'msgErreurId' =>  "Cet utilisateur n'existe pas."
                );
            }
            else if(!($userRepository->checkCmdp($user->getMdpHache(),$userRepository->setMdpHache($mdp))) ||
                !MotDePasse::verifier($mdp, $user->getMdpHache()))
            {
                $parametres = array(
                    'pagetitle' => 'Erreur',
                    'cheminVueBody' => 'user/connexion.php',
                    'msgErreurMdp' =>  "Mot de passe incorrect."
                );
            }
        }
        self::afficheVue('view.php', $parametres);


    }

    public static function inscrit() : void
    {
        $idUser = htmlspecialchars($_POST['identifiant']);
        $mdp = htmlspecialchars($_POST['motDePasse']);
        $cmdp = htmlspecialchars($_POST['confirmerMotDePasse']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $nom = htmlspecialchars($_POST['nom']);
        $email = htmlspecialchars($_POST['email']);

        $parametres = [];
        $mdpconfig = new MotDePasse();
        $userRepository = new UserRepository();
        $user = new User($idUser, $mdpconfig->hacher($mdp), $prenom, $nom, 'invité', $email);

        if ($userRepository->checkCmdp($mdp, $cmdp)          //check si aucune contrainte n'a été violée
            && $userRepository->checkId($idUser)
            && $userRepository->checkEmail($email)
            && $userRepository->checkMdp($mdp))
        {
            $userRepository->sauvegarder($user);
            $parametres = array(
                'pagetitle' => 'Valider l\'inscription.',
                'cheminVueBody' => 'user/validationEmail.php',
                'idUser'=> $idUser
            );
            $userRepository->mailDeValidation($user);

        }
        else
        {
            if (!$userRepository->checkCmdp($mdp, $cmdp)) {

                $parametres = array(
                    'pagetitle' => 'Erreur',
                    'cheminVueBody' => 'user/inscription.php',
                    'persistanceValeurs' => array('idUser' => $idUser,
                                                    'nom' => $nom,
                                                    'prenom' => $prenom),
                    'msgErreur' =>  'Les mots de passes doivent être identiques.'
                );

            }
            if(!$userRepository->checkEmail($email))
            {
                $parametres = array(
                    'pagetitle' => 'Erreur',
                    'cheminVueBody' => 'user/inscription.php',
                    'persistanceValeurs' => array('idUser' => $idUser,
                        'nom' => $nom,
                        'prenom' => $prenom),
                    'msgErreur' =>  'L\'email '.$email.' est déjà utilisé.'
                );
            }
            if (!$userRepository->checkId($idUser)) {
                $parametres = array(
                    'pagetitle' => 'Erreur',
                    'cheminVueBody' => 'user/inscription.php',
                    'persistanceValeurs' => array('nom' => $nom,
                                                    'prenom' => $prenom,
                        'email'=> $email),
                    'msgErreur' =>  'L\'identifiant '.$idUser.' est déjà utilisé.'
                );
            }
            if(!$userRepository->checkMdp($mdp))
            {
                $parametres = array(
                    'pagetitle' => 'Erreur',
                    'cheminVueBody' => 'user/inscription.php',
                    'persistanceValeurs' => array('nom' => $nom,
                        'prenom' => $prenom,
                        'email'=> $email),
                    'msgErreur' =>  'Le mot de passe n\'est pas assez sécurisé.',
                    'msgErreurMdp' => $userRepository->checkMdp($mdp)

                );
            }

        }
        self::afficheVue('view.php', $parametres);
    }

    public static function userValide()
    {
        $userRepository = new UserRepository();
        $user = $userRepository->select(htmlspecialchars($_GET['idUser']));

        if($userRepository->checkCmdp($user->getNonce(),htmlspecialchars($_POST['nonce']))
        && $user != null)
        {
            $userRepository->validerUser($user);
            $connexion = new ConnexionUtilisateur();
            $connexion->connecter(htmlspecialchars($_GET['idUser']));

            $parametres = array(
                'pagetitle' => 'Inscription validée !',
                'cheminVueBody' => 'user/accueil.php',
            );
        }
        else
        {
            MessageFlash::ajouter('info', 'Le code ne correspond pas à celui envoyé à l\'adresse '.$user->getEmail().'.');
            $parametres = array(
                'pagetitle' => 'Erreur.',
                'cheminVueBody' => 'user/validationEmail.php',
            );
        }

        self::afficheVue('view.php', $parametres);
    }

    public static function renvoyerCode()
    {
        $idUser = htmlspecialchars($_GET['idUser']);
        $userRepository = new UserRepository();
        $user = $userRepository->select($idUser);
        if($user != null)
        {
            $userRepository->mailDeValidation($user);
            $parametres = array(
                'pagetitle' => 'Valider l\'inscription.',
                'cheminVueBody' => 'user/validationEmail.php',
                'idUser'=> $idUser
            );
            MessageFlash::ajouter('info', 'Code renvoyé.');
            self::afficheVue('view.php', $parametres);
        }
        else
        {
            MessageFlash::ajouter('info', 'erreur');
            self::redirection('frontController.php?controller=user&action=connexion');
        }



    }



    public static function readAll() : void
    {
        if(ConnexionUtilisateur::estConnecte() && (new UserRepository)->select(ConnexionUtilisateur::getLoginUtilisateurConnecte())->isverified())
        {
            if (isset($_POST['title']) AND !empty($_POST['title'])){
                $recherche= strtolower(htmlspecialchars($_POST['title']));
                $arrayUser = (new UserRepository())->search($recherche);
            }
            else{
                $arrayUser = (new UserRepository())->selectAllValide();
            }

            $privilegeUser=(new UserRepository())->getPrivilege(ConnexionUtilisateur::getLoginUtilisateurConnecte());


            $parametres = array(
                'pagetitle' => 'Liste Utilisateurs',
                'cheminVueBody' => 'user/list.php',
                'users' => $arrayUser,
                'privilegeUser' => $privilegeUser
            );
        }
        else
        {
            $parametres = array(
                'pagetitle' => 'Liste Utilisateurs',
                'cheminVueBody' => 'user/list.php'
            );
        }

        self::afficheVue('view.php', $parametres);
    }

    public static function read():void
    {
        self::connexionRedirect('warning', 'Connectez-vos');
        $groupes = GroupeRepository::selectAllGroupeIdUser(htmlspecialchars($_GET['id']));
        $questions = (new QuestionRepository())->selectAllfromOrganisateur(htmlspecialchars($_GET['id']));
        $propositions = (new PropositionRepository())->selectAllfromResponsable(htmlspecialchars($_GET['id']));
        $user = (new UserRepository())->select(htmlspecialchars($_GET['id']));

        $demandes = DemandeUserRepository::selectAllDemandeDemandeur(htmlspecialchars($_GET['id']));

        $parametres = array(
            'pagetitle' => 'Détails user',
            'cheminVueBody' => 'user/detail.php',
            'user' => $user,
            'propositions' => $propositions,
            'questions' => $questions,
            'groupes' => $groupes,
            'demandes' => $demandes
        );

        self::afficheVue('view.php', $parametres);
    }

    public static function update():void
    {
        $mdpOublie = false;
        if((new UserRepository())->selectMdpHache(htmlspecialchars($_GET['id'])) != null) $mdpOublie=true;

        if((new UserRepository())->select(htmlspecialchars($_GET['id'])) != null || (new UserRepository())->selectMdpHache((htmlspecialchars($_GET['id']))) != null)
        {
            if((new UserRepository())->select(htmlspecialchars($_GET['id'])) != null )
            {
                $user = (new UserRepository())->select(htmlspecialchars($_GET['id']));
                if(!(new ConnexionUtilisateur())->estUtilisateur($user->getId()) && !(new ConnexionUtilisateur())->estAdministrateur())
                {
                    MessageFlash::ajouter('danger', "Vous n'êtes pas autorisé à acceder à cette page.");
                    self::redirection('frontController.php?controller=user&action=readAll');
                }
            }
            else
            {
                $user = (new UserRepository())->selectMdpHache(htmlspecialchars($_GET['id']));
            }
            $parametres = array(
                'pagetitle' => 'Mettre à jour un utilisateur',
                'cheminVueBody' => 'user/update.php',
                'user' => $user,
                'mdpOublie' => $mdpOublie
            );
            self::afficheVue('view.php', $parametres);
        }
        else
        {
            MessageFlash::ajouter('warning', "Cet utilisateur n'existe pas.");
            self::redirection('frontController.php?controller=user&action=readAll');
        }
    }

    public static function updated() : void
    {
        $userRepository = new UserRepository();
        $mdp = new MotDePasse();
        $user = $userRepository->select(htmlspecialchars($_GET['id']));
        $parametres=[];

        if( isset($_POST['aMdp']))
        {
            $aMdp = htmlspecialchars($_POST['aMdp']);
            $nMdp = htmlspecialchars($_POST['nMdp']);
            $cNMdp = htmlspecialchars($_POST['cNMdp']);


            if ($userRepository->checkCmdp($nMdp, $cNMdp) &&
                $mdp->verifier($aMdp, $user->getMdpHache()))
            {
                $user->setMdp($nMdp);
                $userRepository->update($user);
                $parametres = array(
                    'pagetitle' => 'Mot de passe mis à jour.',
                    'cheminVueBody' => 'user/accueil.php',
                );
            }
            else
            {
                $mdpOublie = false;
                if((new UserRepository())->selectMdpHache(htmlspecialchars($_GET['id'])) != null) $mdpOublie=true;

                if (!$userRepository->checkCmdp($nMdp, $cNMdp)) {


                    $parametres = array(
                        'pagetitle' => 'Erreur',
                        'cheminVueBody' => 'user/update.php',
                        'msgErreur' =>  'Les mots de passes doivent être identiques.',
                        'user' => $user,
                        'mdpOublie' => $mdpOublie
                    );

                }
                if (!$mdp->verifier($aMdp, $user->getMdpHache())) {
                    $parametres = array(
                        'pagetitle' => 'Erreur',
                        'cheminVueBody' => 'user/update.php',
                        'msgErreur' =>  'L\'ancien mot de passe ne correspond pas.',
                        'user' => $user,
                        'mdpOublie' => $mdpOublie
                    );
                }
            }
        }
        else if(isset($_POST['identifiant']))
        {
            if($userRepository->checkId(htmlspecialchars($_POST['identifiant'])))
            {

                $user->setId(htmlspecialchars($_POST['identifiant']));

                $userRepository->update($user);
                MessageFlash::ajouter('info', 'Identifiant mis à jour.');
                self::redirection('frontController.php?controller=user&action=read&id='.rawurlencode($user->getId()));
            }
            else
            {
                MessageFlash::ajouter('danger', 'Identifiant déjà utilisé.');
                self::redirection('frontController.php?controller=user&action=read&id='.rawurlencode($user->getId()).'&modif=identifiant');
            }
        }
        else if(isset($_POST['nom']))
        {
            $user->setNom(htmlspecialchars($_POST['nom']));
            $userRepository->update($user);
            MessageFlash::ajouter('info', 'Nom mis à jour.');
            self::redirection('frontController.php?controller=user&action=read&id='.rawurlencode($user->getId()));
        }
        else if(isset($_POST['prenom']))
        {
            $user->setPrenom(htmlspecialchars($_POST['prenom']));
            $userRepository->update($user);
            MessageFlash::ajouter('info', 'Prenom mis à jour.');
            self::redirection('frontController.php?controller=user&action=read&id='.rawurlencode($user->getId()));
        }
        else if(isset($_POST['email']))
        {
            $user->setEmail(htmlspecialchars($_POST['email']));
            $userRepository->update($user);
            MessageFlash::ajouter('info', 'Prenom mis à jour.');
            self::redirection('frontController.php?controller=user&action=read&id='.rawurlencode($user->getId()));
        }
        self::afficheVue('view.php', $parametres);
    }

    public static function deconnexion()
    {
        (new ConnexionUtilisateur())->deconnecter();
        MessageFlash::ajouter('info', 'Deconnecté.');
        self::redirection('frontController.php?controller=user&action=accueil');
    }

    public static function delete()
    {
        if((new UserRepository())->select(htmlspecialchars($_GET['id'])) != null)
        {
            $user = (new UserRepository())->select(htmlspecialchars($_GET['id']));
            if(!(new ConnexionUtilisateur())->estUtilisateur($user->getId()) && !(new ConnexionUtilisateur())->estAdministrateur())
            {
                MessageFlash::ajouter('danger', "Vous n'êtes pas autorisé à acceder à cette page.");
                self::redirection('frontController.php?controller=user&action=readAll');
            }

            $parametres = array(
                'pagetitle' => 'Suppression du compte',
                'cheminVueBody' => 'user/delete.php',
                'user' => $user);
            self::afficheVue('view.php', $parametres);
        }
        else
        {
            MessageFlash::ajouter('warning', "Cet utilisateur n'existe pas.");
            self::redirection('frontController.php?controller=user&action=readAll');
        }
    }

    public static function deleted()
    {

        if(!(new ConnexionUtilisateur())->estAdministrateur())
        {
            $mdp = htmlspecialchars($_POST['mdp']);
            $cMdp = htmlspecialchars($_POST['cMdp']);
        }
        $userRepository = new UserRepository();
        $user = $userRepository->select(htmlspecialchars($_GET['id']));

        if ((new ConnexionUtilisateur())->estAdministrateur()||($userRepository->checkCmdp($mdp, $cMdp)
        && MotDePasse::verifier($mdp,$user->getMdpHache())))
        {
            $userRepository->delete($user->getId());
            MessageFlash::ajouter('info', 'Profil supprimé.');
            if((new ConnexionUtilisateur())->getLoginUtilisateurConnecte() == $user->getId())
            {
                ConnexionUtilisateur::deconnecter();
                self::redirection('frontController.php?controller=user&action=accueil');
            }
            else
            {
                self::redirection('frontController.php?controller=user&action=readAll');
            }
        }
        else
        {
            if(!$userRepository->checkCmdp($mdp, $cMdp))
            {
                MessageFlash::ajouter('danger', 'Les mots de passe ne correspondent pas.');
                self::redirection('frontController.php?controller=user&action=delete&id='.rawurlencode($user->getId()));
            }
            else if(!$userRepository->checkCmdp($user->getMdpHache(), $userRepository->setMdpHache($mdp)))
            {
                MessageFlash::ajouter('danger', 'Mot de passe incorrect.');
                self::redirection('frontController.php?controller=user&action=delete&id='.rawurlencode($user->getId()));
            }

        }
    }

    public static function mdpOublie()
    {
        if(!ConnexionUtilisateur::estConnecte())
        {
            $parametres = array(
                'pagetitle' => 'Récuperation du mot de passe',
                'cheminVueBody' => 'user/mdpOublie.php');

            self::afficheVue('view.php', $parametres);
        }
        else
        {
            MessageFlash::ajouter('info', 'Vous êtes déjà connecté.');
            self::redirection('frontController.php?controller=user&action=accueil');
        }

    }

    public static function emailRecup()
    {
        $userRepository = new UserRepository();

        if(isset($_POST['emailRecup']) && $userRepository->emailExiste(htmlspecialchars($_POST['emailRecup'])))
        {
            VerificationEmail::envoiEmailRecuperation(htmlspecialchars($_POST['emailRecup']));
            MessageFlash::ajouter('info', 'Email de récupération envoyé.');
            self::redirection('frontController.php?controller=user&action=mdpOublie');
        }
        else
        {
            MessageFlash::ajouter('info', 'Cet email ne figure pas dans la base de données.');
            self::redirection('frontController.php?controller=user&action=update');
        }

    }

    public static function updatedMdpOublie() : void
    {
        $userRepository = new UserRepository();
        $id = htmlspecialchars($_GET['id']);
        $user = $userRepository->selectMdpHache(htmlspecialchars($_GET['id']));
        $nMdp = htmlspecialchars($_POST['nMdp']);
        $cNMdp = htmlspecialchars($_POST['cNMdp']);

        if((new ConnexionUtilisateur())->estConnecte())
        {
            MessageFlash::ajouter('danger', 'Vous n\'êtes  pas autorisé à accéder à cette page.');
            self::redirection('frontController.php?controller=user&action=accueil');
        }
        if ($userRepository->checkCmdp($nMdp, $cNMdp)) {
            $user->setMdp($nMdp);
            $userRepository->update($user);


            $connexion = new ConnexionUtilisateur();
            $connexion->connecter($user->getId());

            MessageFlash::ajouter('info', 'Mot de passe mis à jour.');
            self::redirection('frontController.php?controller=user&action=accueil');
        } else {
            MessageFlash::ajouter('info', 'Les mots de passe ne correspondent pas.');
            self::redirection('frontController.php?controller=user&action=update&id='.rawurlencode($id));
        }
    }



}
