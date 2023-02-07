<?php

namespace App\Model\Repository;

use App\Model\DataObject\AbstractDataObject;
use App\Model\DataObject\Groupe;

class GroupeRepository extends AbstractRepository
{
    protected function getNomTable(): string
    {
        return 'GROUPEUSERS';
    }

    protected function getNomClePrimaire(): string
    {
        return 'nomGroupe';
    }

    protected function getIntitule(): string
    {
        return 'nomGroupe';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'nomGroupe',
            'idUserResponsable'
        ];
    }

    protected function construire(array $objetFormatTableau): AbstractDataObject
    {
        return new Groupe(
            $objetFormatTableau['NOMGROUPE'],
            $objetFormatTableau['IDUSERRESPONSABLE'],
            $this->getIdMembres($objetFormatTableau['NOMGROUPE']));
    }

    public function update(AbstractDataObject $object): void
    {
        parent::update($object);

        $idMembres = $object->getIdMembres();
        $this->insertMembres($object->getId(), $idMembres);
    }


    public function getIdMembres(string $nomGroupe) : array{
        $sql = 'SELECT idUser FROM AppartientGroupe WHERE nomGroupe=:nomGroupe';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $pdoStatement->execute(['nomGroupe'=>$nomGroupe]);

        $result = [];
        foreach ($pdoStatement as $idUser){
            $result[] = $idUser['IDUSER'];
        }
        return $result;
    }

    public static function selectAllGroupeIdUser(string $idUser) : array{
        $sql = 'SELECT nomGroupe from AppartientGroupe WHERE idUser=:idUser';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);
        $param = ['idUser' => $idUser];
        $pdoStatement->execute($param);

        $result = [];
        foreach ($pdoStatement as $tab){
            $result[] = (new GroupeRepository())->select($tab['NOMGROUPE']);
        }

        return $result;
    }

    public function sauvegarder(Groupe $groupe){
        $sql = "INSERT INTO GROUPEUSERS(nomGroupe, idUserResponsable) VALUES(:nomGroupe, :idUser)";

        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $pdoStatement->execute(['nomGroupe' => $groupe->getId(),
            'idUser' => $groupe->getIdResponsable()]);

        // insertions des membres
        $this->insertMembres($groupe->getId(), $groupe->getIdMembres());
    }

    private function insertMembres(string $nomGroupe, array $idMembres){
        $sql = "CALL insertMembres(:nomGroupe, :idUser)";

        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        foreach ($idMembres as $idMembre){
            $pdoStatement->execute(['idUser'=>$idMembre, 'nomGroupe'=>$nomGroupe]);
        }
    }
}