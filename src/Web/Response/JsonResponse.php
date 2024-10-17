<?php

declare(strict_types=1);

namespace App\Web\Response;

class JsonResponse extends Response
{

    public function __construct(array $headers = [], ?array $body = null, StatusCode $code = StatusCode::OK)
    {
        parent::__construct($headers, $body, $code);
        $this->headers[] = 'Content-type: application/json';
    }

    public function render(): void
    {
        foreach ($this->headers as $header) {
            header($header);
        }

        http_response_code($this->code->value);

        if ($this->body !== null) {
            echo json_encode($this->body);
        }
    }
}
