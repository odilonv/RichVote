<?php

namespace App\Controller;

use App\Lib\ConnexionUtilisateur;
use App\Lib\MessageFlash;
use App\Model\DataObject\Demande;
use App\Model\DataObject\Proposition;
use App\Model\Repository\CommentaireRepository;
use App\Model\Repository\DemandeUserRepository;
use App\Model\Repository\GroupeRepository;
use App\Model\Repository\PropositionRepository;
use App\Model\Repository\QuestionRepository;
use App\Model\Repository\SectionRepository;
use App\Model\Repository\UserRepository;

class ControllerProposition extends GenericController
{
    public static function readAll() : void
    {
        self::connexionRedirect('warning', 'Connectez-vous pour voir les propositions');
        $idQuestion = htmlspecialchars($_GET['id']);
        if(htmlspecialchars($_GET['id'])=='')
        {
            self::redirection('frontController.php?controller=question&action=readAll');
        }

        $listePropositions = (new PropositionRepository())->selectAllByDate($idQuestion);

        $parametres = array(
            'pagetitle' => 'Liste Propositions',
            'cheminVueBody' => 'proposition/list.php',
            'propositions' => $listePropositions
        );

        self::afficheVue('view.php',$parametres);
    }

    public static function read() : void
    {
        self::connexionRedirect('info', 'Connectez-vous pour accéder aux propositions');
        if(htmlspecialchars($_GET['id'])=='')
        {
            self::redirection('frontController.php?controller=question&action=readAll');
        }
        $idProposition = htmlspecialchars($_GET['id']);

        $proposition = (new PropositionRepository())->select($idProposition);

        $demandes = DemandeUserRepository::selectAllDemandeAuteurProposition($proposition);

        $idUser = ConnexionUtilisateur::getLoginUtilisateurConnecte();


        $roleProposition = '';
        if($proposition->getIdResponsable()==$idUser){
            $roleProposition='responsable';
        }
        else{
            foreach ($proposition->getIdAuteurs() as $idAuteur){
                if($idAuteur==$idUser){
                    $roleProposition='auteur';
                }
            }
        }

        $commentaires = (new CommentaireRepository())->selectAllProp($idProposition);

        $parametres = array(
            'pagetitle' => 'Détail Proposition',
            'cheminVueBody' => 'proposition/detail.php',
            'proposition' => $proposition,
            'demandes' => $demandes,
            'commentaires'=>$commentaires,
            'roleProposition' => $roleProposition,
            'peutModifier' => (new QuestionRepository())->select($proposition->getIdQuestion())->getCurrentPhase()->getType()=='redaction'
        );

        self::afficheVue('view.php', $parametres);
    }

    public static function update(){
        self::connexionRedirect('warning', 'Veuillez vous connecter');
        $idProposition = htmlspecialchars($_GET['id']);

        $proposition = (new PropositionRepository())->select($idProposition);
        $question = (new QuestionRepository())->select($proposition->getIdQuestion());

        if($question->getCurrentPhase()->getType()!='redaction'){
            MessageFlash::ajouter('danger', 'Modification impossible');
            self::read();
        }
        else if(in_array(ConnexionUtilisateur::getLoginUtilisateurConnecte(), $proposition->getIdAuteurs())){
            $parametres = array(
                'pagetitle' => 'Modifier Proposition',
                'cheminVueBody' => 'proposition/update.php',
                'proposition' => $proposition
            );

            self::afficheVue('view.php', $parametres);
        }
        else{
            MessageFlash::ajouter("danger", 'vous n\'avez pas le droit de modiifier cette proposition');
            self::redirection("frontController.php?controller=proposition&action=read&id=$idProposition");
        }
    }

