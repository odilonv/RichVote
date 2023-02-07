<?php
/**
 * @var Question $question
 * @var array $propositionsWithScore
 */

use App\Model\DataObject\Proposition;
use App\Model\DataObject\Question;

?>
<div class="block">
    <div class="text-box">
        <form method="post" id="formVote" action="frontController.php?controller=vote&action=jugementMajoritaireVoted">
            <fieldset>
                <h1>Vous pouvez voter.</h1>

                <div class="ligneCent"><div class="ligne"></div></div><br>
                <h3>Votre choix restera confidentiel.</h3>
                <div class="descG"></div>
                <?php
            foreach ($propositionsWithScore as $propositionWithScore){
                $proposition = $propositionWithScore[0];
                $score = $propositionWithScore[1];
                $intituleProposition = $proposition->getIntitule();
                $idProposition = $proposition->getId();
                $nameScore = "score[$idProposition]";
                echo "
                  <div class='ligneCent'><label for='$nameScore'><h1>" .ucfirst($intituleProposition). " : </h1> </label>
                
                <select name='$nameScore'>
                <option value='0' ". selected($score, '0') .">Non intéressé</option>
                <option value='1' ". selected($score,'1') .">Insuffisant</option>
                <option value='2' ". selected($score,'2') .">Passable</option>
                <option value='3' ". selected($score,'3') .">Assez bien</option>
                <option value='4' ". selected($score,'4') .">Bien</option>
                <option value='5' ". selected($score,'5') .">Très bien</option>
                </select></div>";
            }
            function selected(string $score, string $scoreTarget):string{
                return $score==$scoreTarget?'selected':'';
            }
            ?>
                <br>
                <br>
                <br>
            <div class="ligneCent"><button type="submit" class="opt"><img alt="voter" src="../assets/img/icon-vote.png"></button></div>

            </fieldset>
        </form>
    </div>
</div>