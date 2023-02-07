
<div class="block">
    <div class="text-box">
        <form method="post" id="formConnect" action="frontController.php?controller=user&action=connected">
            <div class="ligneExt"> <h1>Connexion :</h1> <div>Vous n'avez pas de compte ? <h3><a href="frontController.php?controller=user&action=inscription">Inscrivez vous</a></h3></div></div>
            <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>

            <div class="descG"></div>

            <h3><label for="id">Identifiant :</label></h3>
            <input type="text" id="id" name="id" placeholder="Identifiant" size="50"  required>
            <?php if(isset($msgErreurId)) {echo '<div style="color:#ffffff;">' .$msgErreurId.'</div>';}?>
            <div class="descP"></div>


            <h3><label for="mdp">Mot de passe :</label></h3>
            <input type="password" id="mdp" name="mdp" placeholder="********" size="50" required>
            <?php if(isset($msgErreurMdp)) {echo '<div style="color:#ffffff;">' .$msgErreurMdp.'</div>';}?>
            <div></div>
            <a style="color: #aca9ff" href="frontController.php?controller=user&action=mdpOublie"> Mot de passe oublié ? </a>
            <div class="descG"></div>


            <div class="ligneCent"> <input class="optQuestion" type="submit" value="Se connecter"/></div>
        </form>

    </div>
</div>

