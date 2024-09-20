<?php

namespace src\hyphenators;

interface IHyphenator {
    public function hyphenate(string $word) : string;
}