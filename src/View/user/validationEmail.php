<?php
/** @var string $idUser
*/
if(!isset($idUser))
{
    $idUser = $_GET['idUser'];
}
?>
<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <h1>Validation de l'email :</h1></div>
        <form method="post" id="formConnect" action="frontController.php?controller=user&action=userValide&idUser=<?=$idUser?>">
            <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>
            <div class="descG"></div>
            <h3><label for="nonce">Code de verification :</label></h3>
            <div><input type="text" id="nonce" name="nonce" placeholder=" _ _ _ _ _ _" size="6"  required></div>
            <div class="descP"></div>
            <h3><a style="color:#c6a1f3" href="frontController.php?controller=user&action=renvoyerCode&idUser=<?=$idUser?>">Renvoyer un code</a></h3>
            <div class="descG"></div>
            <div class="ligneCent"> <input class="optQuestion" type="submit" value="Valider"/></div>
        </form>


    </div>
</div>