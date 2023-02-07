<?php

use App\Model\DataObject\Demande;
use App\Model\DataObject\Proposition;
use App\Lib\ConnexionUtilisateur;
use  App\Model\DataObject\Commentaire;
use App\Model\Repository\SectionRepository;

/** @var Proposition $proposition
 * @var Array $commentaires
 * @var Commentaire $commentaire
 * @var string $roleProposition
 * @var Demande[] $demandes
 * @var bool $peutModifier
 */
$nbDemandes= sizeof($demandes);
$idProposition = $proposition->getId();
?>
<div class="block" >
    <div class="column">
        <div class="text-box">
            <div class="ligneExt"> <a id="fleche" class="optQuestion" href="frontController.php?controller=question&action=read&id=<?=$proposition->getIdQuestion()?>">↩</a>
                <?=($roleProposition=='responsable'&&$nbDemandes > 0)?'<div class="ligneExt"><span></span><div class="iconsNotifs" id="iconNotification2">'.$nbDemandes.'</div></div><a  class="optQuestion" href="frontController.php?controller=proposition&action=readDemandeAuteur&id=' . rawurlencode($idProposition) .'"> Demandes de Co-Auteurs </a>':''?>
            </div>
            <div class="ligneExt">
                <h1><?=htmlspecialchars($proposition->getIntitule())?></h1>
                <h3>Rédigée par : <?=htmlspecialchars($proposition->getIdResponsable())?></h3></div>
        <div class="ligneExt"><div class="ligne"></div></div>
        <div class="ligneExt">
            <?php
            $btnModifier = '';
            if($peutModifier){
                $btnModifier = '<a href=frontController.php?controller=proposition&action=update&id=' . rawurlencode($idProposition) . ' ><img class="icons" title="Modifier Proposition" alt="Modifier Proposition" src="../assets/img/icons8-crayon-48.png"></a>';
            }
            if ($roleProposition=='responsable'){
                echo '<div class="ligneAlign">'.
                    $btnModifier .
                    '<a href=frontController.php?controller=proposition&action=delete&id='. rawurlencode($idProposition) . ' ><img class="icons" id="poubelle" title="Supprimer Proposition" alt="Supprimer Proposition" src="../assets/img/icons8-poubelleBlanc.svg"></a>' .
                    '<a href="frontController.php?controller=proposition&action=addAuteursToProposition&id=' . rawurlencode($idProposition) . '"><img class="icons" title="Ajouter Utilisateurs" alt="Ajouter Utilisateurs" src="../assets/img/icons8-ajtUserBlanc-48.png"></a>'
                    .'</div>';
            }
            elseif ($roleProposition=='auteur'){
                echo $btnModifier;
            }
            elseif ($roleProposition!='auteur'){
                echo '<a class="optQuestion" href="frontController.php?controller=proposition&action=addDemandeAuteur&id=' . $proposition->getId() . '"> devenir auteur de cette proposition</a>';
            }?>
        </div>
        <br>

            <?php
            $i=0;
            foreach ($proposition->getSectionsTexte() as $infos){
                $idSection = $infos['section']->getId();
                $texte = $infos['texte'];
                $nbLikes = (new SectionRepository())->getNbLikes($idSection,$idProposition);
                $i++;
                echo '<div class="ligneExt" id="section'. $i .'"><h3 id="sections">'. $i .'. ' . (new SectionRepository())->select($idSection)->getIntitule() . "</h3></div>";
                echo "<div class='ligne'></div>";
                echo $texte ;
                if((new SectionRepository())->userALike($idSection,ConnexionUtilisateur::getLoginUtilisateurConnecte(),$idProposition))
                {
                    echo '<div><a href="frontController.php?controller=proposition&action=likeSectionProposition&id='.$idSection.'&idQuestion='.$proposition->getIdQuestion().'&idProposition='.$proposition->getId().'"><img alt="Aimé" src="../assets/img/icons8-jaimeBleu.png"></a>     '.$nbLikes.'</div></li>';
                }
                else
                {
                    echo '<div><a href="frontController.php?controller=proposition&action=likeSectionProposition&id='.$idSection.'&idQuestion='.$proposition->getIdQuestion().'&idProposition='.$proposition->getId().'"><img alt="Aime" src="../assets/img/icons8-jaimeBlanc.png"></a>     '.$nbLikes.'</div></li>';
                }
                echo '<br>';
            }?>
        </div>

        <div class="text-box" >
            <h3> Commentaires  </h3>
            <form action="frontController.php?controller=proposition&action=ajtCommentaire&id=<?php echo $_GET['id'] ?>" method="post">
                <label for="commentaire"></label>
                <input  type="text" name="commentaire" id="commentaire" required>
                <div class="ligneExt"><div></div>
                    <input type="image" src="../assets/img/icons8-coche-white.svg" alt="Submit" /></div>
            </form>
            <div class="descG"></div>

            <?php
            if(!empty($commentaires))
            {
                foreach ($commentaires as $commentaire)
                {
                    if($commentaire->getIDUSER() == ConnexionUtilisateur::getLoginUtilisateurConnecte() || ConnexionUtilisateur::estAdministrateur())
                    {
                        echo '<li class="ligneExt"><div><div  class="descP"><a href="frontController.php?controller=user&action=read&id='.$commentaire->getIDUSER().'">'.$commentaire->getIDUSER().'</a></div><div class="descP" style="margin-left: 20px;color: black">'.$commentaire->getTEXTE(). ' </div><div style="color: #adadad;">' .$commentaire->getDATECOMMENTAIRE().' </div></div>   <div><a href="frontController.php?controller=proposition&action=deleteCommentaire&id='.$proposition->getId().'&idCommentaire='.$commentaire->getIDCOMMENTAIRE() .'"><img alt="Supprimer Commentaire" src="../assets/img/icons8-poubelleNoir.svg"></a></div></li>';
                    }
                    else
                    {
                        echo '<li class="ligneExt"><div><div  class="descP"><a href="frontController.php?controller=user&action=read&id='.$commentaire->getIDUSER().'">'.$commentaire->getIDUSER().'</a></div><div class="descP" style="margin-left: 20px;color: black">'.$commentaire->getTEXTE(). ' </div><div style="color: #adadad;">' .$commentaire->getDATECOMMENTAIRE().' </div></div>   </li>';
                    }
                }
            }
            else
            {
                echo '<div> Pas encore de commentaires. </div>';
            } ?>
        </div>
    </div>
</div>

