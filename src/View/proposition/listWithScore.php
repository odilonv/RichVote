<?php
//liste des propositions pour une question donnée

use App\Model\DataObject\Phase;
use App\Model\DataObject\Proposition;
/** @var array $propositions
 *  @var array $scores
 */
?>

<div class="block">
    <div class="text-box">
        <div class="ligneExt"> <div><a class="optQuestion" href=frontController.php?controller=question&action=read&id=<?= rawurlencode($_GET['id'])?>></a><h1>Propositions publiées :</h1></div> <div>Vous êtes connecté en tant que : <h3>Organisateur </h3></div></div>
        <div class="ligneExt"><div class="ligne"></div><div class="ligne"></div></div>
        <ul>
            <?php
                foreach ($propositions as $proposition) {
                    $idProposition = $proposition->getId();
                    $score = $scores[$idProposition];
                    echo '<li class="ligneExt"><a class="atxt" href=frontController.php?controller=proposition&action=read&id=' . rawurlencode($idProposition) . '>' . htmlspecialchars($proposition->getIntitule()) .' score: ' . $score . '</a></li>';
                }
            ?>
        </ul>
    </div>
</div>