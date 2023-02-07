<?php

namespace App\Model\Repository;

use App\Model\DataObject\AbstractDataObject;
use App\Model\DataObject\Proposition;
use App\Model\DataObject\User;
use App\Lib\VerificationEmail;

class UserRepository extends AbstractRepository
{
    function getNomTable(): string
    {
        return 'SOUVIGNETN.USERS';
    }

    protected function getNomClePrimaire(): string
    {
        return '"idUser"';
    }

    protected function getNomsColonnes(): array
    {
        return [
            '"idUser"',
            "MDP",
            "PRENOMUSER",
            "NOMUSER",
            '"role"',
            "EMAIL"
        ];

    }

    protected function construire(array $objetFormatTableau): AbstractDataObject
    {
        return new User(
            $objetFormatTableau['idUser'],
            $objetFormatTableau['MDP'],
            $objetFormatTableau['PRENOMUSER'],
            $objetFormatTableau['NOMUSER'],
            $objetFormatTableau['role'],
            $objetFormatTableau['EMAIL']
        );
    }

    public function setMdpHache(string $mdpClair): string
    {
        return hash('sha256', $mdpClair);
    }

    public function checkMdp(string $mdp):string
    {
        $erreursMdp = '';

        $mdpLong = false;
        if(strlen($mdp) >= 8)
        {
            $mdpLong = true;
        }
        else
        {
            $erreursMdp .= 'Longueur de 8 caractères minimum requise <br>';
        }


        $alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $contientMaj = false;
        foreach ($alphabet as $lettre)
        {
            if(strpos($mdp, $lettre) !== false)
            {
                $contientMaj = true;
            }
        }

        if(!$contientMaj) {
            $erreursMdp .= 'Au moins une majuscule requise <br>';
        }

        $chiffres = [0,1,2,3,4,5,6,7,8,9];
        $contientChiffre = false;
        foreach ($chiffres as $chiffre)
        {
            if(strpos($mdp, $chiffre) !== false)
            {
                $contientChiffre = true;
            }
        }

        if(!$contientChiffre)
        {
            $erreursMdp .= 'Au moins un chiffre requis <br>';
        }

        $specialChars = ['&','"','#','~','\'','{','(','[','-','|','è','`','_','^','à','@',')',']','=','}','+','°','^','$','*','¨','$','£','€','µ','%','ù','!','§','/',';','.',',','?'];

        $contientSpecialChar = false;
        foreach ($specialChars as $speChar)
        {
            if(strpos($mdp, $speChar) !== false)
            {
                $contientChiffre = true;
            }
        }

        if(!$contientSpecialChar)
        {
            $erreursMdp .= 'Au moins un caractère special requis <br>';
        }


        if($mdpLong && $contientMaj && $contientChiffre && $contientSpecialChar)
        {
            return 'true';
        }
        else
        {
            return $erreursMdp;
        }

    }



    public function checkCmdp(string $mdp, string $cmdp):bool
    {
        if($mdp != $cmdp)
        {
            return false;
        }
        return true;
    }

    public function checkId(string $idUser):bool
    {

        $sql = "SELECT \"idUser\" FROM souvignetn.Users WHERE \"idUser\" = :idUser";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);
        $pdoStatement->execute(array(
            'idUser' => $idUser
        ));

        if ($pdoStatement->fetch()) {
            return false;
        }
        return true;

