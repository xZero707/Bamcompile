<?php

@$iniFile = file_get_contents("res:///PHP/PHP.INI");

if ($iniFile) {
    $f = fopen("phpini.bam", "wb");
    fwrite($f, $iniFile);
    fclose($f);
}