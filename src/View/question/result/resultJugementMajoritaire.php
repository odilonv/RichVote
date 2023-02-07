<?php
/**
 * @var array $propositionsScore
 */

if(!isset($propositionsScore[0])){
    echo '<h1>Il n\'y a pas eu de proposition';
}
else {

    echo '<h1>Gagnant : ' . $propositionsScore[0][0]->getIntitule() . '</h1>';
    $nbParticipant = 0;
    $cptligne = 0;
    if (isset($propositionsScore[0])) {
        $infoScore = $propositionsScore[0][1];
        foreach ($infoScore as $score) {
            $nbParticipant += $score;
        }
    }
    foreach ($propositionsScore as [$proposition, $scores]) {
        echo '<br>' . $proposition->getIntitule() . '   -> scores: <ol>';
        foreach ($scores as $nomScore => $score) {
            $tradNom = "";
            switch ($nomScore) {
                case '0':
                    $tradNom = 'à rejeter';
                    break;
                case '1':
                    $tradNom = 'insuffisant';
                    break;
                case '2':
                    $tradNom = 'passable';
                    break;
                case '3':
                    $tradNom = 'assez bien';
                    break;
                case '4':
                    $tradNom = 'bien';
                    break;
                case '5':
                    $tradNom = 'très bien';
                    break;

            }
            echo "<li>$tradNom : $score </li>";
        }
        echo '</ol>';
    }
}


