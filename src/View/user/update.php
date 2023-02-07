
<?php
use App\Model\DataObject\User;

/** @var User $user
 * @var bool $mdpOublie

 */



?>

<div class="block">
    <div class="text-box">
        <?php
        if (!$mdpOublie)
        {
            echo    '<form method="post" id="formConnect" action="frontController.php?controller=user&action=updated&id='.$user->getId().'">';
        }
        else
        {
            echo    '<form method="post" id="formConnect" action="frontController.php?controller=user&action=updatedMdpOublie&id='.$_GET['id'].'">';
        }
        ?>

            <div class="ligneExt"> <h1>Modifier le mot de passe :</h1></div>
                <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>
            <div class="descG"></div>
        <?php
        if (!$mdpOublie)
        {
            echo    '<h3><label for="aMdp">Ancien mot de passe :</label></h3>
            <input type="password" id="aMdp" name="aMdp" placeholder="********" size="50"  required>
            <div class="descG"></div>';
        } ?>
        <h3><label for="nMdp">Nouveau mot de passe :</label></h3>
            <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="nMdp" name="nMdp" placeholder="********" size="50" required>
            <div class="descP"></div>

            <h3><label for="cNMdp">Confirmer le nouveau mot de passe :</label></h3>
            <input type="password" id="cNMdp" name="cNMdp" placeholder="********" size="50" required>
            <div class="descG"></div>


            <div class="ligneCent"> <input class="optQuestion" type="submit" value="Valider"/></div>
            <?php if(isset($msgErreur))
            {
                echo '<p>'.$msgErreur.'</p>';
            }
            ?>
        </form>

    </div>
</div>
