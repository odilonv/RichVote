<?php

namespace App\Model\Repository;

use App\Model\DataObject\AbstractDataObject;
use App\Model\DataObject\Commentaire;


class CommentaireRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return 'SOUVIGNETN.Commentaires';
    }

    protected function getNomClePrimaire(): string
    {
        return '';
    }

    protected function getIntitule(): string
    {
        return '';
    }

    protected function getNomsColonnes(): array
    {
        return array('');
    }

    protected function construire(array $objetFormatTableau): AbstractDataObject
    {
        return new Commentaire(
            $objetFormatTableau['IDPROPOSITION'],
            $objetFormatTableau['IDUSER'],
            $objetFormatTableau['TEXTE'],
            $objetFormatTableau['DATECOMMENTAIRE'],
            $objetFormatTableau['NBLIKE'],
            $objetFormatTableau['IDCOMMENTAIRE']
        );
    }

    public function commenter(int $idProposition,string $idUser,string $texte,string $date): void
    {
        $sql = "INSERT INTO souvignetn.commentaires(IDPROPOSITION, IDUSER, TEXTE, DATECOMMENTAIRE,NBLIKE,IDCOMMENTAIRE) VALUES($idProposition,:IDUSER,:TEXTE, to_date($date),0,0)";
        $pdo = DatabaseConnection::getInstance()::getPdo();

        ($pdo->prepare($sql))->execute(array(
            'IDUSER' => $idUser,
            'TEXTE' => $texte));
    }

    public function deleteCommentaire(int $idCommentaire):void
    {
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $sql='DELETE FROM souvignetn.commentaires WHERE idCommentaire = '.$idCommentaire;
        $pdo->query($sql);
    }

    public function selectAllProp(string $idProposition):Array
    {
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $sqlUpdate = 'CALL updatePhase()';
        $pdo->query($sqlUpdate);


        $pdoStatement = $pdo->query('SELECT * FROM souvignetn.commentaires WHERE IDPROPOSITION ='.$idProposition);

        $tabRepo = array();
        foreach($pdoStatement as $objetFormatTab){
            $tabRepo[] = $this->construire($objetFormatTab);
        }

        return $tabRepo;
    }

    public function liker($idCommentaire):void
    {
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $sql='UPDATE souvignetn.commentaires SET NBLIKE = NBLIKE+1 WHERE idCommentaire = '.$idCommentaire;
        $pdo->query($sql);
    }

    public function disliker($idCommentaire):void
    {
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $sql='UPDATE souvignetn.commentaires SET NBLIKE = NBLIKE-1 WHERE idCommentaire = '.$idCommentaire;
        $pdo->query($sql);
    }
}