    public static function updated() : void
    {
        self::connexionRedirect('warning', 'Veuillez vous connecter');
        $idProposition = htmlspecialchars($_GET['id']);
        $proposition = (new PropositionRepository())->select($idProposition);
        $question = (new QuestionRepository())->select($proposition->getIdQuestion());
        if($proposition==null){
            MessageFlash::ajouter('danger', "La question avec l'id suivant : " . htmlspecialchars($_GET['id']) . "n'existe pas");
            self::redirection('frontController.php?controller=question&action=readAll');
        }
        else if($question->getCurrentPhase()->getType()!='redaction'){
            MessageFlash::ajouter('danger', 'Modification impossible');
            self::read();
        }
        else {
            $sectionsText = $proposition->getSectionsTexte();

            foreach ($sectionsText as $index=>$infos){
                $infos['texte'] = $_POST['texte'][$infos['section']->getId()];
                $sectionsText[$index] = $infos;
            }

            $proposition->setSectionsTexte($sectionsText);
            $proposition->setIntitule(htmlspecialchars($_POST['intitule']));

            (new PropositionRepository())->update($proposition);
            self::read();
        }
    }

    public static function create(){
        self::connexionRedirect('warning', 'Veuillez vous connecter');
        $idQuestion = htmlspecialchars($_GET['id']);

        $idUser = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        if((new UserRepository())->getRoleQuestion($idUser, $idQuestion) == 'responsable')
        {
            if((new UserRepository())->aDejaCreeProp($idUser, $idQuestion))
            {
                MessageFlash::ajouter('info','Vous avez déjà créé une question, vous pouvez modifier ou supprimer celle existante.');
                self::redirection('frontController.php?controller=proposition&action=read&id='.(new UserRepository())->getPropDejaCree($idUser,$idQuestion));
            }
            $proposition = (new PropositionRepository())->sauvegarder(new Proposition(null, $idQuestion, ConnexionUtilisateur::getLoginUtilisateurConnecte(),null, null, false, []));

            $parametres = array(
                'pagetitle' => 'Créer Proposition',
                'cheminVueBody' => 'proposition/update.php',
                'proposition' => $proposition
            );
            self::afficheVue('view.php', $parametres);
        }
        else{
            MessageFlash::ajouter('warning', 'Vous ne disposez pas des droits pour créer une proposition');
            self::redirection('frontController.php?controller=question&action=read&id='.$idQuestion);
        }
    }

    public static function delete(){
        self::connexionRedirect('warning', 'Veuillez vous connecter');
        $idProposition = htmlspecialchars($_GET['id']);

        $proposition = (new PropositionRepository())->select($idProposition);
        if($proposition->getIdResponsable()==ConnexionUtilisateur::getLoginUtilisateurConnecte()){
            (new PropositionRepository())->delete($idProposition);
            MessageFlash::ajouter('info', 'La proposition : "' . $proposition->getIntitule() . '" a bien été suprimée');
            self::redirection('frontController.php?controller=proposition&action=readAll&id='. $proposition->getIdQuestion());
        }
        else{
            MessageFlash::ajouter('warning', 'Vous ne pouvez pas supprimer cette proposition');
            self::redirection('frontController.php?controller=proposition&action=read&id='. $proposition->getId());
        }

        // n'est pas utilisé en l'état
        if($proposition==null) {
            MessageFlash::ajouter('warning', "La proposition n'existe pas");
            self::redirection('frontController.php?controller=question&action=readAll');
        }
    }

    public static function selectAllWithScore(){
        self::connexionRedirect('warning', 'Connectez-vous');
        $idPhase = htmlspecialchars($_GET['idPhase']);
        $scores = [];
        $propositions = [];

        $propositionsScore = (new PropositionRepository())->selectAllWithScore($idPhase);
        foreach ($propositionsScore as $proposition){
            $propositions[] = $proposition[0];
            $scores[$proposition[0]->getId()] = $proposition[1];
        }

        $param = [
            'pagetitle' => 'Scores',
            'cheminVueBody' => '/proposition/list.php',
            'propositions' => $propositions,
            'scores' => $scores
        ];

        self::afficheVue('view.php', $param);
    }

