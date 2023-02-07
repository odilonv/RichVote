<div class="block">
    <div class="text-box">
    <form method="post" id="formVote" action="frontController.php?controller=vote&action=scrutinMajoritaireVoted">
        <fieldset>
        <h1>Vous pouvez voter.</h1>

        <div class="ligneCent"><div class="ligne"></div></div><br>
        <h3>Votre choix restera confidentiel.</h3>
        <div class="descG"></div>


        <?php
        /** @var Proposition[] $propositions */

        use App\Model\DataObject\Proposition;

        foreach ($propositions as $proposition){
            $idProposition = $proposition->getId();
            $intituleProposition = $proposition->getIntitule();
            echo '<div class="ligneCent">
                    <h3>'.ucfirst($intituleProposition).' → </h3>
                    <label for="checkbox"  class="checkbox"><input type="radio" id='.$idProposition .' name=idProposition value='.$idProposition .'></label>
                    </div><div class="descP"></div>';
        }
        ?>
        <div class="descG"></div>
        <button type="submit" class="opt"><img alt="voter" src="../assets/img/icon-vote.png"></button>

            </fieldset>



    </form>
    </div>
</div>