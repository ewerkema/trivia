<?php

namespace App;

class Player
{
    public $name;
    private $position = 0;
    private $purse = 0;
    private $inPenaltyBox = false;

    function __construct($name) {
        $this->name = $name;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setCoins($coins)
    {
        $this->purse = $coins;
    }

    public function totalCoins()
    {
        return $this->purse;
    }

    public function inPenaltyBox()
    {
        return $this->inPenaltyBox;
    }

    public function movePlaces($places)
    {
        $this->position = ($this->position + $places) % 12;
    }

    public function incrementGoldCoins()
    {
        $this->purse++;
    }

    public function sendToPenaltyBox()
    {
        $this->inPenaltyBox = true;
    }

    public function removeFromPenaltyBox()
    {
        $this->inPenaltyBox = false;
    }
}