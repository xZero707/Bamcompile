<?php

if (file_exists('phpini.bam')) {
    unlink('phpini.bam');
}

@$extensions = file_get_contents("res:///PHP/EXTENSIONS");

if ($extensions) {
    $extensionFiles = explode(';', $extensions);

    if ( ! empty($extensionFiles)) {
        foreach ($extensionFiles as $key => $filename) {
            $dllData = file_get_contents($filename);
            $dllData = str_replace('php4ts.dll', 'void00.000', $dllData);
            dl_memory($filename, $dllData);
        }
    }
}

@$mainFile = file_get_contents("res:///PHP/MAIN");

if ($mainFile) {
    include($mainFile);
}