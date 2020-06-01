<?php

namespace Tests;

use App\Game;
use App\Player;
use PHPUnit\Framework\TestCase;

/**
 * @property Game game
 */
class GameTurnTest extends TestCase
{
    const PLAYER1 = "player1";
    const PLAYER2 = "player2";
    const PLAYER3 = "player3";

    const CATEGORY_POP = "Pop";
    const CATEGORY_SCIENCE = "Science";
    const CATEGORY_SPORTS = "Sports";
    const CATEGORY_ROCK = "Rock";

    protected function setUp(): void
    {
        parent::setUp();

        $this->game = new Game();
        $this->game->add(self::PLAYER1);
        $this->game->add(self::PLAYER2);
        $this->game->add(self::PLAYER3);
    }

    /** @test */
    public function the_game_starts_with_first_added_player()
    {
        $this->assertEquals(self::PLAYER1, $this->game->currentPlayer()->name);
    }

    /** @test */
    public function the_game_can_process_a_dice_roll()
    {
        $roll = rand(0, 12);

        $this->game->roll($roll);

        $this->assertPlayersTurn(self::PLAYER1, $roll);
    }

    /** @test */
    public function the_players_get_turns_in_the_order_they_were_added()
    {
        $this->nextTurn();

        $this->assertPlayersTurn(self::PLAYER1);

        $this->nextTurn();

        $this->assertPlayersTurn(self::PLAYER2);

        $this->nextTurn();

        $this->assertPlayersTurn(self::PLAYER3);

        $this->nextTurn();

        $this->assertPlayersTurn(self::PLAYER1);
    }

    /** @test */
    public function the_game_puts_players_in_the_penalty_box_if_questions_are_answered_incorrectly()
    {
        $this->nextTurn(false, true);

        $this->assertPlayerSentToPenaltyBox(self::PLAYER1);
    }

    /** @test */
    public function players_get_out_of_penalty_box_when_roll_is_odd()
    {
        $this->nextTurn(false, true);

        $this->assertPlayerSentToPenaltyBox(self::PLAYER1);

        $this->setPlayersTurn(self::PLAYER1);

        $this->game->roll(5);

        $this->assertPlayerGettingOutOfPenaltyBox(self::PLAYER1);
    }

    /** @test */
    public function players_dont_get_out_of_penalty_box_when_roll_is_even()
    {
        $this->nextTurn(false, true);

        $this->assertPlayerSentToPenaltyBox(self::PLAYER1);

        $this->setPlayersTurn(self::PLAYER1);

        $this->game->roll(2);

        $this->assertPlayerNotGettingOutOfPenaltyBox(self::PLAYER1);
    }

    /** @test */
    public function players_move_the_roll_amount_places_forward_when_not_in_penalty_box()
    {
        $this->nextTurn(false, false, 5);

        $this->assertPlayersPositionIs(self::PLAYER1, 5);

        $this->setPlayersTurn(self::PLAYER1);

        $this->nextTurn(false, false, 4);

        $this->assertPlayersPositionIs(self::PLAYER1, 9);
    }

    /** @test */
    public function players_dont_move_when_in_penalty_box()
    {
        $this->nextTurn(false, true, 5);

        $this->assertPlayersPositionIs(self::PLAYER1, 5);

        $this->setPlayersTurn(self::PLAYER1);
        $this->sendPlayerToPenaltyBox(self::PLAYER1);

        $this->nextTurn(false, false, 4);

        $this->assertPlayersPositionIs(self::PLAYER1, 5);
    }

    /** @test */
    public function players_reset_their_position_after_11_steps()
    {
        $this->nextTurn(false, false, 11);

        $this->assertPlayersPositionIs(self::PLAYER1, 11);

        $this->setPlayersTurn(self::PLAYER1);

        $this->nextTurn(false, false, 1);

        $this->assertPlayersPositionIs(self::PLAYER1, 0);
    }

    /** @test */
    public function players_get_gold_coins_for_answering_questions_correctly()
    {
        $this->assertPlayerHasGoldCoins(self::PLAYER1, 0);

        $this->nextTurn(false, false);

        $this->assertPlayerHasGoldCoins(self::PLAYER1, 1);

        $this->setPlayersTurn(self::PLAYER1);
        $this->nextTurn(false, true);

        $this->assertPlayerHasGoldCoins(self::PLAYER1, 1);

        $this->sendPlayerOutsidePenaltyBox(self::PLAYER1);
        $this->setPlayersTurn(self::PLAYER1);
        $this->nextTurn(false, false);

        $this->assertPlayerHasGoldCoins(self::PLAYER1, 2);
    }

