<?php
use App\Lib\ConnexionUtilisateur;
use App\Model\DataObject\Groupe;
use App\Model\DataObject\User;


/** @var ?User[] $users
 * @var ?Groupe[] $groupes
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
$nomEntite = isset($users)?'utilisateur':'groupe';
$controller = isset($groupes)?'groupe':'user';
$role = isset($_GET['role'])?$_GET['role']:'votant';
?>

<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <h1>Liste des Utilisateurs :</h1> <?php
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
            <img alt="rafraichir la page" src="../assets/img/icon-refresh.svg">
        </a>
    </form>
    <h3>Ajouter</h3>
</div>


        <div class="ligneAlign">
            <form method='get' action='<?=$url?>'>
                <?php
                if(!isset($_GET['nomGroupe'])){
                    echo "<label for='r'>Ajouter des </label>
                <select name='role' id='r' onchange='this.form.submit();'>
                <option value='votant' ";
                    if($role=='votant'){
                        echo 'selected';
                    }
                    echo ">votants</option><option value='responsable'";
                    if($role=='responsable'){
                        echo 'selected';
                    }
                    echo ">responsables</option></select>";
                }
                ?>
                <input type='hidden' name='controller' value='question'>
                <input type="hidden" name="action" value="<?=$controller=='user'?'addUsersToQuestion':'addGroupesRoleQuestion'?>">
                <?=(!isset($_GET['nomGroupe']))?'<button type="submit" name="action" class="optButton"':''?>
                <?=(!isset($_GET['nomGroupe']))?$controller!='user'?' value="addUsersToQuestion"':' value="addGroupesRoleQuestion"':''?>
                <?=(!isset($_GET['nomGroupe']))?'">Sélectionner des ':''?>
                <?=(!isset($_GET['nomGroupe']))?$controller=='user'?'groupes':'utilisateurs':''?>
                <?=(!isset($_GET['nomGroupe']))?'</button>':''?>
                <input type='hidden' name='id' value='<?=$_GET['id']?>'>
            </form>
        </div>

        <?php
    if ((isset($users) && empty($users)) || (isset($groupes) && empty($groupes))){
        echo "<div class='descG'></div><div class='ligneCent'><h3> Il n'y a rien </h3></div>
                    <div class='descP'></div><div class='ligneCent'>
                    <a href=frontController.php?controller=$controller&action=readAllSelect>Clique <strong>ici</strong> pour afficher <strong>toute</strong> la liste !</a></div>";
    }
    else {
        echo "<form method='post' action='$action'><ul>";

        if($controller=='user') {
            foreach ($users as $user) {
                $idUser = rawurlencode($user->getId());
                $htmlId = ucfirst(htmlspecialchars($idUser));
                $prenom = ucfirst(htmlspecialchars($user->getPrenom()));
                $nom = ucfirst(htmlspecialchars($user->getNom()));
                    echo "<div class='ligneExt'>

                            <li class='ligneExt'>
                            <label for='cb[$idUser]' class='checkbox'>
                                <a href='frontController.php?controller=user&action=read&id=$idUser'> $htmlId </a> 
                                <span> ($prenom $nom) </span>
                            </label>
                        
                            </li>
                            <input type='checkbox' id='cb[$idUser]' name='user[$idUser]' value='$idUser'>
                            </div>";
            }
        }
        else {
            foreach ($groupes as $groupe) {
                $nomGroupe = htmlspecialchars($groupe->getId());
                $htmlnom = ucfirst(htmlspecialchars($nomGroupe));


                echo "<div class='ligneExt'>

                            <li class='ligneExt'> <a href='frontController.php?controller=groupe&action=read&nomGroupe=$nomGroupe'> $htmlnom</a></span></li>
                            <label for='checkbox' class='checkbox'> 
                                <input type='checkbox' id='cb[$nomGroupe]' name='groupe[$nomGroupe]' value='$nomGroupe'>
                            </label>
                          </div>";
            }
        }
        echo '</ul> <div class="ligneCent"> <input type="submit" value="Ajouter les ' . $nomEntite .'s selectionnés" class="optQuestion"></div></form>';
    }
    ?>
</div>
</div>