    public static function ajtCommentaire()
    {
        self::connexionRedirect('warning', 'Connectez-vous');
        $commentaire = htmlspecialchars($_POST['commentaire']);
        $userRepository = new UserRepository();
        $idUser = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        $idProposition=htmlspecialchars($_GET['id']);
        $date = date("'d/m/y G:i:s'");
        $date .=",'dd/mm/yy hh24:mi:ss'";

        $commentaireRepository= new CommentaireRepository();
        $commentaireRepository->commenter($idProposition,$idUser,$commentaire,$date);

        MessageFlash::ajouter('info','Commentaire ajouté');
        self::redirection('frontController.php?controller=proposition&action=read&id='.$idProposition);
    }

    public static function deleteCommentaire():void
    {// s'assurer que le commentaire nous apppartient
        self::connexionRedirect('warning', 'Connectez-vous');
        $idCommentaire= htmlspecialchars($_GET['idCommentaire']);
        $idProposition= htmlspecialchars($_GET['id']);

        (new CommentaireRepository())->deleteCommentaire($idCommentaire);


        MessageFlash::ajouter('info','Vous n\'avez pas aimé ce commentaire.');
        self::redirection('frontController.php?controller=proposition&action=read&id='.$idProposition);
    }

    public static function likeCommentaire():void
    {
        self::connexionRedirect('warning', 'Connectez-vous');
        $idCommentaire= htmlspecialchars($_GET['idCommentaire']);
        $idProposition= htmlspecialchars($_GET['id']);

        (new CommentaireRepository())->liker($idCommentaire);

        self::redirection('frontController.php?controller=proposition&action=read&id='.$idProposition);
    }

    public static function dislikeCommentaire():void
    {
        self::connexionRedirect('warning', 'Connectez-vous');
        $idCommentaire= htmlspecialchars($_GET['idCommentaire']);
        $idProposition= htmlspecialchars($_GET['id']);

        (new CommentaireRepository())->disliker($idCommentaire);

        self::redirection('frontController.php?controller=proposition&action=read&id='.$idProposition);
    }

    public static function addAuteursToProposition(){
        self::connexionRedirect('warning', 'Connectez-vous');
        $proposition = (new PropositionRepository())->select(htmlspecialchars($_GET['id']));
        $responsableProposition = (new PropositionRepository())->selectResponsable(htmlspecialchars($_GET['id']));

        if(isset($_GET['entite'])){
            $entite = htmlspecialchars($_GET['entite']);
        }
        else{
            $entite = 'user';
        }

        if($proposition->getIdResponsable() == ConnexionUtilisateur::getLoginUtilisateurConnecte()){

            $action = 'frontController.php?controller=proposition&action=auteursAdded&id='.$proposition->getId();
            $param = [
                'pagetitle' => 'ajout d\'auteurs',
                'cheminVueBody' => 'proposition/listPourAjouter.php',
                'privilegeUser' => 'responsable',
                'action' => $action,
                'responsableProposition' => $responsableProposition
            ];

            if($entite=='user') {
                if(isset($_POST['filtre'])){
                    $users = (new UserRepository())->search(htmlspecialchars($_POST['filtre']));
                }
                else{
                    $users = (new UserRepository())->selectAll();
                }

                $idAuteurs = $proposition->getIdAuteurs();
                foreach ($users as $index => $user) {
                    if (in_array($user->getId(), $idAuteurs)) {
                        unset($users[$index]);
                    }
                }
                $param['users'] = $users;
            }
            else{
                if(isset($_POST['filtre'])){
                    $groupes = (new GroupeRepository())->search(htmlspecialchars($_POST['filtre']));
                }
                else{
                    $groupes = (new GroupeRepository())->selectAll();
                }
                $param['groupes'] = $groupes;
            }

            self::afficheVue('view.php', $param);
        }
    }

    public static function auteursAdded(){
        self::connexionRedirect('warning', 'Connectez-vous');
        $proposition = (new PropositionRepository())->select(htmlspecialchars($_GET['id']));

        if(isset($_GET['entite'])){
            $entite = htmlspecialchars($_GET['entite']);
        }
        else{
            $entite = 'user';
        }

        if($entite=='user'){
            (new PropositionRepository())->addAuteursProposition($_POST['list'], $proposition);
        }
        else{
            (new PropositionRepository())->addGroupeAuteur($_POST['list'], $proposition);
        }
        MessageFlash::ajouter('success', 'Auteurs ajoutés');

        self::redirection('frontController.php?controller=proposition&action=read&id='.$proposition->getId());
    }

