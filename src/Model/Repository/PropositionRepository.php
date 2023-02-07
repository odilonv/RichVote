<?php

namespace App\Model\Repository;

use App\Model\DataObject\AbstractDataObject;
use App\Model\DataObject\Proposition;

class PropositionRepository extends AbstractRepository
{
    public static function voter(string $idProposition, string $idUser, int $score)
    {
        $sql = "CALL voter(:idUser, :idProposition, :score)";
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdoStatement = $pdo->prepare($sql);

        $param = ['idUser' => $idUser,
            'idProposition' => $idProposition,
            'score' => $score];
        $pdoStatement->execute($param);
    }

    protected function getNomTable(): string
    {
        return 'SOUVIGNETN.PROPOSITIONS';
    }

    protected function construire(array $objetFormatTableau): AbstractDataObject
    {
        $archive = false;
        if($objetFormatTableau['ARCHIVE'] == 'V'){$archive = true;}
        return new Proposition(
            $objetFormatTableau['IDPROPOSITION'],
            $objetFormatTableau['IDQUESTION'],
            $objetFormatTableau['IDRESPONSABLE'],
            null,
            $objetFormatTableau['INTITULE'],
            $archive,
            (new GroupeAuteurRepository())->getIdAuteursProposition($objetFormatTableau['IDPROPOSITION'])
        );
    }

    protected function getNomClePrimaire(): string
    {
        return 'idProposition';
    }

    protected function getNomsColonnes(): array
    {
        return [
            "idProposition",
            "idQuestion",
            "idResponsable",
            "intitule"
        ];

    }


    public function select(string $idProposition) : AbstractDataObject{
        $proposition = parent::select($idProposition);

        $sectionsTexte = (new SectionRepository())->getSectionsProposition($proposition->getId());

        $proposition->setSectionsTexte($sectionsTexte);

        return $proposition;
    }

    //retourne toutes les propositions en lien avec une question
    public function selectAllForQuestion(string $idQuestion) : array{
        $sql = 'SELECT * FROM SOUVIGNETN.PROPOSITIONS WHERE idQuestion = :id';

        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute(array('id' => $idQuestion));

        $arrayProposition = [];
        foreach ($pdoStatement as $propositionFormatTab){
            $arrayProposition[] = $this->select($propositionFormatTab['IDPROPOSITION']);
        }
        return $arrayProposition;
    }

    public function selectAllfromResponsable(string $id): ?array
    {
        $sql = "SELECT * FROM SOUVIGNETN.PROPOSITIONS WHERE idResponsable='" . $id ."'";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->query($sql);

        $propositions = [];
        foreach ($pdoStatement as $propositionTab){
            $propositions[] = $this->construire($propositionTab);
        }

        return $propositions;
    }


     public function sauvegarder(AbstractDataObject $proposition) : AbstractDataObject{
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $sql = "CALL creerProposition(:idQuestion, :idResponsable)";

        $pdoStatement = $pdo->prepare($sql);

        $params = ['idQuestion' => $proposition->getIdQuestion(),
            'idResponsable' => $proposition->getIdResponsable()];

        $pdoStatement->execute($params);

        $sqlIdP = "select propositions_seq.CURRVAL as id from DUAL";

         (new GroupeAuteurRepository())->sauvegarderGroupeProposition($proposition);

        return $this->select($pdo->query($sqlIdP)->fetch()['ID']);
    }

    public function update(AbstractDataObject $object): void
    {
        parent::update($object);
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $sql = "update SOUVIGNETN.PROPOSERTEXTE SET texte = :texte WHERE idProposition = :idProposition AND idSection = :idSection";
        $pdoStatement = $pdo->prepare($sql);

        $newSectionsTexte = $object->getSectionsTexte();
        foreach($newSectionsTexte as $infos){
            $params = array(
                'idProposition' => $object->getIdProposition(),
                'idSection' => $infos['section']->getId(),
                'texte' => $infos['texte']
            );
            $pdoStatement->execute($params);
        }

        (new GroupeAuteurRepository())->updateGroupeProposition($object);
    }

    public function setScore(Proposition $proposition, int $score){
        //$sql = 'CALL voter(' . $idProposition . ", $score)"; la procédure ne marche pas
        $currentPhase = (new PhaseRepository())->getCurrentPhase($proposition->getIdQuestion());
        $idProposition = $proposition->getId();
        $sql = "UPDATE SESSIONVOTE sv set sv.score = $score where IDPROPOSITION = $idProposition AND sv.IDPHASEVOTE = "  . $currentPhase->getId();
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdo->query($sql);
    }

    protected function getIntitule(): string
    {
        return "intitule";
    }

    /*public function  getIntituleQuestion(string $idProposition): string
    {
        $sql = 'SELECT INTITULEQUESTION
                FROM SOUVIGNETN.QUESTIONS q
                JOIN SOUVIGNET.PROPOSITIONS p ON q.idQuestion = p.idQuestion
                WHERE idProposition = $idProposition';

        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute('id' => $idQuestion);

        return $arrayProposition;


        $sql = "SELECT * FROM vue_PhasesDetail WHERE idQuestion = :idQuestion";
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute(['idQuestion' => $idQuestio
    }*/

