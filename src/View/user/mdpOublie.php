<div class="block">
    <div class="text-box">
        <form method="post" id='formConnect' action="frontController.php?controller=user&action=emailRecup">
            <div class="ligneExt"> <h1>Récupération du mot de passe :</h1></div>
            <div class="ligneExt"><div class="ligne"></div></div>

            <div class="descG"></div>
            <h3><label for="emailRecup">Identifiant :</label></h3>
            <input type="email" id="emailRecup" name="emailRecup" placeholder="Email de récupération" size="50"  required>
            <div class="descG"></div>

            <div class="ligneCent"> <input class="optQuestion" type="submit" value="Envoyer le mail de récupération"/></div>
        </form>

    </div>
</div>
