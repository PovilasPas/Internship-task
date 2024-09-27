<?php

namespace App;

class Timer
{
    private float $start;
    private float $stop;

    public function startTimer(): void
    {
        $this->start = microtime(true);
    }

    public function stopTimer(): void
    {
        $this->stop = microtime(true);
    }

    public function getElapsed(): float
    {
        return $this->stop - $this->start;
    }
}