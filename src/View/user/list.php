<?php
use App\Model\DataObject\User;
use App\Lib\ConnexionUtilisateur;

/** @var User[] $users
 * @var string $privilegeUser
 */
?>
<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <h1>Liste des Utilisateurs :</h1> <?php
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
                echo '  <div class="ligneExt"><form class="ligneAlign" method="post" action="frontController.php?controller=user&action=readAll">
                <input type="search" class="opt" name="title" id="title" placeholder="Rechercher un Utilisateur">
                <button type="submit" class="opt"><img alt="recherche" src="../assets/img/icon-chercher.svg"></button>
                <a href="frontController.php?controller=user&action=readAll" id="refresh"><img alt="actualiser la liste" src="../assets/img/icon-refresh.svg"></a>
            </form><h3>Rôle</h3></div>';
                if($privilegeUser=='administrateur'){
                    echo '<div class="ligneAlign">
                            <a class="optButton" href="frontController.php?controller=groupe&action=readAll">Voir Groupes</a>
                            <a class="optButton" href="frontController.php?controller=groupe&action=create">Créer Groupe</a>
                          </div>';

                }
            } ?>

        <ul>
            <?php

            if(ConnexionUtilisateur::estConnecte())
            {

                if (isset($users) && empty($users))
                {
                    echo "<div class='descG'></div><div class='ligneCent'><h3>Aucun résultat a été trouvé pour " . htmlspecialchars($_POST['title']) . " .</h3></div>";
                }
                else if (ConnexionUtilisateur::estAdministrateur() || ((isset($_POST['title']) && !empty($_POST['title']))))
                {
                    foreach ($users as $user)
                    {
                        echo '<div class="ligneExt"><li class="ligneExt"><a href=frontController.php?controller=user&action=read&id=' . rawurlencode($user->getId()) . '>' . ucfirst(htmlspecialchars($user->getId())) . '</a> <span>' . ucfirst(htmlspecialchars($user->getPrenom())) . ' ' . ucfirst(htmlspecialchars($user->getNom())) . '</span></span></li><h2>' . ucfirst(htmlspecialchars($user->getRole())) . '</h2></div>';

                    }
                }
            }
            else
            {
                echo "<div class='descG'></div><div class='ligneCent'><h3> Vous devez être connecté pour visualiser les contributeurs.</h3></div>";
            }
            ?>
        </ul>
    </div>
</div>