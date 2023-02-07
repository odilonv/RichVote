<div class="block">
    <div class="text-box">
    <form method="post" action="frontController.php?controller=question&action=created">
        <fieldset>
            <div class="descP"></div>
            <h1>Créer une nouvelle question</h1>
            <div class="ligneCent"><div class="ligne"></div></div>
            <div class="descG"></div>

                <h3><label for="tq">Question :</label></h3>
                <input type="text" id="tq" maxlength="100" name="titreQuestion" placeholder="Titre de la question" size="50" required>
                <div class="descP"></div>

                <h3><label for="dF">Date de cloture de la question :</label></h3>
                <input type="date" id="dF" name="dateFermeture" required>

                <div class="descP"></div>
                <h3><label for="ns"> Nombre de sections :</label></h3>
                <input type="number" min="1" max="10" id="ns" name="nbSections" value="1" placeholder="1">

                <div class="descP"></div>
                <h3><label for="np"> Nombre de phases de votes :</label></h3>
                <input type="number" min="1" max="10" id="np" name="nbPhases" value="1" placeholder="1">
                <div class="descG"></div>

                <div class="ligneCent"> <input class="optQuestion" type="submit" value="sauvegarder"/></div>
            <div class="descP"></div>
        </fieldset>
    </form>
    </div>
</div>
