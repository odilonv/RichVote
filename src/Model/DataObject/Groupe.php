<?php

namespace App\Model\DataObject;

class Groupe extends AbstractDataObject
{
    private string $nomGroupe;
    private ?string $idResponsable;
    private array $idMembres;

    public function formatTableau(): array
    {
        return ['nomGroupeTag' => $this->nomGroupe,
            'idUserResponsableTag' => $this->idResponsable];
    }

    public function getId(): ?string
    {
        return $this->nomGroupe;
    }

    /**
     * @return string|null
     */
    public function getIdResponsable(): ?string
    {
        return $this->idResponsable;
    }

    public function getIdMembres():array{
        return $this->idMembres;
    }

    public function __construct(
        string $nomGroupe,
        ?string $idResponsable,
        ?array $idMembres=[]
    )
    {
        $this->nomGroupe = $nomGroupe;
        $this->idResponsable = $idResponsable;
        $this->idMembres = $idMembres;
    }

    public function addUser(string $idUser){
        if(!isset($this->idMembres[$idUser])){
            $this->idMembres[] = $idUser;
        }
    }

}