        //return var_dump($pdoStatement->fetch());
    }

    public function selectAllValide(): array{
        $pdo = DatabaseConnection::getInstance()::getPdo();


        $pdoStatement = $pdo->query('SELECT * FROM souvignetn.users WHERE "idUser" NOT IN(SELECT IDUSER FROM SOUVIGNETN.EMAILUSERSINVALIDE)');

        $tabRepo = array();
        foreach($pdoStatement as $objetFormatTab){
            $tabRepo[] = $this->construire($objetFormatTab);
        }

        return $tabRepo;
    }

    public function checkEmail(string $email):bool
    {

        $sql = "SELECT * FROM souvignetn.Users WHERE EMAIL = :email";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);
        $pdoStatement->execute(array(
            'email' => $email
        ));

        if ($pdoStatement->fetch()) {
            return false;
        }
        return true;

        //return var_dump($pdoStatement->fetch());
    }

    public function getRoleQuestion(string $user, string $question) : ?string{
        $sql = 'SELECT getRoleQuestion(:idUser, :idQuestion) FROM DUAL';
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute(
                ['idUser' => $user,
                'idQuestion' => $question]
        );


        return $pdoStatement->fetch()[0];
    }

    public function getPrivilege(string $id) : string{
        $sql = 'SELECT "role" FROM souvignetn.Users WHERE "idUser" = :id';
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute(array(
            'id' => $id
        ));


        return $pdoStatement->fetch()[0];
    }



    public function voter(User $user, Proposition $proposition, int $score){
        $sql = "CALL voter(:idVotant, :idProposition, :scoreVote)";
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdoStatement = $pdo->prepare($sql);

        $parametres = [
            'scoreVote' => $score,
            'idVotant' => $user->getId(),
            'idProposition' => $proposition->getId()
        ];

        $pdoStatement->execute($parametres);
    }

    protected function getIntitule(): string
    {
        return '"idUser"';
    }

    public function sauvegarder(User $object): void
    {
        $colonnes = "";
        foreach ($this->getNomsColonnes() as $colonne) {
            if(!$colonnes =="") {
                $colonnes .= ',';
            }
            $colonnes .= $colonne;
        }

        $values = "";
        foreach ($this->getNomsColonnes() as $value) {
            if(!$values =="") {
                $values .= ',';
            }
            $values .= ":".$value;
        }

        $sql = 'INSERT INTO '.$this->getNomTable().'('.$colonnes.') VALUES('.$values.')';
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute($object->formatTableau());
    }


    public function update(AbstractDataObject $object): void{
        $txtsql="";
        $nomColonnes = $this->getNomsColonnes();
        foreach ($nomColonnes as $nomColonne){
            if($nomColonne!=$nomColonnes[0]){
                $txtsql .= ', ';
            }
            if($this->getNomTable()=='SOUVIGNETN.USERS')
            {
                $txtsql .= "$nomColonne = :$nomColonne";
            }
            else
            {
                $txtsql .= "$nomColonne = :$nomColonne".'Tag';
            }
        }

        if($this->select($object->getId()) != null)
        {
            $sql = "UPDATE ".$this->getNomTable() ." SET ".$txtsql." WHERE " . $this->getNomClePrimaire() . "='".$object->getId()."'";
        }
        else
        {
            $sql = "UPDATE ".$this->getNomTable() ." SET ".$txtsql." WHERE EMAIL='".$object->getEmail()."'";
        }

        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute($object->formatTableau());
    }

    public function getDemandeVote(string $idQuestion) : array{
        $sql = "SELECT * FROM DemmandeVotant WHERE idQuestion=:idQuestion";
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);

        $users = [];
        if($pdoStatement->execute(['idQuestion' => $idQuestion])) {
            foreach ($pdoStatement as $votant) {
                $users[] = (new UserRepository())->select($votant['idUser']);
            }
        }
        return $users;
    }

    public function demanderAccesVote(string $idUser, string $idQuestion) : bool{
        $sql = "CALL demandeAccesVote(:idUser, :idQuestion)";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);



        return $pdoStatement->execute([
            'idQuestion' => $idQuestion,
            'idUser' => $idUser]);
    }

    public function validerUser(User $user)
    {
        $sql = "DELETE FROM SOUVIGNETN.EMAILUSERSINVALIDE WHERE IDUSER = :idUser";
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);
        $values = array(
            "idUser" => $user->getId()
        );
        $pdoStatement->execute($values);
    }

    public function mailDeValidation(User $user)
    {
        $sql = 'UPDATE SOUVIGNETN.EMAILUSERSINVALIDE SET nonce = :nonce WHERE IDUSER = :idUser';
        $pdoStatement = DatabaseConnection::getInstance()::getPdo()->prepare($sql);
        $values = array(
            "nonce" => $this->genererNonce(),
            "idUser" => $user->getId()
        );
        $pdoStatement->execute($values);

        VerificationEmail::envoiEmailValidation($user);

    }

    function genererNonce():string {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[rand(0, 9)];
        }
        return $randomString;
    }

    public function emailExiste(string $email) :bool
    {
        $pdo = DatabaseConnection::getInstance()::getPdo();


        $sql = "SELECT email FROM souvignetn.users where email = '".$email."'";

        $pdostatement = $pdo->query($sql);

        $objectTab = $pdostatement->fetch();
        if(!$objectTab) {
            return false;
        }
        else {
            return true;
        }
    }

    public function selectEmail(string $email) :?User
    {
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $sql = "SELECT * FROM souvignetn.users where email = '".$email."'";
        $pdostatement = $pdo->query($sql);
        $objectTab = $pdostatement->fetch();
        if(!$objectTab) {
            return null;
        }
        else {
            return  $this->construire($objectTab);
        }
    }

    public function selectMdpHache(string $mdpHache) :?User
    {
        $pdo = DatabaseConnection::getInstance()::getPdo();

        $sql = "SELECT * FROM souvignetn.users where MDP = '".$mdpHache."'";
        $pdostatement = $pdo->query($sql);
        $objectTab = $pdostatement->fetch();
        if(!$objectTab) {
            return null;
        }
        else {
            return  $this->construire($objectTab);
        }
    }


    public static function peutVoter(string $idUser, string $idQuestion) : bool{
        $sql = 'SELECT peutVoter(:idUser, :idQuestion) FROM DUAL';
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute([
            'idQuestion' => $idQuestion,
            'idUser' => $idUser
        ]);

        $result = $pdoStatement->fetch()[0];

        return $result == 1;
    }

    public static function aDejaCreeProp(string $idUser, int $idQuestion):bool
    {
        $sql = "SELECT * FROM SOUVIGNETN.PROPOSITIONS WHERE IDRESPONSABLE = :IDUSER AND IDQUESTION =".$idQuestion;
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute([
            'idUser' => $idUser
        ]);
        if(!$pdoStatement->fetch())
        {
            return false;
        }
        else
            return true;

    }

    public static function getPropDejaCree(string $idUser, int $idQuestion): ?int
    {
        $sql = "SELECT IDPROPOSITION FROM SOUVIGNETN.PROPOSITIONS WHERE IDRESPONSABLE = :IDUSER AND IDQUESTION =".$idQuestion;
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute([
            'idUser' => $idUser
        ]);
        return $pdoStatement->fetch()[0];
    }

    public static function estOrganisateurSurQuestion(string $idUser, int $idQuestion):bool
    {
        $sql = "SELECT * FROM SOUVIGNETN.QUESTIONS WHERE IDORGANISATEUR = :IDUSER AND IDQUESTION =".$idQuestion;
        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute([
            'idUser' => $idUser
        ]);
        if(!$pdoStatement->fetch())
        {
            return false;
        }
        else
        return true;
    }

    public function selectAllSaufOrganisateur(string $idQuestion): array{
        $sql = 'SELECT * FROM souvignetn.users WHERE "idUser" NOT IN(SELECT IDORGANISATEUR FROM souvignetn.users u JOIN souvignetn.questions q ON q.IDORGANISATEUR = u."idUser" WHERE IDQUESTION ='.$idQuestion.')';

        $pdo = DatabaseConnection::getInstance()::getPdo();
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute();


        $tabRepo = array();
        foreach($pdoStatement as $objetFormatTab){
            $tabRepo[] = $this->construire($objetFormatTab);
        }

        return $tabRepo;
    }


}