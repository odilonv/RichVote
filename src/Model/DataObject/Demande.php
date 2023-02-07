<?php

namespace App\Model\DataObject;

class Demande
{
    private string $role;
    private Question $question;
    private User|Groupe $demandeur;
    private ?Proposition $proposition;

    public function __construct(
        string $type,
        Question $question,
        User|Groupe $user,
        ?Proposition $proposition=null
    )
{
    $this->role = $type;
    $this->question=$question;
    $this->demandeur=$user;
    $this->proposition=$proposition;
}

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return User|Groupe
     */
    public function getDemandeur(): User|Groupe
    {
        return $this->demandeur;
    }

    /**
     * @return Proposition|null
     */
    public function getProposition(): ?Proposition
    {
        return $this->proposition;
    }

}