<?php

namespace App\Model\DataObject;

use DateTime;

class Phase extends AbstractDataObject
{
    private ?string $id;
    private string $type;
    private DateTime $dateDebut;
    private DateTime $dateFin;
    private ?int $nbDePlaces;

    public function __construct(?string $id, string $type, DateTime $dateDebut, DateTime $dateFin, ?int $nbDePlaces)
    {
        $this->id = $id;
        $this->type = $type;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->nbDePlaces = $nbDePlaces;
    }

    /**
     * @return int|null
     */
    public function getNbDePlaces(): ?int
    {
        return $this->nbDePlaces;
    }

    /**
     * @param int|null $nbDePlaces
     */
    public function setNbDePlaces(?int $nbDePlaces): void
    {
        $this->nbDePlaces = $nbDePlaces;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return DateTime
     */
    public function getDateDebut(): DateTime
    {
        return $this->dateDebut;
    }

    /**
     * @return DateTime
     */
    public function getDateFin(): DateTime
    {
        return $this->dateFin;
    }

    public function formatTableau(): array
    {
        return ['idPhaseTag' => $this->id,
            'dateDebutTag' => $this->dateDebut->format('d/m/yy'),
            'dateFinTag' => $this->dateFin->format('d/m/yy'),
            'typePhaseTag' => $this->type,
            'nbDePlacesTag' => $this->nbDePlaces];
    }

    public function exist():bool{
        return $this->id == NULL;
    }


    public function getId(): ?string
    {
        return $this->id;
    }

    public static function emptyPhase(): Phase
    {
        return new Phase(null, 'consultation', date_create(), date_create(), null);
    }

    public function isEmpty():bool{
        return $this->id==null;
    }

    public function estFinie():bool{
        return $this->getDateFin()<date_create("now");
    }

    public function estCommence():bool
    {
        return ($this->getDateDebut()<=date_create("now")
            &&  $this->getDateFin()>=date_create("now"));
    }


}