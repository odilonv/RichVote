<?php

namespace App\Model\Repository;

use App\Model\DataObject\AbstractDataObject;
use App\Model\DataObject\Phase;

class PhaseRepository extends AbstractRepository
{
    protected function getNomTable(): string
    {
        return 'vue_PhasesDetail';
    }

    protected function getNomClePrimaire(): string
    {
        return 'idPhase';
    }

    protected function getNomsColonnes(): array
    {
        return ['IDPHASE',
                'DATEDEBUT',
                'DATEFIN',
                'TYPEPHASE',
                'NBDEPLACES'];
    }

    protected function construire(array $objetFormatTableau): AbstractDataObject
    {
        return new Phase($objetFormatTableau['IDPHASE'],
                        $objetFormatTableau['TYPEPHASE'],
                        date_create_from_format('d/m/y',$objetFormatTableau['DATEDEBUT']),
                        date_create_from_format('d/m/y',$objetFormatTableau['DATEFIN']),
                        $objetFormatTableau['NBDEPLACES']);
    }

    public function getCurrentPhase(string $idQuestion) : ?AbstractDataObject{
        $sql = "SELECT * FROM vue_PhasesDetail
	            WHERE idQuestion = $idQuestion
	            AND dateDebut<=SYSDATE AND dateFin>SYSDATE";
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $statement = $pdo->query($sql);
        $result = $statement->fetch();
        if(!isset($result['TYPEPHASE'])){
            return Phase::emptyPhase();
        }
        else{
            return $this->construire($result);
        }
    }

    public function endPhase(String $phase) : void{
        $sql = "CALL end_phase(" . $phase . ")";
        DatabaseConnection::getInstance()::getPdo()->query($sql);
        $this->updatePhase();
    }

    public function startPhase(String $phase) : void{
        $sql = "CALL start_phase(" . $phase . ")";
        DatabaseConnection::getInstance()::getPdo()->query($sql);
        $this->updatePhase();
    }

    public function updatePhase():void{
        $sql = 'CALL updatePhase()';
        DatabaseConnection::getInstance()::getPdo()->query($sql);
    }

    public function getPhasesIdQuestion(string $idQuestion) : array{
        $sql = "SELECT * FROM vue_PhasesDetail WHERE idQuestion = :idQuestion ORDER BY idPhase";
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute(['idQuestion' => $idQuestion]);

        $result = [];
        foreach ($pdoStatement as $formatTableau){
            $result[] = $this->construire($formatTableau);
        }
        return $result;
    }

    public function estFini(string $idPhase) : bool{
        $sql = "SELECT :idPhase FROM Phases p WHERE to_date(datefin, 'DD/MM/YY') >= SYSDATE";
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute(
            ['idPhase' => $idPhase]
        );

        return $pdoStatement->fetch()[0]=='1';
    }

    protected function getIntitule(): string
    {
        return "";
    }
}