<div class="block">
    <div class="text-box">
        <form id="formConnect" action="frontController.php?controller=user&action=inscrit" method="post">
            <div class="ligneExt"> <h1>Inscription :</h1> <div>Vous êtes déjà inscrit ?<h3><a href="frontController.php?controller=user&action=connexion">Connectez-vous</a></h3></div></div>
            <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>
            <div class="descG"></div>
            <h3><label for="id">Identifiant :</label></h3>
            <input type="text" id="id" name="identifiant" placeholder="Identifiant" size="50"  required
                <?php if(isset($persistanceValeurs["idUser"]))
                {
                    echo 'value="'.$persistanceValeurs["idUser"].'"';
                }
                ?>>
            <div class="descP"></div>

            <h3><label for="email">Email :</label></h3>
            <input type="email" pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" id="email" name="email" placeholder="email" size="50"  required
                <?php if(isset($persistanceValeurs["email"]))
                {
                    echo 'value="'.$persistanceValeurs["email"].'"';
                }
                ?>>
            <div class="descP"></div>

            <h3><label for="mdp" title="Votre mot de passe doit contenir au moins :
                    Une majuscule
                    Une minuscule
                    Un chiffre
                    Un caractère spécial">Mot de passe * : </label></h3>
            <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="mdp" name="motDePasse" placeholder="********" size="50" required>
            <?php if(isset($msgErreurMdp))
            {
                echo '<div style="color:#5d58ff;">' .$msgErreurMdp.'</div>';
            }
            ?>

            <div class="descP"></div>

            <h3><label for="confirmationMdp">Confirmer le mot de passe :</label></h3>
            <input type="password" id="confirmationMdp" name="confirmerMotDePasse" placeholder="********" size="50" required>
            <div class="descP"></div>


            <h3><label for="prenom">Prénom :</label></h3>
            <input type="text" id="prenom" name="prenom" size="50" required
                <?php if(isset($persistanceValeurs["prenom"]))
                {
                    echo 'value="'.$persistanceValeurs["prenom"].'"';
                }
                ?>>
            <div class="descP"></div>
            <h3><label for="nom">Nom :</label></h3>
            <input type="text" id="nom" name="nom" size="50" required
                <?php if(isset($persistanceValeurs["nom"]))
                {
                    echo 'value="'.$persistanceValeurs["nom"].'"';
                }
                ?>>
            <div class="descG"></div>


            <div class="ligneCent"> <input class="optQuestion" type="submit" name="submit" value="S'inscrire"/></div>
            <?php if(isset($msgErreur))
            {
                echo '<p>'.$msgErreur.'</p>';
            }
            ?>
        </form>
    </div>
</div>
