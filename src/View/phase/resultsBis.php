<?php

use App\Model\Repository\PhaseRepository;

$phases=(new PhaseRepository())->getPhasesIdQuestion($question->getId());

$bool=0;
foreach ($phases as $phase)
{
    if($phase->getType()!="consultation"){
        $bool=1;
    }
}
$cpt=0;
if($bool==1){
    foreach ($phases as $phase) {
        $cpt = $cpt + 1;
        echo '<h3>Phase ' . $cpt . '</h3>';
        if ($phase->getType() == "consultation") {
            echo "<p>Il n'y a pas de vote sur cette phase.</p>";
        }
        else {
            $scores = [];
            $propositions = [];

            $propositionsScore = (new PropositionRepository())->selectAllWithScore($phase->getId());
            foreach ($propositionsScore as $proposition) {
                $propositions[] = $proposition[0];
                $scores[$proposition[0]->getId()] = $proposition[1];
            }

            foreach ($propositions as $proposition) {
                $idProposition = $proposition->getId();
                $score = $scores[$idProposition];
                echo "<p>Proposition : " . $proposition->getIntitule() ." score : ". $score . "</p>";
            }
        }
    }
}
else{
    if($phases[0]->getType()=="consultation"){
        echo "<h3>Il n'y a pas eu de vote sur cette question.</h3>";

    }
}