<?php
use App\Model\DataObject\Question;

/** @var Question $question */
?>
<div class="block">
    <div class="text-box">
    <form method="post" action="frontController.php?controller=question&action=updated&id=<?=$question->getId()?>">
        <fieldset>
            <div class="descP"></div>
            <h1>Votre Question</h1>
            <div class="ligneCent"><div class="ligne"></div></div>
            <div class="descG"></div>
            <?php
            $phases = $question->getPhases();
            $phase = $phases[0];
            if(!($phase->estCommence() ||$phase->estFinie())) {
                require __DIR__ . '/../phase/updateRedac.php';
            }
            else{
                echo 'Si une phase n\'apparait pas, c\'est qu\'elle n\'est plus modifiable';
            }
            for($numeroPhase=1; $numeroPhase<count($phases)-1; $numeroPhase++)
            {

                $phase = $phases[$numeroPhase];
                if(!($phase->estCommence() ||$phase->estFinie()))
                {
                    require __DIR__ .'/../phase/updateVote.php';
                }
            }

            $numeroPhase = count($phases)-1;
            $phase = $phases[$numeroPhase];

            if(!($phase->estCommence() ||$phase->estFinie())) {
                require __DIR__ . '/../phase/updateFinalVote.php';
            }
            ?>


            <h3><label for="tq">Question :</label></h3>
            <input type="text" id="tq" name="titreQuestion" size="50" value="<?=ucfirst($question->getIntitule())?>">
            <div class="descP"></div>

            <h3><label for="mytextarea">Description :</label></h3>
            <textarea id="mytextarea" name="descriptionQuestion" rows="4" maxlength="1000" cols="100"><?=ucfirst($question->getDescription())?></textarea>

            <div class="descG"></div>
            <?php

            $sections = $question->getSections();
            for($i=0; $i<count($sections); $i++){
                $section = $sections[$i];
                echo '<h3> Section '. ($i + 1) . ' : </h3>';
                require __DIR__ .'/../section/update.php';
                echo '<div class="descP"></div>';
            }
            ?>

            <div class="ligneCent"> <input class="optQuestion" type="submit" value="sauvegarder"/></div>
        </fieldset>
    </form>
    </div>
</div>