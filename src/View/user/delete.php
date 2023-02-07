
<?php
use App\Model\DataObject\User;
use App\Lib\ConnexionUtilisateur;

/** @var User $user */
?>

<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <h1>Suppresion du compte</h1></div>
        <?php
        if((new ConnexionUtilisateur())->estAdministrateur())
        {
            echo '<form method="post" id="formConnect" action="frontController.php?controller=user&action=deleted&id='.$user->getId().'">
           <div class="ligneExt"><div class="ligne"></div></div>
            <div class="ligneCent"> <input class="optQuestion" type="submit" value="Supprimer"/></div>
        </form>';
        }
        else
        {
            echo '<form method="post" id="formConnect" action="frontController.php?controller=user&action=deleted&id='.$user->getId().'">
           <div class="ligneExt"><div class="ligne"></div></div>
            <div class="descG"></div>
            <p>
            <h3>Mot de passe du compte :</h3>
            <input type="password" id="mdp" name="mdp" placeholder="********" size="50"  required>
            <div class="descP"></div>

            <h3>Entrer à nouveau le mot de passe du compte :</h3>
            <input type="password" id="cMdp" name="cMdp" placeholder="********" size="50" required>
            <div class="descG"></div>
            </p>
            <div class="ligneCent"> <input class="optQuestion" type="submit" value="Supprimer"/></div>
        </form>';
        }
        ?>


    </div>
</div>

