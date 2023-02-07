<?php
use App\Model\DataObject\Groupe;
use App\Lib\ConnexionUtilisateur;

/** @var Groupe[] $groupes
 * @var string $privilegeUser

 */
?>
<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <h1>Liste des groupes :</h1> <?php
            if(ConnexionUtilisateur::estConnecte()){
                $idUser = ConnexionUtilisateur::getLoginUtilisateurConnecte();
                echo "<div class='responsive'>Vous êtes connecté en tant que :<h3>".ucfirst($privilegeUser)."</h3></div>";
            }
            else{
                echo "<h3 class='responsive'>Vous n'êtes pas connecté</h3>";
            }?></div>
        <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>

        <?php
        if(ConnexionUtilisateur::estConnecte())
        {
            echo '  <div class="ligneExt"><form class="ligneAlign" method="post" action="frontController.php?controller=groupe&action=readAll">
                <input type="search" class="opt" name="title" id="title" placeholder="Rechercher un Groupe">
                <button type="submit" class="opt"><img alt="recherche" src="../assets/img/icon-chercher.svg"></button>
                <a href="frontController.php?controller=groupe&action=readAll" id="refresh"><img alt="actualiser la liste" src="../assets/img/icon-refresh.svg"></a>
            </form><h3>Responsable</h3></div>';
            if($privilegeUser=='administrateur'){
                echo '<div class="ligneAlign">
                            <a class="optButton" href="frontController.php?controller=groupe&action=create">Créer Groupe</a>
                          </div>';

            }
        } ?>

        <ul>
            <?php
            if(ConnexionUtilisateur::estAdministrateur())
            {
                if (empty($groupes))
                {
                    echo "<div class='descG'></div><div class='ligneCent'><h3>Aucun résultat a été trouvé pour " . htmlspecialchars($_POST['title']) . " .</h3></div>";
                }
                else
                {
                    foreach ($groupes as $groupe)
                    {
                        echo '<div class="ligneExt"><li class="ligneExt"><a class="atxt" href=frontController.php?controller=groupe&action=read&nomGroupe=' . rawurlencode($groupe->getId()) . '>' . ucfirst(htmlspecialchars($groupe->getId())) . '</a></li><a class="liste" id="resp" href=frontController.php?controller=user&action=read&id=' . ConnexionUtilisateur::getLoginUtilisateurConnecte() . '><h2>' . ucfirst(htmlspecialchars($groupe->getIdResponsable())) . '</h2></a></div>';

                    }
                }
            }
            else
            {
                echo "<div class='descG'></div><div class='ligneCent'><h3> Vous devez être administrateur pour visualiser les groupes.</h3></div>";
            }
            ?>
        </ul>
    </div>
</div>