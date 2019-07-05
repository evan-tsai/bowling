<?php


namespace App\Bowling;



use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Game
{
    const FRAMES_PER_GAME = 10;
    const MAX_SCORE = 10;

    /**
     * @var array
     */
    protected $frames;

    /**
     * @var array
     */
    public $score;

    public function __construct($frames)
    {
        $this->frames = $this->validate($frames);
    }

    public function play()
    {
        $score = 0;

        foreach ($this->frames as $key => $frame) {
            if ($this->isStrike($frame)) {
                $score += $this->addBonus($key);
            } else {
                $score += $this->sumOfPins($frame);
            }
            $this->score[] = $score;
        }
    }

    protected function isStrike($frame)
    {
        return count($frame) === 1 && $frame[0] === self::MAX_SCORE;
    }

    protected function addBonus($key)
    {
        $nextKey = $key + 1;

        if ($this->isStrike($this->frames[$nextKey])) {
            $bonus = $this->addBonus($nextKey);
        } else {
            $bonus = $this->sumOfPins($this->frames[$nextKey]);
        }

        return self::MAX_SCORE + $bonus;
    }

    protected function sumOfPins($frame)
    {
        $sum = array_sum($frame);

        if (count($frame) === 2 && $sum > self::MAX_SCORE) {
            throw new \Exception('Sum can not exceed ' . self::MAX_SCORE);
        }

        return $sum;
    }

    protected function validate($data)
    {
        $validator = Validator::make(['data' => $data], [
            'data' => 'required|array|size:' . self::FRAMES_PER_GAME,
            'data.*' => 'array|min:1|max:3',
            'data.*.*' => 'integer|min:0|max:' . self::MAX_SCORE,
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $data;
    }
}
