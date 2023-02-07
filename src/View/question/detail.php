<?php
use App\Model\DataObject\Question;
use App\Model\DataObject\Demande;
use App\Model\DataObject\Phase;
use App\Lib\ConnexionUtilisateur;


/** @var Question $question
 * @var Demande[] $demandes
 * @var Phase[] $phases
 * @var string $roleQuestion
 * @var bool $peutVoter
 * @var bool $peutPasser
 * @var bool $estFini
 * @var bool $dejaResponsable
 * @var int $propositionDejaExistante
 * @var bool $dejaDemande
 */

if(!isset($demandes)) {
    $demandes = [];
}

$nbDemandes=sizeof($demandes);

$typePrecisPhase= $question->getCurrentPhase()->getType();
$typePhase = 'placeHolder';
switch ($typePrecisPhase) {
    case 'consultation':
        $typePhase= 'En cours de consultation';
        break;
    case 'redaction':
        $typePhase= 'En cours de rédaction';
        break;
    case 'scrutinMajoritaire' || 'scrutinMajoritairePlurinominal' || 'jugementMajoritaire' :
        $typePhase= 'Voter juste Ici';
        break;
    case 'termine':
        echo "Vote(s) terminé(s)";
        break;
}
?>

<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <div><a class="optQuestion" id="fleche" href=frontController.php?controller=question&action=readAll>↩</a>
                <h1><?=htmlspecialchars($question->getIntitule())?></h1></div>

                    <?php
                    if(ConnexionUtilisateur::estConnecte()){
                        $idQuestion = $question->getId();
                        echo "<div id='col'>";
                        if($roleQuestion!=null){
                            echo "<h3> Vous êtes ".$roleQuestion." sur cette question.</h3>";
                        }
                        else{
                            echo "<h3>Vous n'avez pas de rôle sur cette question.</h3>";
                        }

                        if($peutVoter && $typePrecisPhase!="termine" && $typePrecisPhase!="consultation"&& $typePrecisPhase!="redaction") {
                            $typePrecisPhase = ucfirst($typePrecisPhase);
                            echo "<a href=frontController.php?controller=vote&action=voter$typePrecisPhase&idQuestion=$idQuestion id='boutonVote'><h2>Vote en cliquant ici</h2></a>";
                        }
                        else if($roleQuestion==null && ($typePrecisPhase!="termine")){
                            echo "<a href=frontController.php?controller=question&action=demandeRoleQuestion&role=votant&id=". rawurlencode($idQuestion) .">
                                <h2>Vous souhaitez voter?</h2></a>";
                        }
                        else if($question->getCurrentPhase()->getType()=="termine" || $question->getCurrentPhase()->getType()=="consultation"|| $question->getCurrentPhase()->getType()=="redaction"){
                            echo '<h2>' . $typePhase . '</h2>';
                        }
                        else{
                            echo '<h2>Vote indisponible</h2>';
                        }

                        echo '</div>';
                    }
                    else{
                      echo '<div><h2 id="desc">Aucunes informations disponibles.</h2></div>';
                    }?>

                </div>
        <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>


            <?php
            if(ConnexionUtilisateur::estConnecte()) {
                if ($roleQuestion == "organisateur") {
                    if($peutPasser) {
                        $btnPhase = '<a class="optQuestion" href="frontController.php?controller=question&action=changePhase&id=' . $question->getId() . '">Passer à la prochaine phase</a>';
                    }else{
                        $btnPhase = '';
                    }

                    echo '<h2>Interface Organisateur</h2><br><div class="ligneExt"><a class="optQuestion" href=frontController.php?controller=proposition&action=readAll&id=' . rawurlencode($question->getId()) . '>Voir les propositions</a>';
                    if($estFini)
                    {
                        echo '<a class="optQuestion" href=frontController.php?controller=question&action=readResult&id=' . rawurlencode($question->getId()) . '>Résultats</a>';
                    }

                        $btnModifier = '<a href=frontController.php?controller=question&action=update&id=' . rawurlencode($question->getId()) . '><img class="icons" title="Modifier Question" alt="Modifier" src="../assets/img/icons8-crayon-48.png"></a>';
                        echo '</div><div class="ligneExt"><div class="ligneExt">' .$btnPhase .
                        '</div><div class="ligneAlign">
                            ' . $btnModifier .'
                            <a href=frontController.php?controller=question&action=delete&id=' . rawurlencode($question->getId()) . '><img class="icons" id="poubelle" title="Supprimer Question" alt="Supprimer Question" src="../assets/img/icons8-poubelleBlanc.svg"></a>
                            <a href="frontController.php?controller=question&action=addUsersToQuestion&id=' . $question->getId() .'"><img class="icons" title="Ajouter Utilisateurs" alt="Ajouter Utilisateurs" src="../assets/img/icons8-ajtUserBlanc-48.png"></a>
                        </div>
                        </div>
                        <div class="ligneExt">
                        <div>
                        
                    
                        </div>
                            <div id="col">';

                        if($nbDemandes>0)
                        {
                            echo  '<div class="iconsNotifs" id="iconsNotifiaction">'.$nbDemandes.'</div>
                            <a class="optQuestion" id="askVotants" href=frontController.php?controller=question&action=readDemandeVote&id='.$question->getId().'> Demandes</a>';

                        }


                    echo '
                        
                    
                         </div>
                        </div>
                       ';
                } else if($roleQuestion=="responsable"){
                    echo '<div class="ligneExt">
                            <a class="optQuestion" href=frontController.php?controller=proposition&action=readAll&id=' . rawurlencode($question->getId()) . '>Voir les propositions</a>';
                    if($estFini) {
                        echo '<a class="optQuestion" href=frontController.php?controller=question&action=readResult&id=' . rawurlencode($question->getId()) . '>Résultats</a>';
                    }


                            if(!$dejaResponsable)
                            {

                                echo '</div><div class="ligneExt"><a class="optQuestion" href=frontController.php?controller=proposition&action=create&id=' . rawurlencode($question->getId()) . '>
                        Créer proposition</a></div>';
                            }
                            else //a deja une prop de crée pour cette q
                            {
                                echo '</div><div class="ligneExt"><a class="optQuestion" href=frontController.php?controller=proposition&action=read&id=' .$propositionDejaExistante . '>
                        Gérer votre proposition</a></div>';
                            }

                }
                else{
                    echo '<div class="ligneExt">
                            <a class="optQuestion" href=frontController.php?controller=proposition&action=readAll&id=' . rawurlencode($question->getId()) . '>Voir les propositions</a>';
                    if($estFini) {
                        echo '<a class="optQuestion" href=frontController.php?controller=question&action=readResult&id=' . rawurlencode($question->getId()) . '>Résultats</a>';
                    }
                    echo '</div>';

                    if(!$dejaDemande)
                    {
                       echo "<a class='optQuestion' href=frontController.php?controller=question&action=demandeRoleQuestion&role=responsable&id=". rawurlencode($idQuestion) .">
                                Demander à écrire une proposition</a>";
                    }
                    else
                    {
                        echo "<div class='optQuestionEnAttente'>Demande en attente de confirmation</div>";
                    }

                }

                //TIMELINE
                if(!(empty($phases))){
                    echo '<h3 id="prog">Progression :</h3><div class="ligneP"></div>';
                    echo '<div class="timeline">';

                    foreach ($phases as $phase){
                        $type='';
                        $typeP=$phase->getType();
                        switch ($typeP) {
                            case 'consultation':
                                $type= 'Consultation';
                                break;
                            case 'scrutinMajoritaire':
                                $type= 'Scrutin Majoritaire';
                                break;
                            case 'scrutinMajoritairePlurinominal':
                                $type= "Scrutin Plurinominal";
                                break;
                            case 'jugementMajoritaire' :
                                $type='Jugement Majoritaire';
                                break;
                            case 'redaction':
                                $type='Rédaction';
                                break;
                        }

                        echo '
                        <p id="pet">'.$type.'<br>';
                        if($phase->getNbDePlaces()>1){
                            echo ' <span id="prop">(' . $phase->getNbDePlaces() . ' propositions selectionnées)</span> ';
                        }
                        else if($phase->getNbDePlaces()==1){
                            echo ' <span id="prop">(' . $phase->getNbDePlaces() . ' proposition selectionnée)</span> ';
                        }
                            echo "</p><div class='ligneT' style='background: transparent'></div>";


                    }
                    echo '<p id="pet">Résultats publics</p></div>';

                    //DEBUT
                    echo '<div class="timeline">';

                    $widthLigne= 90/(sizeof($phases));

                    foreach ($phases as $phase){

                        echo '<style>.ligneT{width: '.$widthLigne.'%;}</style>';
                        echo '<a href=frontController.php?controller=question&action=readResultPhase&id=' . $question->getId() . '&idPhase='. $phase->getId() .' id="circle"></a><div class="ligneT"></div>';
                    }
                    //FIN
                    echo '<a href=frontController.php?controller=question&action=readResult&id=' . $question->getId() . ' id="circle"></a></div>';


                    echo '<div class="timeline" id="margintimeline">';
                    $widthLigne=(85/(sizeof($phases)));
                    foreach ($phases as $phase){
                        echo '<style>.ligneTbis{width: '.$widthLigne.'%;}</style>';
                        echo '<p id="pet">Du '.htmlspecialchars($question->dateToString($phase->getDateDebut())).'<br> au
                        '.htmlspecialchars($question->dateToString($phase->getDateFin())).'</p><div class="ligneTbis" style="background: transparent"></div>';
                    }
                    echo '<p id="pet">'.htmlspecialchars($question->dateToString($question->getDateFermeture())).'</p></div>';
                }

                echo '<br><div class="description">';
                echo '<div class="ligneAlign"><h1>Description :</h1></div><div class="ligne"></div><br><p id="sections">
                        ' .$question->getDescription() . '</p><div class="descG"></div>';
                echo '<div class="ligneCent"><div class="ligne"></div></div><div class="ligneCent"><h1>Sommaire</h1></div>';
                $y=0;
                foreach ($question->getSections() as $section) {
                    $y++;
                    echo '<div class="ligneCent"><a href="#section'. $y .'"><h3 id="sections">' . $y . '. ' . ucfirst(htmlspecialchars($section->getIntitule())) . "</h3></a></div>";
                }
                echo '<br><div class="ligneCent"><div class="ligne"></div></div><br><br>';

                $i=0;
                foreach ($question->getSections() as $section) {
                    $i++;
                    echo '<div class="ligneExt" id="section'. $i .'"><h3 id="sections">'. $i .'. ' . ucfirst(htmlspecialchars($section->getIntitule())) . "</h3></div>";
                    echo "<div class='ligne'></div><p>" . $section->getDescription();
                    echo '<br>';
                    //echo '<div><a href="frontController.php?controller=question&action=likeSection&id='.$section->getId().'&idQuestion='.$_GET['id'].'"><img src="../assets/img/icons8-jaimeBlanc.png"></a>     '.$section->getNbLikes().'</div></li>';
                    echo '<br>';
                    echo "</p><br>";

                }
                echo '</div>';
        }
            else{
                echo "<div class='LigneCent'><h3>Vous n'êtes pas connecté. Connectez-vous pour plus d'informations !</h3></div>";
            }
            ?>

    </div>
</div>
