<?php
use App\Model\DataObject\Phase;
use App\Model\DataObject\Question;
/** @var Question $question
 * @var array $propositionsScore
 *  une array [Proposition, int score]
 * @var Phase $phase
 */
?>





<div class="block">
    <div class="text-box">
        <a class="optQuestion" id="fleche" href=frontController.php?controller=question&action=read&id=<?=$question->getId()?>>↩</a>
        <div class="column">
            <div class="results">
                <h2>Résultats Finaux</h2><h3 id="quest"><?=htmlspecialchars($question->getIntitule())?></h3>
                <div class="ligne"></div>
                <br>
                <?php
                if ($phase->getType() == "consultation") {
                    echo "<p>Il n'y a pas eu de vote sur cette question.</p>";
                } else {
                    if($phase->getType()=='scrutinMajoritaire' || $phase=='scrutinMajoritairePlurinominal') {
                        require_once __DIR__ . '/result/resultScrutinMajoritaire.php';
                    }
                    else{
                        require_once __DIR__.'/result/resultJugementMajoritaire.php';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>