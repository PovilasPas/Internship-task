<?php

declare(strict_types=1);

namespace App\CLI;

class Menu
{
    public function __construct(private readonly array $options)
    {

    }

    public function show(): void
    {
        $message = 'Choose an option: ';
        $this->render();
        $selection = trim(readline($message));
        while (!array_key_exists($selection, $this->options)) {
            $this->clear(1);
            $this->render();
            $selection = trim(readline($message));
        }
        $this->options[$selection]->getAction()->execute();
    }

    private function render(): void
    {
        foreach ($this->options as $index => $option) {
            echo $index . '. ' . $option->getName() . PHP_EOL;
        }
    }

    private function clear(int $extra = 0): void
    {
        echo str_repeat("\033[F\033[K", count($this->options) + $extra);
    }
}
