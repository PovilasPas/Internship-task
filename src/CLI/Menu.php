<?php

declare(strict_types=1);

namespace App\CLI;

use App\Executor\ExecutorInterface;

class Menu
{
    public function __construct(private array $options)
    {

    }

    public function getSelection(): ExecutorInterface
    {
        $message = 'Choose an option: ';
        $this->render();
        $selection = trim(readline($message));
        while (!array_key_exists($selection, $this->options)) {
            $this->render();
            $selection = trim(readline($message));
        }
        return $this->options[$selection]->getAction();
    }

    private function render(): void
    {
        foreach ($this->options as $idx => $option) {
            echo $idx . '. ' . $option->getName() . PHP_EOL;
        }
    }
}
