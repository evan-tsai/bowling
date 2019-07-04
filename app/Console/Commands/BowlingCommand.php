<?php

namespace App\Console\Commands;

use App\Bowling\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class BowlingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bowl:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs bowling command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $frames = json_decode($this->ask('Please enter array of frames.'), true);
        try {
            $game = new Game($frames);

            $this->info(json_encode($game->score()));
        } catch (ValidationException $exception) {
            foreach (Arr::flatten($exception->errors()) as $error) {
                $this->error($error);
            }
        }
    }
}