    public static function addDemandeAuteur(){
        self::connexionRedirect('warning', 'Connectez-vous');
        $idUser = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        $idProposition =htmlspecialchars($_GET['id']);

        $proposition = (new PropositionRepository())->select($idProposition);

        if($proposition->getIdResponsable() == $idUser){
            MessageFlash::ajouter('danger', 'Vous n\'êtes pas authorisé');
            self::redirection('frontController.php?controller=proposition&action=read&id='.$idProposition);
        }

        $question = (new QuestionRepository())->select($proposition->getIdQuestion());
        $user = (new UserRepository())->select($idUser);
        $demande = new Demande('auteur', $question, $user, $proposition);

        DemandeUserRepository::sauvegarder($demande);

        MessageFlash::ajouter('success', 'Demande effectuée');
        ControllerQuestion::readAll();
    }

    public static function readDemandeAuteur() : void{
        self::connexionRedirect('warning', 'Connectez-vous');
        $idProposition = htmlspecialchars($_GET['id']);

        $proposition = (new PropositionRepository())->select($idProposition);
        if($proposition->getIdResponsable()==ConnexionUtilisateur::getLoginUtilisateurConnecte()){
            $demandes = DemandeUserRepository::selectAllDemandeAuteurProposition($proposition);

            $action = 'frontController.php?action=demandesAccepted&controller=Proposition&id=' . $idProposition;

            $privilege = 'Responsable';
            $parametres = [
                'pagetitle' => 'demandes en attentes',
                'cheminVueBody' => 'gestionRoles/readDemandes.php',
                'demandes' => $demandes,
                'action' => $action,
                'privilegeUser' => $privilege
            ];
            self::afficheVue('view.php', $parametres);
        }
        else{
            MessageFlash::ajouter('warning', 'Vous ne pouvez pas accéder à cette fonctionnalité');
            self::read();
        }
    }

    public static function demandesAccepted(){
        self::connexionRedirect('warning', 'Connectez-vous');

        $idProposition = htmlspecialchars($_GET['id']);
        $proposition = (new PropositionRepository())->select($idProposition);
        if($proposition->getIdResponsable()==ConnexionUtilisateur::getLoginUtilisateurConnecte()) {
            $demandesProposition = DemandeUserRepository::selectAllDemandeAuteurProposition($proposition);
            $acceptees = [];
            foreach ($_POST['user'] as $idUser) {
                $acceptees[] = htmlspecialchars($idUser);
            }
            foreach($demandesProposition as $demande){
                if(in_array($demande->getDemandeur()->getId(), $acceptees)){
                    DemandeUserRepository::delete($demande);
                }
            }
            (new PropositionRepository())->addAuteursProposition($acceptees, $proposition);

            MessageFlash::ajouter('success', 'Toutes les demandes ont été acceptées');
        }
        else{
            MessageFlash::ajouter('warning', 'Vous ne pouvez pas accéder à cette fonctionnalité');
        }
        self::redirection('frontController.php?controller=proposition&action=read&id='.$idProposition);
    }

    public static function likeSectionProposition()
    {
        $idSection = htmlspecialchars($_GET['id']);
        $idProposition = htmlspecialchars($_GET['idProposition']);

        if (ConnexionUtilisateur::estConnecte()) {
            $idUser = ConnexionUtilisateur::getLoginUtilisateurConnecte();
            $sectionRepository = new SectionRepository();
            if ($sectionRepository->userALike($idSection, $idUser,$idProposition)) {
                $sectionRepository->deliker($idSection, $idUser,$idProposition);
            } else {
                $sectionRepository->liker($idSection, $idUser,$idProposition);
            }
        }
        self::redirection('frontController.php?controller=proposition&action=read&id=' . $idProposition);


    }



}