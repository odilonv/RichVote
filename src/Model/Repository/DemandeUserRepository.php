<?php

namespace App\Model\Repository;

use App\Model\DataObject\Demande;
use App\Model\DataObject\Proposition;
use App\Model\DataObject\Question;

class DemandeUserRepository
{
    private static string $nomTable = 'view_demandes';
    private static string $nomClePrimaire = 'IDUSER';

    public static final function selectAllDemandeVoteQuestion(Question $question) : array{
        $nom = self::$nomTable;
        $sql = "SELECT * FROM $nom WHERE idQuestion=:idQuestion AND role='votant'";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $idQuestion = $question->getId();

        $param = [
            'idQuestion' => $idQuestion
        ];

        $pdoStatement->execute($param);

        $result = [];
        foreach ($pdoStatement as $id) {
            $demandeur = (new UserRepository())->select($id[self::$nomClePrimaire]);
            $result[] = new Demande('votant', $question, $demandeur, null);
        }
        return $result;
    }

    public static final function selectAllDemandeQuestion($question) : array{
        $nomTable = self::$nomTable;
        $sql = "SELECT * FROM $nomTable WHERE idQuestion=:idQuestion AND idProposition IS NULL";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $idQuestion = $question->getId();

        $param = [
            'idQuestion' => $idQuestion
        ];

        $pdoStatement->execute($param);

        $result = [];
        foreach ($pdoStatement as $tab) {
            $demandeur = (new UserRepository())->select($tab[self::$nomClePrimaire]);
            $result[] = new Demande($tab['ROLE'], $question, $demandeur, null);
        }
        return $result;
    }

    public static final function selectAllDemandeAuteurProposition(Proposition $proposition) : array{
        $nomTable = self::$nomTable;
        $sql = "SELECT * FROM $nomTable WHERE idProposition=:idProposition";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $idProposition = $proposition->getId();

        $param = [
            'idProposition' => $idProposition
        ];

        $pdoStatement->execute($param);

        $result = [];
        foreach ($pdoStatement as $id) {
            $demandeur = (new UserRepository())->select($id[self::$nomClePrimaire]);
            $question = (new QuestionRepository())->select($proposition->getIdQuestion());
            $result[] = new Demande('auteur', $question, $demandeur, $proposition);
        }
        return $result;
    }

    public static final function selectAllDemandeDemandeur(string $idDemandeur) : array{
        $nomTable = self::$nomTable;
        $clePrimaire = self::$nomClePrimaire;
        $sql = "select * from $nomTable where $clePrimaire=:idDemandeur";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $param = [
            'idDemandeur' => $idDemandeur
        ];

        $pdoStatement->execute($param);

        $demandeur = (new UserRepository())->select($idDemandeur);
        $result = [];
        foreach ($pdoStatement as $tab){
            $question = (new QuestionRepository())->select($tab['IDQUESTION']);
            $proposition = isset($tab['IDPROPOSITION']) ? (new PropositionRepository())->select($tab['IDPROPOSITION']) : null;
            $demande = new Demande($tab['ROLE'], $question, $demandeur, $proposition);
            $result[] = $demande;
        }
        return $result;
    }

    public static final function delete(Demande $demande){
        $nomTable = self::$nomTable;
        $clePrimaire = self::$nomClePrimaire;

        $param=[
            'idDemandeur' => $demande->getDemandeur()->getId(),
            'idQuestion' => $demande->getQuestion()->getId(),
            'role' => $demande->getRole()
        ];
        if($demande->getProposition()!=null){
            $param['idProposition'] = $demande->getProposition()->getId();
            $sql = "delete from $nomTable where $clePrimaire=:idDemandeur AND idQuestion=:idQuestion AND role=:role AND idProposition=:idProposition";
        }
        else{
            $sql = "delete from $nomTable where $clePrimaire=:idDemandeur AND idQuestion=:idQuestion AND role=:role";
        }
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $pdoStatement->execute($param);
    }

    public static function sauvegarder(Demande $demande): bool{
        $sql = "call sauvegarderDemande(:typeDemande, :idUser, :idQuestion, :idProposition)";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $idProposition = $demande->getProposition()!=null?$demande->getProposition()->getId():null;
        $params = [
            'typeDemande' => $demande->getRole(),
            'idUser' => $demande->getDemandeur()->getId(),
            'idQuestion' => $demande->getQuestion()->getId(),
            'idProposition' => $idProposition
        ];

        return $pdoStatement->execute($params);
    }

    public static function aDejaDemande($idUser,$idQuestion):bool
    {
        $sql = "SELECT IDUSER FROM  SOUVIGNETN.VIEW_DEMANDES WHERE IDUSER = :IDUSER AND IDQUESTION = ".$idQuestion;
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);
        $params = [
            'IDUSER' => $idUser
        ];

        $pdoStatement->execute($params);

        if(!$pdoStatement->fetch())
        {
            return false;
        }
        else
            return true;
    }
}