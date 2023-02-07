<?php

use App\Model\DataObject\Phase;

/** @var Phase $phase
 * @var int $numeroPhase
 */
$id = $phase->getId();
$dateDebut = $phase->getDateDebut()->format('Y-m-d');
$dateFin = $phase->getDateFin()->format('Y-m-d');
$type = $phase->getType();
$nbPlaces = $phase->getNbDePlaces();
?>
<div class="descP"></div>
Modifier la phase de rédaction
<div id="phase<?=$id?>">
    <div>Début :
        <input type="date" name="dateDebut[<?=$id?>]" value="<?=$dateDebut?>" <?=($phase->estCommence()||$phase->estFinie())?'readonly':''?>>
    </div><div class="descP"></div>
    <div>Fin :
        <input type="date" name="dateFin[<?=$id?>]" value="<?=$dateFin?>" <?=($phase->estCommence()||$phase->estFinie())?'readonly':''?>>
    </div><div class="descP"></div>

    <div class="descG"></div>
    <input type="hidden"  name="type[<?=$id?>]" value="redaction">
    <input type="hidden" name="nbDePlaces[<?=$id?>]" value="1">
</div>