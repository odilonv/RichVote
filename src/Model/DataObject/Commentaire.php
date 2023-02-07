<?php

namespace App\Model\DataObject;

use DateTime;

class Commentaire extends AbstractDataObject
{

    private int $IDPROPOSITION;
    private string $IDUSER;
    private string $TEXTE;
    private string $DATECOMMENTAIRE;
    private int $NBLIKE;
    private int $IDCOMMENTAIRE;

    /**
     * @param string $IDPROPOSITION
     * @param string $IDUSER
     * @param string $TEXTE
     * @param string $DATECOMMENTAIRE
     * @param int $NBLIKE
     * @param int $IDCOMMENTAIRE
     */
    public function __construct(string $IDPROPOSITION, string $IDUSER, string $TEXTE, string $DATECOMMENTAIRE,int $NBLIKE, int $IDCOMMENTAIRE)
    {
        $this->IDPROPOSITION = $IDPROPOSITION;
        $this->IDUSER = $IDUSER;
        $this->TEXTE = $TEXTE;
        $this->DATECOMMENTAIRE = $DATECOMMENTAIRE;
        $this->NBLIKE = $NBLIKE;
        $this->IDCOMMENTAIRE = $IDCOMMENTAIRE;
    }


    /**
     * @return string
     */
    public function getIDPROPOSITION(): string
    {
        return $this->IDPROPOSITION;
    }





    /**
     * @return string
     */
    public function getIDUSER(): string
    {
        return $this->IDUSER;
    }

    /**
     * @return string
     */
    public function getTEXTE(): string
    {
        return $this->TEXTE;
    }

    /**
     * @return string
     */
    public function getDATECOMMENTAIRE(): string
    {
        return $this->DATECOMMENTAIRE;
    }

    /**
     * @return int
     */
    public function getNBLIKE(): int
    {
        return $this->NBLIKE;
    }


    public function formatTableau(): array
    {
        return array(
            'IDPROPOSITION' => $this->getIDPROPOSITION(),
            'IDUSER' => $this->getIDUSER(),
            'TEXTE' => $this->getTEXTE(),
            'DATECOMMENTAIRE' => $this->getDATECOMMENTAIRE()

        );
    }

    /**
     * @return int
     */
    public function getIDCOMMENTAIRE(): int
    {
        if( isset($this->IDCOMMENTAIRE))
        {
            return $this->IDCOMMENTAIRE;
        }
        return 0;

    }



    public function ancienneteArrondie():string
    {
        $date = date("'d/m/y G:i:s'");
        //$dateCom =strtotime($this->getDATECOMMENTAIRE());
        return DateTime::createFromFormat('d/m/y G:i:s',$this->getDATECOMMENTAIRE());
    }


}