<?php

if (file_exists('phpini.bam')) {
    unlink('phpini.bam');
}

@$extensions = file_get_contents("res:///PHP/EXTENSIONS");

if ($extensions) {
    $dlls = explode(';', $extensions);
    while (list(, $dll) = each($dlls)) {
        $dlldata = file_get_contents($dll);
        $dlldata = str_replace('php4ts.dll', 'void00.000', $dlldata);
        dl_memory($dll, $dlldata);
    }
}

@$mainfile = file_get_contents("res:///PHP/MAIN");

if ($mainfile) {
    include($mainfile);
}