    /** @test */
    public function players_win_when_reaching_6_gold_coins()
    {
        $this->setPlayersGoldCoins(self::PLAYER1, 5);

        $this->assertFalse($this->game->didPlayerWin());

        $this->setPlayersGoldCoins(self::PLAYER1, 6);

        $this->assertTrue($this->game->didPlayerWin());
    }

    /** @test */
    public function the_game_sets_the_category_based_on_the_position_of_the_player()
    {
        $pop = [0, 4, 8];
        $science = [1, 5, 9];
        $sports = [2, 6, 10];
        $rock = [3, 7, 11];
        $categories = [
            self::CATEGORY_POP => $pop,
            self::CATEGORY_SCIENCE => $science,
            self::CATEGORY_SPORTS => $sports,
            self::CATEGORY_ROCK => $rock,
        ];

        foreach ($categories as $name => $positions) {
            foreach ($positions as $position) {
                $this->assertCategoryOnPosition(self::PLAYER1, $name, $position);
            }
        }
    }

    private function nextTurn($random = true, $fails = false, $roll = null)
    {
        $roll = $roll ?? rand(0, 12);

        $this->game->roll($roll);

        if (($random && rand(0,9) == 5) || (!$random && $fails)) {
            return $this->game->wrongAnswer();
        } else {
            return $this->game->wasCorrectlyAnswered();
        }
    }

    private function assertPlayersTurn($playerName, $roll = null)
    {
        $this->assertOutputContains("$playerName is the current player", false);

        if ($roll != null) {
            $this->assertOutputContains("They have rolled a $roll");
        }
    }

    private function assertPlayerSentToPenaltyBox($playerName)
    {
        $this->assertOutputContains("$playerName was sent to the penalty box");
    }

    private function assertPlayerGettingOutOfPenaltyBox($playerName)
    {
        $this->assertOutputContains("$playerName is getting out of the penalty box");
    }

    private function assertPlayerNotGettingOutOfPenaltyBox($playerName)
    {
        $this->assertOutputContains("$playerName is not getting out of the penalty box");
    }

    private function assertPlayerHasGoldCoins($playerName, $totalCoins)
    {
        $player = $this->findPlayerByName($playerName);

        $this->assertEquals($totalCoins, $player->totalCoins());
    }

    private function assertCategoryOnPosition($playerName, $category, $position)
    {
        $this->setPlayersPosition($playerName, $position);

        $this->assertEquals($this->game->currentCategory(), $category);
    }

    private function assertPlayersPositionIs($playerName, $position)
    {
        $player = $this->findPlayerByName($playerName);

        $this->assertEquals($position, $player->getPosition());
    }

    private function assertOutputContains($contents, $clear = true)
    {
        $output = ob_get_contents();

        $this->assertStringContainsString($contents, $output);

        if ($clear) ob_clean();
    }

    private function setPlayersTurn($playerName)
    {
        $index = $this->findPlayerIndexByName($playerName);

        $this->game->current = $index;
    }

    private function setPlayersPosition($playerName, $position)
    {
        $player = $this->findPlayerByName($playerName);

        $player->setPosition($position);
    }

    private function setPlayersGoldCoins($playerName, $totalCoins)
    {
        $player = $this->findPlayerByName($playerName);

        $player->setCoins($totalCoins);
    }

    private function sendPlayerToPenaltyBox($playerName)
    {
        $player = $this->findPlayerByName($playerName);

        $player->sendToPenaltyBox();
    }

    private function sendPlayerOutsidePenaltyBox($playerName)
    {
        $player = $this->findPlayerByName($playerName);

        $player->removeFromPenaltyBox();
    }

    /**
     * @param $playerName
     * @return Player|null
     */
    private function findPlayerByName($playerName)
    {
        foreach ($this->game->players as $player) {
            if ($player->name == $playerName) {
                return $player;
            }
        }

        return null;
    }

    /**
     * @param $playerName
     * @return int|null
     */
    private function findPlayerIndexByName($playerName)
    {
        foreach ($this->game->players as $index => $player) {
            if ($player->name == $playerName) {
                return $index;
            }
        }

        return null;
    }
}