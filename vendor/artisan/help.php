<?php
function logHelp($type, $msg)
{
    $padding = str_repeat(' ', max(0, 20 - strlen($type)));
    echo "\t\e[1;33m" . $type . "\e[0m" . $padding . ": \e[0;36m" . $msg . "\e[0m\n";
}

echo "\e[1;34mMake (Tạo)\e[0m\n";

logHelp("make:controller", "Tạo controller");
logHelp("make:model", "Tạo model");
logHelp("make:middleware", "Tạo middleware");
logHelp("make:trait", "Tạo trait");
