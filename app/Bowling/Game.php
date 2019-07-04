<?php


namespace App\Bowling;



use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Game
{
    const FRAMES_PER_GAME = 10;

    /**
     * @var array
     */
    private $frames;

    public function __construct($frames)
    {
        $this->frames = $this->validate($frames);
    }

    public function score()
    {
        return $this->frames;
    }

    protected function validate($data)
    {
        $validator = Validator::make(['data' => $data], [
            'data' => 'required|array|size:' . self::FRAMES_PER_GAME,
            'data.*' => 'array|min:1|max:3',
            'data.*.*' => 'integer|min:0|max:10',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $data;
    }
}
