<?php
use App\Model\DataObject\User;
use \App\Lib\ConnexionUtilisateur;
use App\Model\Repository\UserRepository;
/** @var User[] $users,
 */
?>
<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <h1>Ajouter des Organisateurs :</h1> <?php
            if(ConnexionUtilisateur::estConnecte()){
                $idUser = htmlspecialchars(ConnexionUtilisateur::getLoginUtilisateurConnecte());
                echo "<div class='responsive'>Vous êtes connecté en tant que :<h3>".ucfirst((new UserRepository())->getPrivilege($idUser))."</h3></div>";
            }
            else{
                echo "<h3 class='responsive'>Vous n'êtes pas connecté</h3>";
            }?></div>
        <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>
        <div class="ligneExt"><form class="ligneAlign" method="post" action='frontController.php?controller=question&action=addOrganisateurs'>
                <label for="title"></label>
                <input type="search" class="opt" name="title" id="title" placeholder="Rechercher un Utilisateur">
                <button type="submit" class="opt"><img alt="recherche" src="../assets/img/icon-chercher.svg"></button>
                <a href='frontController.php?controller=question&action=addOrganisateurs' id="refresh"><img alt="rafraichir la page" src="../assets/img/icon-refresh.svg"></a>
            </form><h3>Ajouter</h3></div>
        <ul>
            <?php
            if (empty($users)){
                echo "<div class='descG'></div><div class='ligneCent'>
                    <h3>Aucun résultat a été trouvé pour " . htmlspecialchars($_POST['title']) . " .</h3></div>
                    <div class='descP'></div><div class='ligneCent'>
                    <a href='frontController.php?controller=question&action=addOrganisateurs'>Clique <strong>ici</strong> pour afficher <strong>toute</strong> la liste !</a></div>";
            }
            else {
                echo "<form method='post' action='frontController.php?controller=question&action=OrganisateursAdded'>";
                foreach ($users as $user) {
                    $idUser = rawurlencode($user->getId());
                    $htmlId = ucfirst(htmlspecialchars($idUser));
                    $prenom = ucfirst(htmlspecialchars($user->getPrenom()));
                    $nom = ucfirst(htmlspecialchars($user->getNom()));

                    $role = (new UserRepository())->getPrivilege($idUser);
                    if($role=="invité"){
                        echo "<div class='ligneExt'>

                            <li class='ligneExt'> <a href='frontController.php?controller=user&action=read&id=$idUser'> $htmlId</a> <span> $prenom $nom </span></span></li>
                            <h2>" . ucfirst($role) . " </h2>
                            <label for='checkbox' class='checkbox'> 
                                <input type='checkbox' id='cb[$htmlId]' name='user[$htmlId]' value='$htmlId'>
                            </label>
                          </div>";
                    }


                }
                echo '<div class="descG"></div> <div class="ligneCent"> <input type="submit" value="Ajouter les utilisateurs selectionnés" class="optQuestion"></div></form>';
            }
            ?>

        </ul>
    </div>
</div>