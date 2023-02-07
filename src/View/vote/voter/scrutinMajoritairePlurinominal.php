<?php
/*
 * faire la vue en deux  parties:
 * - une qui affiche les propositions votés pour
 * - l'autre qui affiche les votes contre
 * - une dernière qui affiche les neutres?
 */
use App\Model\DataObject\Proposition;
use App\Model\DataObject\Question;

/**
 * @var Proposition[] $propositionsPour
 * @var Proposition[] $propositionsContre
 * @var Question $question
 */?>

<div class="block">
    <div class="text-box">
        <form method="post" id="formVote" action="frontController.php?controller=vote&action=scrutinMajoritairePlurinominalVoted&idQuestion=<?=$question->getId()?>">
            <fieldset>
             <h1>Vous pouvez voter.</h1>

            <div class="ligneCent"><div class="ligne"></div></div><br>
            <h3>Votre choix restera confidentiel.</h3>
                <h2>CLiquez sur la proposition si vous souhaitez la déplacer de case.</h2>
            <div class="descG"></div>

            <h1> Vous avez voté pour :</h1>
                <fieldset id="caseVote">
            <?php
            foreach ($propositionsPour as $propalPour){
                $idProposition = $propalPour->getId();
                $intituleProposition = htmlspecialchars($propalPour->getIntitule());
                echo "<button class='optButton' name='idPropositionContre' value='$idProposition'>$intituleProposition</button>";
            }
            ?></fieldset>
                <div class="descG"></div>
            <h1> Vous avez voté contre :</h1>
                <fieldset id="caseVote">
            <?php
            foreach ($propositionsContre as $propalContre){
                $idProposition = $propalContre->getId();
                $intituleProposition = htmlspecialchars($propalContre->getIntitule());
                echo "<button class='optButton' id='' name='idPropositionPour' value='$idProposition'>$intituleProposition</button>";
            }
            ?></fieldset>
                <div class="descG"></div>
                <a href=frontController.php?controller=question&action=read&id=<?=$question->getId()?> class="opt"><img alt="voter" src="../assets/img/icon-vote.png"></a>
            </fieldset>
        </form>
    </div>
</div>