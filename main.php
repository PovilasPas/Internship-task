<?php

require_once "autoload.php";

use App\cache\SimpleCache;
use App\hyphenators\RegexHyphenator;
use App\IOUtils;
use App\TaskUtils;

class Main
{
    private const string RULE_FILE = "data.txt";

    public static function main()
    {
        $rules = IOUtils::readFile(self::RULE_FILE);

        $hyphenator = new RegexHyphenator($rules);

        TaskUtils::HyphenateFile("toHyphenate.txt", $hyphenator);
    }
}

Main::main();
