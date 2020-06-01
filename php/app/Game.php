<?php

namespace App;

class Game
{
    var $players;

    var $popQuestions;
    var $scienceQuestions;
    var $sportsQuestions;
    var $rockQuestions;

    var $current = 0;

    function __construct()
    {
        $this->players = [];

        $this->popQuestions = array();
        $this->scienceQuestions = array();
        $this->sportsQuestions = array();
        $this->rockQuestions = array();

        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }

    function createRockQuestion($index)
    {
        return "Rock Question " . $index;
    }

    function isPlayable()
    {
        return ($this->totalPlayers() >= 2);
    }

    function add($playerName)
    {
        $player = new Player($playerName);
        array_push($this->players, $player);

        self::echoln($playerName . " was added");
        self::echoln("They are " . $this->totalPlayers() . " players");
        return true;
    }

    function totalPlayers()
    {
        return count($this->players);
    }

    function roll($roll)
    {
        $player = $this->currentPlayer();

        self::echoln($player->name . " is the current player");
        self::echoln("They have rolled a " . $roll);

        if ($player->inPenaltyBox()) {
            if ($roll % 2 != 0) {
                self::echoln($player->name . " is getting out of the penalty box");

                $player->removeFromPenaltyBox();
            } else {
                self::echoln($player->name . " is not getting out of the penalty box");

                return;
            }

        }

        $player->movePlaces($roll);

        self::echoln($player->name
            . "'s new location is "
            . $player->getPosition());
        self::echoln("The category is " . $this->currentCategory());
        $this->askQuestion();
    }

    function askQuestion()
    {
        if ($this->currentCategory() == "Pop")
            self::echoln(array_shift($this->popQuestions));
        if ($this->currentCategory() == "Science")
            self::echoln(array_shift($this->scienceQuestions));
        if ($this->currentCategory() == "Sports")
            self::echoln(array_shift($this->sportsQuestions));
        if ($this->currentCategory() == "Rock")
            self::echoln(array_shift($this->rockQuestions));
    }


    function currentCategory()
    {
        $player = $this->currentPlayer();
        if ($player->getPosition() == 0) return "Pop";
        if ($player->getPosition() == 4) return "Pop";
        if ($player->getPosition() == 8) return "Pop";
        if ($player->getPosition() == 1) return "Science";
        if ($player->getPosition() == 5) return "Science";
        if ($player->getPosition() == 9) return "Science";
        if ($player->getPosition() == 2) return "Sports";
        if ($player->getPosition() == 6) return "Sports";
        if ($player->getPosition() == 10) return "Sports";
        return "Rock";
    }

    /**
     * @return Player
     */
    public function currentPlayer(): Player
    {
        return $this->players[$this->current];
    }

    function wasCorrectlyAnswered()
    {
        $player = $this->currentPlayer();
        if ($player->inPenaltyBox()) {
            $this->nextPlayer();
            return true;
        }

        self::echoln("Answer was correct!!!!");
        $player->incrementGoldCoins();
        self::echoln($player->name
            . " now has "
            . $player->totalCoins()
            . " Gold Coins.");

        $notAWinner = !$this->didPlayerWin();
        $this->nextPlayer();

        return $notAWinner;
    }

    function wrongAnswer()
    {
        $player = $this->currentPlayer();
        self::echoln("Question was incorrectly answered");
        self::echoln($player->name . " was sent to the penalty box");
        $player->sendToPenaltyBox();

        $this->nextPlayer();
        return true;
    }

    private function nextPlayer(): void
    {
        $this->current = ($this->current + 1) % $this->totalPlayers();
    }

    function didPlayerWin()
    {
        return $this->currentPlayer()->totalCoins() >= 6;
    }

    public static function echoln($string)
    {
        echo $string . "\n";
    }
}
