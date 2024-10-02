<?php

declare(strict_types=1);

namespace App\CLI;

use App\Executor\ExecutorInterface;

class Menu
{
    public function __construct(private array $options)
    {

    }

    public function show(): void
    {
        $message = 'Choose an option: ';
        $this->render();
        $selection = trim(readline($message));
        while (!array_key_exists($selection, $this->options)) {
            $this->render();
            $selection = trim(readline($message));
        }
        $this->options[$selection]->getAction()->execute();
    }

    private function render(): void
    {
        foreach ($this->options as $idx => $option) {
            echo $idx . '. ' . $option->getName() . PHP_EOL;
        }
    }
}
