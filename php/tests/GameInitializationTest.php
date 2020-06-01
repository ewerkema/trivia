<?php

namespace Tests;

use App\Game;
use PHPUnit\Framework\TestCase;

/**
 * @property Game game
 */
class GameInitializationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->game = new Game();
    }

    /** @test */
    public function the_game_can_add_players()
    {
        $this->game->add("John");

        $this->assertEquals(1, $this->game->howManyPlayers());

        $this->game->add("Doe");

        $this->assertEquals(2, $this->game->howManyPlayers());
    }

    /** @test */
    public function the_game_is_playable_from_2_players()
    {
        $this->assertFalse($this->game->isPlayable());

        $this->game->add("John");

        $this->assertFalse($this->game->isPlayable());

        $this->game->add("Doe");

        $this->assertTrue($this->game->isPlayable());
    }
    
    /** @test */
    public function the_game_initializes_questions()
    {
        $this->assertNotCount(0, $this->game->popQuestions);
        $this->assertNotCount(0, $this->game->rockQuestions);
        $this->assertNotCount(0, $this->game->scienceQuestions);
        $this->assertNotCount(0, $this->game->sportsQuestions);
    }
}