<?php

use \App\Lib\ConnexionUtilisateur;
use App\Model\DataObject\Question;

/** @var Question[] $questions
 * @var string $privilegeUser
 */
?>
<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <h1>Questions archivées :</h1>

            <?php
            if(ConnexionUtilisateur::estConnecte()){
                echo '<div class="responsive">Vous êtes connecté en tant que :<h3>'.ucfirst($privilegeUser).'</h3></div></div>
        <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>
        
        <div class="ligneExt">
        <form class="ligneAlign" method="post" action="frontController.php?controller=question&action=readAllArchives">
                <input type="search" class="opt" name="title" id="title" placeholder="Rechercher une Question archivée">
                <button type="submit" class="opt"><img alt="recherche" src="../assets/img/icon-chercher.svg"></button>
                <a href="frontController.php?controller=question&action=readAllArchives" id="refresh"><img alt="actualiser la liste" src="../assets/img/icon-refresh.svg"></a>
            </form>
            <h3>Résultats</h3></div>
 <ul>';

            if(empty($questions)){
                echo "<div class='descG'></div><div class='ligneCent'><h3>Aucun résultat n'a été trouvé</h3></div>
                    <div class='descP'></div><div class='ligneCent'>
                    <a href=frontController.php?controller=question&action=readAllArchives>Clique <strong>ici</strong> pour afficher <strong>toute</strong> la liste !</a></div>";
            }
            else{
                foreach ($questions as $question){
                    echo '
                <div class="ligneExt">
        <li class="ligneExt">
                            <div>
                                <a href=frontController.php?controller=question&action=read&id=' . rawurlencode($question->getId()).'>
                                    <div class="atxt">' .ucfirst(htmlspecialchars($question->getIntitule())).'</div>
                                    <div class="descP"></div>
                                    <p>'. htmlspecialchars($question->getApercuDescription()).'</p>
                                    <p id="date">Du '. htmlspecialchars($question->dateToString($question->getDateCreation())) .' au ' .
                        htmlspecialchars($question->dateToString($question->getDateFermeture())) .'</p>
                                </a>
                            </div>
                            <div>
                                <a class="abis" href=frontController.php?controller=user&action=read&id=' .
                        $question->getIdOrganisateur() . '>Organisateur<strong>' . $question->getIdOrganisateur() . '</strong>
                                </a>
                            </div>
                        </li>
           
            <a class="liste" href=frontController.php?controller=question&action=readResult&id=' . rawurlencode($question->getId()) . '><h2><img alt="résultats" src="../assets/img/icon-vote.png"></h2></a>
        
            </div>';
                }
                echo '</ul>';
            }
            }
            else{
                echo "<h3 class='responsive'>Vous n'êtes pas connecté</h3></div><div class='ligneExt'><div class='ligne'></div><div class='ligne'></div></div>
<div class='descG'></div> <div class='ligneCent'><p>Aucun résultat n'est disponible.</p></div><div class='ligneCent'><p>Connectez-vous en cliquant juste<a href='frontController.php?controller=user&action=connexion'>ici</a>.</p></div>";
            }?>



    </div>
</div>