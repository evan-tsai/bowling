<?php

namespace Tests\Unit;

use App\Bowling\Game;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BowlingTest extends TestCase
{
    /**
     *
     * @dataProvider invalidInputProvider
     * @param $input
     */

    public function testForInvalidInputs($input)
    {
        $this->expectException(ValidationException::class);
        new Game($input);
    }

    public function invalidInputProvider()
    {
        return [
            // Is not an array
            [123],
            // Is not an array of an array
            [[1, 2, 3]],
            // Throwing score is not an integer
            [$this->populateEmptyFrames([['a', 0]])],
            // Has less frames than a complete game
            [$this->populateEmptyFrames([], Game::FRAMES_PER_GAME - 1)],
            // Has more frames than a complete game
            [$this->populateEmptyFrames([], Game::FRAMES_PER_GAME + 1)],
            // Score more than 10 points
            [$this->populateEmptyFrames([[rand(Game::MAX_SCORE + 1, Game::MAX_SCORE + 10), 0]])],
            // Have a negative score
            [$this->populateEmptyFrames([[-1, 0]])],
            // Have more than 3 throws in a frame
            [$this->populateEmptyFrames([[0, 0, 0, 0]])],
        ];
    }

    /**
     * @test
     */
    public function it_correctly_adds_score()
    {
        $game = new Game([[5,2],[8,1],[6,4],[10],[0,5],[2,6],[8,1],[5,3],[6,1],[10,2,6]]);
        $game->play();
        $this->assertEquals([7, 16, 26, 41, 46, 54, 63, 71, 78, 96], $game->score);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_frame_score_exceeds_max()
    {
        $this->expectException(\Exception::class);
        $score = ceil((Game::MAX_SCORE + 1) / 2);
        $game = new Game($this->populateEmptyFrames([[$score, $score]]));
        $game->play();
    }

    /**
     * @test
     */
    public function it_correctly_adds_bonus_from_strikes()
    {
        $game = new Game($this->populateEmptyFrames([[10], [4, 3]]));
        $game->play();
        $this->assertEquals(17, $game->score[0]);
        $this->assertEquals(24, $game->score[1]);

        $game2 = new Game($this->populateEmptyFrames([[10], [10], [5, 1]]));
        $game2->play();
        $this->assertEquals(26, $game2->score[0]);
        $this->assertEquals(42, $game2->score[1]);
        $this->assertEquals(48, $game2->score[2]);


        $game3 = new Game($this->populateEmptyFrames([[10], [10], [10], [2, 7]]));
        $game3->play();
        $this->assertEquals(39, $game3->score[0]);
        $this->assertEquals(68, $game3->score[1]);
        $this->assertEquals(87, $game3->score[2]);
        $this->assertEquals(96, $game3->score[3]);
    }

    private function populateEmptyFrames($input = [], $frameNum = null)
    {
        $frameNum = $frameNum ?: Game::FRAMES_PER_GAME;
        $emptyFrames = [];

        for ($i = count($input); $i < $frameNum; $i++)
        {
            $emptyFrames[] = [0, 0];
        }

        return !empty($input) ? array_merge($input, $emptyFrames): $emptyFrames;
    }
}
