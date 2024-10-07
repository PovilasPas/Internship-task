<?php

declare(strict_types=1);

namespace App\Web\Response;

class JsonResponse extends Response
{

    public function __construct(array $headers, array $body, int $code = 200)
    {
        parent::__construct($headers, $body, $code);
        $this->headers[] = 'Content-type: application/json';
    }

    public function render(): void
    {
        foreach ($this->headers as $header) {
            header($header);
        }
        if (count($this->body) > 0) {
            echo json_encode($this->body);
        }
        http_response_code($this->code);
    }
}