    // utile que pour récupérer le score lors d'un jugement majoritaire
    public function selectAllWithScoreJugement(string $idPhase): array // forme [Poposition, [% tres bien, % assez bien, ...]]
    {
        $sql = 'SELECT idProposition, scoreVote, count(scoreVote) as nbVote 
                FROM votantProposition vp
                WHERE idPhaseVote=:idPhase
                GROUP BY (idProposition, scoreVote)';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $pdoStatement->execute(['idPhase' => $idPhase]);

        $idPropositionScore = [];
        foreach ($pdoStatement as $infoVote){
            $idPropositionScore[$infoVote['IDPROPOSITION']][$infoVote['SCOREVOTE']] = $infoVote['NBVOTE'];
        }

        $result = [];
        foreach ($idPropositionScore as $idProposition => $infoScore){
            $result[] = [$this->select($idProposition), $infoScore];
        }

        return $result;
    }

    public function selectAllWithScore(string $idPhase): array{ // forme [Proposition, score]
        $sql = 'SELECT p.idProposition, idResponsable, p.idQuestion, intitule, archive, score  
                FROM sessionVote sv
                JOIN Propositions p ON p.idProposition=sv.idProposition
                where idPhaseVote=:idPhase
                ORDER BY score DESC';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $pdoStatement->execute(['idPhase'=>$idPhase]);

        $result = [];
        foreach ($pdoStatement as $infoProposition){
            $proposition = $this->construire([
                "IDPROPOSITION" => $infoProposition["IDPROPOSITION"],
                "IDQUESTION" => $infoProposition["IDQUESTION"],
                "IDRESPONSABLE" => $infoProposition["IDRESPONSABLE"],
                "INTITULE" => $infoProposition["INTITULE"],
                "ARCHIVE" => $infoProposition["ARCHIVE"]]);
            $result[] = [$proposition, $infoProposition['SCORE']];
        }

        return $result;
    }

    public function selectAllWithScoreForUser(string $idPhase, string $idUser): array
    { // comme au dessus sauf que c'est pour un user (propal de score 0 si pas votée)
        $sql = 'SELECT vp.idProposition, idResponsable, intitule, archive, p.idQuestion, NVL(scoreVote, 0) as scoreVote
                FROM VotantProposition vp
                RIGHT JOIN Propositions p ON p.idProposition=vp.idProposition
                WHERE idPhaseVote=:idPhase AND idVotant=:idUser';

        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $pdoStatement->execute([
            'idPhase' => $idPhase,
            'idUser' => $idUser
        ]);

        $result = [];
        foreach ($pdoStatement as $infoProposition){
            $proposition = $this->construire([
                "IDPROPOSITION" => $infoProposition["IDPROPOSITION"],
                "IDQUESTION" => $infoProposition["IDQUESTION"],
                "INTITULE" => $infoProposition["INTITULE"],
                "ARCHIVE" => $infoProposition["ARCHIVE"],
                "IDRESPONSABLE" => $infoProposition["IDRESPONSABLE"]]);
            $result[] = [$proposition, $infoProposition['SCOREVOTE']];
        }

        return $result;
    }

    public function addAuteursProposition(array $users, Proposition $proposition){
        $sql = "INSERT INTO AuteurProposition(idAuteur, idProposition, idQuestion) VALUES (:idAuteur, :idProposition, :idQuestion)";

        // pour s'assurer que l'utilisateur a bien le role d'auteur sur la question
        $sqlRole = 'Call setRoleQuestion(:idAuteur, \'auteur\', :idQuestion)';

        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatementRole = $pdo->prepare($sqlRole);

        $idProposition = $proposition->getId();
        $idQuestion = $proposition->getIdQuestion();
        foreach ($users as $idUser){
            $param = [
                'idAuteur' => $idUser,
                'idProposition' => $idProposition,
                'idQuestion' => $idQuestion
            ];
            $paramRole = [
                'idAuteur' => $idUser,
                'idQuestion' => $idQuestion
            ];
            $pdoStatementRole->execute($paramRole);
            $pdoStatement->execute($param);
        }
    }

    public function addGroupeAuteur(array $groupes, Proposition $proposition){
        $sql = 'call setRolePropositionGroupe(:nomGroupe, :role, :idProposition)';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $idProposition = $proposition->getId();
        foreach($groupes as $nomGroupe){
            $param = [
                'nomGroupe' => $nomGroupe,
                'role' => 'auteur',
                'idProposition' => $idProposition
            ];
            $pdoStatement->execute($param);
        }
    }

    public function estAuteur(string $idUser, Proposition $proposition) : bool{
        $sql = "SELECT COUNT(idAuteur) FROM AUTEURPROPOSITION
                WHERE idAuteur=:idUser AND idProposition=:idProposition";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);

        $parametres = [
            'idUser' => $idUser,
            'idProposition' => $proposition->getId()
        ];
        $pdoStatement->execute($parametres);

        $result = $pdoStatement->fetch()[0];
        return $result>0;
    }

    public function selectAllByDate($idQuestion): ?array
    {
        $sql = "SELECT * FROM SOUVIGNETN.PROPOSITIONS  WHERE idQuestion = ".$idQuestion."ORDER BY  idquestion DESC";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->query($sql);

        $propositions = [];
        foreach ($pdoStatement as $questionTab){
            $propositions[] = $this->construire($questionTab);
        }

        return $propositions;
    }

    public function selectResponsable(string $idProposition): string{
        $sql = 'SELECT idResponsable FROM SOUVIGNETN.propositions WHERE IDPROPOSITION = '.$idProposition;
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute();

        return $pdoStatement->fetch()[0];
    }


}