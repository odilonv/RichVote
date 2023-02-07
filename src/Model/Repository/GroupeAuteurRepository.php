<?php

namespace App\Model\Repository;

use App\Model\DataObject\Proposition;

class GroupeAuteurRepository
{
    public function getIdAuteursProposition(string $idProposition) : array{
        $sql = "SELECT idAuteur FROM AuteurProposition WHERE idProposition=:idProposition";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $pdoStatement->execute(['idProposition' => $idProposition]);

        $idAuteurs = [];
        foreach ($pdoStatement as $tabId){
            $idAuteurs[] = $tabId['IDAUTEUR'];
        }
        return $idAuteurs;
    }

    public function sauvegarderGroupeProposition(Proposition $proposition){
        $sql = 'call SETROLEAUTEUR(:idUser, :idProposition, :idQuestion)';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $idProposition = $proposition->getId();
        $idQuestion = $proposition->getIdQuestion();
        foreach ($proposition->getIdAuteurs() as $idAuteur){
            $pdoStatement->execute([
                'idUser' => $idAuteur,
                'idProposition' => $idProposition,
                'idQuestion' => $idQuestion
            ]);
        }
    }

    public function updateGroupeProposition(Proposition $proposition){
        $sql = 'call updateAuteur(:idUser, :idProposition, :idQuestion)';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $idProposition = $proposition->getId();
        $idQuestion = $proposition->getIdQuestion();
        foreach ($proposition->getIdAuteurs() as $idAuteur){
            $pdoStatement->execute([
                'idUser' => $idAuteur,
                'idProposition' => $idProposition,
                'idQuestion' => $idQuestion
            ]);
        }
    }
}