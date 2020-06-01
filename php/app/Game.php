<?php

namespace App;

class Game
{
    var $players;
    var $places;
    var $purses;
    var $inPenaltyBox;

    var $popQuestions;
    var $scienceQuestions;
    var $sportsQuestions;
    var $rockQuestions;

    var $currentPlayer = 0;
    var $isGettingOutOfPenaltyBox;

    function __construct()
    {
        $this->players = array();
        $this->places = array(0);
        $this->purses = array(0);
        $this->inPenaltyBox = array(0);

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
        return ($this->howManyPlayers() >= 2);
    }

    function add($playerName)
    {
        array_push($this->players, $playerName);
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;

        self::echoln($playerName . " was added");
        self::echoln("They are player number " . count($this->players));
        return true;
    }

    function howManyPlayers()
    {
        return count($this->players);
    }

    function roll($roll)
    {
        self::echoln($this->players[$this->currentPlayer] . " is the current player");
        self::echoln("They have rolled a " . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;

                self::echoln($this->players[$this->currentPlayer] . " is getting out of the penalty box");
            } else {
                self::echoln($this->players[$this->currentPlayer] . " is not getting out of the penalty box");

                $this->isGettingOutOfPenaltyBox = false;

                return;
            }

        }

        $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
        if ($this->places[$this->currentPlayer] > 11) $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;

        self::echoln($this->players[$this->currentPlayer]
            . "'s new location is "
            . $this->places[$this->currentPlayer]);
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
        if ($this->places[$this->currentPlayer] == 0) return "Pop";
        if ($this->places[$this->currentPlayer] == 4) return "Pop";
        if ($this->places[$this->currentPlayer] == 8) return "Pop";
        if ($this->places[$this->currentPlayer] == 1) return "Science";
        if ($this->places[$this->currentPlayer] == 5) return "Science";
        if ($this->places[$this->currentPlayer] == 9) return "Science";
        if ($this->places[$this->currentPlayer] == 2) return "Sports";
        if ($this->places[$this->currentPlayer] == 6) return "Sports";
        if ($this->places[$this->currentPlayer] == 10) return "Sports";
        return "Rock";
    }

    function wasCorrectlyAnswered()
    {
        if ($this->inPenaltyBox[$this->currentPlayer] && !$this->isGettingOutOfPenaltyBox) {
            $this->nextPlayer();
            return true;
        }

        self::echoln("Answer was correct!!!!");
        $this->purses[$this->currentPlayer]++;
        self::echoln($this->players[$this->currentPlayer]
            . " now has "
            . $this->purses[$this->currentPlayer]
            . " Gold Coins.");

        $notAWinner = !$this->didPlayerWin();
        $this->nextPlayer();

        return $notAWinner;
    }

    function wrongAnswer()
    {
        self::echoln("Question was incorrectly answered");
        self::echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->nextPlayer();
        return true;
    }

    private function nextPlayer(): void
    {
        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
    }

    function didPlayerWin()
    {
        return $this->purses[$this->currentPlayer] == 6;
    }

    public static function echoln($string)
    {
        echo $string . "\n";
    }
}
