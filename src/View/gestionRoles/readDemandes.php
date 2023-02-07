<?php
use App\Lib\ConnexionUtilisateur;
use App\Model\DataObject\Demande;
use App\Model\DataObject\Groupe;
use App\Model\DataObject\User;


/** @var Demande[] $demandes
 * @var string $action
 * @var string $privilegeUser
 */

// trouver un moyen de récupérer l'url pour faire un refresh
$url = 'frontController.php?';
$i = sizeof($_GET);

foreach ($_GET as $key=>$value) {
    if(array_search($value, $_GET)>0){
        $url.='&';
    }
    $url.="$key=$value";
}

$controller = isset($groupes)?'groupe':'user';
$role = isset($_GET['role'])?$_GET['role']:'votant';
?>

    <div class="block">
        <div class="text-box">
            <div class="ligneExt"> <h1>Liste des Demandes :</h1> <?php
                if(ConnexionUtilisateur::estConnecte()){
                    echo "<div class='responsive'>Vous êtes connecté en tant que :<h3>" . ucfirst($privilegeUser) . "</h3></div>";
                }
                else{
                    echo "<h3 class='responsive'>Vous n'êtes pas connecté</h3>";
                }?></div>
            <div class="ligneExt">
                <div class="ligne"></div>
                <div class="ligne"></div>
            </div>
            <div class="ligneExt"><form class="ligneAlign" method="post" action="<?=$url?>">
                    <label for="filtre"></label>
                    <input type="search" class="opt" name="filtre" id="filtre" placeholder="Rechercher un Utilisateur">
                    <button type="submit" class="opt"><img alt="recherche" src="../assets/img/icon-chercher.svg"></button>
                    <a href="<?=$url?>" id="refresh">
                        <img alt="rafraîchir la page" src="../assets/img/icon-refresh.svg">
                    </a>
                </form>
                <h3>Ajouter</h3>
            </div>
            <?php
            if ((isset($users) && empty($users)) || (isset($groupes) && empty($groupes))){
                echo "<div class='descG'></div><div class='ligneCent'><h3> Aucun résultat trouvé </h3></div>
                    <div class='descP'></div><div class='ligneCent'>
                    <a href=frontController.php?controller=$controller&action=readAllSelect>Clique <strong>ici</strong> pour afficher <strong>toute</strong> la liste !</a></div>";
            }
            else {
                echo "<form method='post' action='$action'><ul>";

                foreach ($demandes as $demande) {
                    $idDemandeur = $demande->getDemandeur()->getId();
                    $htmlId = htmlspecialchars($idDemandeur);
                    $role = $demande->getRole();
                    echo "<div class='ligneExt'>
    
                                <li class='ligneExt'>
                                <label for='cb[$idDemandeur]' class='checkbox'>
                                    <a href='frontController.php?controller=user&action=read&id=$idDemandeur'> $htmlId </a> 
                                    <span> ($role) </span>
                                </label>
                               
                                <input type='hidden' name='role[$idDemandeur]' value='$role'>
                                </li>
                                <input type='checkbox' id='cb[$idDemandeur' name='user[$idDemandeur]' value='$idDemandeur'>
                                </div>";
                }

                if(empty($demandes))
                {
                    echo '</ul><div class="ligneCent">Aucunes demandes</div><div class="descG"></div></form>';
                }
                else
                {
                    echo '</ul> <div class="ligneCent"> <input type="submit" value="Ajouter les utilisateurs selectionnés" class="optQuestion"></div></form>';
                }

            }
            ?>
        </div>
    </div>