<?php
namespace App\Model\Repository;

use App\Model\DataObject\AbstractDataObject;

abstract class AbstractRepository{
    protected abstract function getNomTable() : string;

    protected abstract function getNomClePrimaire() : string;

    protected abstract function getIntitule() : string;

    protected abstract function getNomsColonnes(): array;

    protected abstract function construire(array $objetFormatTableau) : AbstractDataObject;

    public function selectAll(): array{
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $sqlUpdate = 'CALL updatePhase()';
        $pdo->query($sqlUpdate);

        $nomTable = $this->getNomTable();

        $pdoStatement = $pdo->query('SELECT * FROM '. $nomTable);

        $tabRepo = array();
        foreach($pdoStatement as $objetFormatTab){
            $tabRepo[] = $this->construire($objetFormatTab);
        }

        return $tabRepo;
    }

    public function search(string $recherche): array{
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $nomTable = $this->getNomTable();

        $pdoStatement = $pdo->query('SELECT * FROM '. $nomTable . ' WHERE LOWER('. $this->getIntitule() .") LIKE '%".$recherche."%' ORDER BY ". $this->getIntitule(). " DESC");

        $tabRepo = array();
        foreach($pdoStatement as $objetFormatTab){
            $tabRepo[] = $this->construire($objetFormatTab);
        }

        return $tabRepo;
    }



    public function select(string $id) : ?AbstractDataObject{
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $nomTable = $this->getNomTable();
        $nomId = $this->getNomClePrimaire();

        $sql = "SELECT * FROM $nomTable WHERE $nomId = '" . $id."'";
        $pdostatement = $pdo->query($sql);

        $objectTab = $pdostatement->fetch();
        if(!$objectTab) {
            return null;
        }
        else {
            $object = $this->construire($objectTab);
            return $object;
        }

    }



    public function delete(string $valeurClePrimaire): void
    {
        $sql = "DELETE FROM ". $this->getNomTable() ." WHERE ". $this->getNomClePrimaire()." = :objetTag";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);
        $values = array(
            "objetTag" => $valeurClePrimaire,
        );
        $pdoStatement->execute($values);
    }


    public function update(AbstractDataObject $object): void{
        $txtsql="";
        $nomColonnes = $this->getNomsColonnes();
        foreach ($nomColonnes as $nomColonne){
            if($nomColonne!=$nomColonnes[0]){
                $txtsql .= ', ';
            }
            $txtsql .= "$nomColonne = :$nomColonne" . 'Tag';
        }

        $sql = "UPDATE ".$this->getNomTable() ." SET ".$txtsql." WHERE " . $this->getNomClePrimaire() . "='".$object->getId()."'";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute($object->formatTableau());
    }




}