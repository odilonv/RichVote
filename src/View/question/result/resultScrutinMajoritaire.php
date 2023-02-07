<?php
/**
 * @var array $propositionsScore
 */
$classement=0;
$scoretotal=0;
$cptligne=0;
foreach ($propositionsScore as [$proposition,$score]) {
    $idProposition = $proposition->getId();
    $scoretotal+=$score;
}
foreach ($propositionsScore as [$proposition,$score]) {
    $classement++;
    $idProposition = $proposition->getId();
    $widthLigne=($score/$scoretotal)*100;
    $widthLigne=round($widthLigne);
    echo "<h3>" . $classement . ". " . ucfirst(htmlspecialchars($proposition->getIntitule())) . " avec un score de : " . $score . "</h3>";
    echo '<style>.lineresults'.$cptligne.'{width: '.$widthLigne.'%; display: flex;background: white;height: 8px;border-radius: 20px}</style>';
    echo '<div class="ligneExt"><div class="lineresults'.$cptligne.'"></div><p id="petit">'.$widthLigne.' %</p></div>';
    $cptligne++;
}