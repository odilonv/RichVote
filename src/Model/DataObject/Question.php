<?php

namespace App\Model\DataObject;

use DateTime;

class Question extends AbstractDataObject
{
    private ?string $id;
    private string $idOrganisateur;
    private string $intitule;
    private string $description;
    private array $sections;
    private DateTime $dateCreation;
    private DateTime $dateFermeture;
    private Phase $currentPhase;
    private array $phases;

    public function __construct(
        ?string $id,
        string $idOrganisateur,
        string $intitule,
        ?string $description,
        DateTime $dateCreation,
        DateTime $dateFermeture,
        Phase $currentPhase,
        array $phases=[]
    )
    {
        $this->id = $id;
        $this->idOrganisateur = $idOrganisateur;
        $this->intitule = $intitule;
        if($description == null)
        {
            $description = ' ';
        }
        $this->description = $description;
        $this->dateCreation = $dateCreation;
        $this->dateFermeture = $dateFermeture;
        $this->currentPhase = $currentPhase;
        if(sizeof($phases)==0){
            $this->phases = [$currentPhase];
        }
        else {
            $this->phases = $phases;
        }
    }

    /**
     * @return DateTime
     */
    public function getDateCreation(): DateTime
    {
        return $this->dateCreation;
    }

    /**
     * @return DateTime
     */
    public function getDateFermeture(): DateTime
    {
        return $this->dateFermeture;
    }

    public function dateToString(DateTime $date): string
    {
        return $date->format('d-m-20y');
    }

    /**
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections(array $sections): void
    {
        $this->sections = $sections;
    }

    /**
     * @param string $intitule
     */
    public function setIntitule(string $intitule): void
    {
        $this->intitule = $intitule;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ?string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIntitule(): string
    {
        return $this->intitule;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function getApercuDescription() : string
    {
        $nbmots = 11;
        $txt = strip_tags($this->getDescription());
        $words = explode(' ', $txt, ($nbmots + 1));
        if (count($words) > $nbmots) {
            array_pop($words);
            $fin = '... [Lire la suite]';
        } else{
            $fin = ' ';
    }
        return implode(' ', $words).$fin;
    }

    /**
     * @return Phase
     */
    public function getCurrentPhase(): Phase
    {
        return $this->currentPhase;
    }

    /**
     * @param Phase $currentPhase
     */
    public function setCurrentPhase(Phase $currentPhase): void
    {
        $this->currentPhase = $currentPhase;
    }

    /**
     * @return array
     */
    public function getPhases(): array
    {
        return $this->phases;
    }

    /**
     * @param array $phases
     */
    public function setPhases(array $phases): void
    {
        $this->phases = $phases;
    }

    public function isClosed(): bool{
        return $this->getDateFermeture() < date_create("now");
    }


    public function getIdOrganisateur(): string
    {
        return $this->idOrganisateur;
    }

    public function ajouterSection(Section $section){
        $this->sections[] = $section;
    }

    public function formatTableau(): array
    {
        return array(
            "idQuestionTag" => $this->getId(),
            "idOrganisateurTag" => $this->getIdOrganisateur(),
            "intituleQuestionTag" => $this->getIntitule(),
            "descriptionQuestionTag" => $this->getDescription(),
            "dateCreationTag" => $this->dateCreation->format('d/m/Y'),
            "dateFermetureTag" => $this->dateFermeture->format('d/m/Y'),
            "idCurrentPhaseTag" => $this->currentPhase->getId()
        );
    }
}