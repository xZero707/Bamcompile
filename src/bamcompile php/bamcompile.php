<?php

function get_all_files($dir, $subpath = '')
{
    global $includefiles;
    global $sourcepath;
    global $destpath;
    $d = dir($dir);
    while (false !== ($entry = $d->read())) {
        if ( ! ($entry == '.') & ! ($entry == '..')) {
            if (is_dir($dir . '/' . $entry)) {
                get_all_files($dir . '/' . $entry, $subpath . $entry . '/');
            } else {
                $includefiles[] = array($sourcepath . $subpath . $entry, $destpath . $subpath . $entry);
            }
        }
    }
}

print "\nBambalam PHP EXE Compiler/Embedder 1.21\n\n";

if ($argc == 1) {
    print "Converts PHP applications to standalone .exe
Created by Anders Hammar (c) 2006 Bambalam - www.bambalam.se/bamcompile

Usage: 
 bamcompile [-options] infile.php [outfile.exe]
 bamcompile [-options] project_directory mainfile.php [outfile.exe]
 bamcompile projectfile.bcp
 
Options:
 -w  Hide console window for windowed applications
 -c  Compress output exe (using UPX)
 -d  Do not encode PHP files
 -e:extension.dll Embed and use PHP extension
 -i:icon.ico Add icon to exe
";
    exit;
}

$option_windowed = false;
$option_compress = false;
$option_noencode = false;
$option_minimal  = false;

$mainfile     = "";
$includefiles = array();
$outfile      = "";
$projectdir   = "";
$extensions   = array();
$projectfile  = "";
$iconfile     = "";

// process arguments

while (list($nr, $val) = each($argv)) {
    if ($nr > 0) {
        $val = strtolower($val);
        if ($val == '-w') {
            $option_windowed = true;
        } else if ($val == '-c') {
            $option_compress = true;
        } else if ($val == '-d') {
            $option_noencode = true;
        } else if (strpos($val, '.php') > -1) {
            $mainfile = $val;
        } else if (strpos($val, '.exe') > -1) {
            $outfile = $val;
        } else if (strpos($val, '.bcp') > -1) {
            $projectfile = $val;
        } else if (substr($val, 0, 3) == '-e:') {
            $extensions[] = substr($val, 3);
        } else if (substr($val, 0, 3) == '-i:') {
            $iconfile = substr($val, 3);
        } else if (is_dir($val)) {
            $projectdir = $val;
        }
    }
}

$sourcepath = "";
$destpath   = "";

if ($projectfile) {
    if ( ! file_exists($projectfile)) {
        print "Problem: Project file $projectfile does not exist!\n";
        exit;
    }
    $projectdir = "";
    print "Using project file: $projectfile\n";
    $projectdata = file($projectfile);
    while (list(, $row) = each($projectdata)) {
        $row = strtolower(trim($row));
        if (substr($row, 0, 5) == 'embed') {
            $embedpath = trim(substr($row, 5));
            if (is_dir($embedpath)) {
                $sourcepath = str_replace('/', '\\', $embedpath);
                if (substr($sourcepath, strlen($sourcepath) - 1, 1) != '\\') {
                    $sourcepath .= '\\';
                }
                get_all_files($embedpath);
            } else {
                if (file_exists($embedpath)) {
                    $includefiles[] = array($embedpath, $destpath . basename($embedpath));
                }
                $filter = basename($embedpath);
                if (substr($filter, 0, 2) == '*.') {
                    $filterdir = dirname($embedpath);
                    $filterext = substr($filter, 2);
                    $d         = dir($filterdir);
                    while (false !== ($entry = $d->read())) {
                        if ( ! ($entry == '.') & ! ($entry == '..') & (strpos($entry, $filterext) > -1)) {
                            $includefiles[] = array($filterdir . '/' . $entry, $destpath . $entry);
                        }
                    }
                }
            }
        }
        if (substr($row, 0, 11) == 'destination') {
            $destpath = trim(substr($row, 11));
            $destpath = str_replace('\\', '/', $destpath);
            if (substr($destpath, strlen($destpath) - 1, 1) != '/') {
                $destpath .= '/';
            }
            if ($destpath == '/') {
                $destpath = "";
            }
        }
        if (substr($row, 0, 9) == 'extension') {
            $extensions[] = trim(substr($row, 9));
        }
        if (substr($row, 0, 8) == 'mainfile') {
            $mainfile = trim(substr($row, 8));
        }
        if (substr($row, 0, 7) == 'outfile') {
            $outfile = trim(substr($row, 7));
        }
        if (substr($row, 0, 4) == 'icon') {
            $iconfile = trim(substr($row, 4));
        }
        if (substr($row, 0, 8) == 'compress') {
            $option_compress = true;
        }
        if (substr($row, 0, 10) == 'dontencode') {
            $option_noencode = true;
        }
        if (substr($row, 0, 8) == 'windowed') {
            $option_windowed = true;
        }
    }
}

if ($mainfile == "" & is_dir($projectdir)) {
    print "Problem: You must specify a main PHP file in your project directory!\n";
    exit;
}

if ($mainfile == "") {
    print "Problem: You must at least specify a PHP file to compile!\n";
    exit;
}

if ($mainfile & $projectfile) {
    reset($includefiles);
    $found = false;
    while (list(, $file) = each($includefiles)) {
        if (basename($file[0]) == $mainfile) {
            $found = true;
        }
    }
    if ( ! $found) {
        print "Problem: Main file $mainfile not found!\n";
        exit;
    }
    reset($includefiles);
}

if ($outfile == "") {
    $outfile = substr($mainfile, 0, strpos($mainfile, '.php')) . '.exe';
}

if ($option_windowed) {
    print "Windowed application\n";
}
if ($option_compress) {
    print "Compress\n";
}
if ($option_noencode) {
    print "Do not encode\n";
}
if ($mainfile) {
    print "Mainfile: $mainfile\n";
}
if ($outfile) {
    print "Outfile: $outfile\n";
}
if ($iconfile) {
    print "Icon: $iconfile\n";
}
if ($projectdir) {
    print "Project dir: $projectdir\n";
}

if ($projectdir) {
    if ( ! is_dir($projectdir)) {
        print "Problem: Project directory not found\n";
        exit;
    }
    if ( ! file_exists($projectdir . '/' . $mainfile)) {
        print "Problem: Main php not found at $projectdir/$mainfile\n";
        exit;
    }
    $sourcepath = str_replace('/', '\\', $projectdir);
    if (substr($sourcepath, strlen($sourcepath) - 1, 1) != '\\') {
        $sourcepath .= '\\';
    }
    get_all_files($projectdir);
} else {
    if ($projectfile == "") {
        if ( ! file_exists($mainfile)) {
            print "Problem: Main file $mainfile not found!\n";
            exit;
        }
        $includefiles[] = array($mainfile, $mainfile);
    }
}

// check for embedded extensions, they don't need to be embedded twice
reset($includefiles);
while (list($nr, $file) = each($includefiles)) {
    reset($extensions);
    while (list(, $ext) = each($extensions)) {
        if (basename($file[0]) == basename($ext)) {
            $includefiles[$nr] = array("", "");
        }
    }
}
reset($includefiles);
reset($extensions);

// check for embedded icon
if ($iconfile) {
    reset($includefiles);
    while (list($nr, $file) = each($includefiles)) {
        if (basename($file[0]) == basename($iconfile)) {
            $includefiles[$nr] = array("", "");
        }
    }
    reset($includefiles);
}

// create, compile and embed!

$stub = file_get_contents("stub.exe");

// make windowed application if wanted

if ($option_windowed) {
    $stub[372] = chr(2);
}

@$f = fopen($outfile, "w");
if ( ! $f) {
    print "Problem: Could not write to $outfile - maybe it's running?\n";
    exit;
}
fwrite($f, $stub);
fclose($f);

print "\n";

while (list(, $file) = each($includefiles)) {
    $sourcefile = $file[0];
    $file       = $file[1];
    if ($file != "") {
        $embedfile = str_replace('/', '\\', $file);

        if (strpos($file, '.php') > -1 & ! $option_noencode) {
            print "Encoding and embedding $file\n";
            $encdata = mmcache_encode($sourcefile);
            $data    = "<?mmcache_load(\"$encdata\");?>";
            res_set($outfile, "PHP", $embedfile, $data);
        } else {
            print "Embedding $file\n";
            $data = file_get_contents($sourcefile);
            res_set($outfile, "PHP", $embedfile, $data);
        }
    }
}

res_set($outfile, "PHP", "MAIN", $mainfile);

$extension_loadlist = array();

if (count($extensions) > 0) {
    while (list(, $file) = each($extensions)) {
        $extension_file = $file;
        if ( ! file_exists($extension_file)) {
            $extension_file = $path . $file;
        }
        if ( ! file_exists($extension_file)) {
            print "Extension $file not found\n";
        } else {
            print "Embedding " . basename($file) . " and adding it to extension loader\n";
            $embedfile = basename($extension_file);
            $data      = file_get_contents($extension_file);
            res_set($outfile, "PHP", $embedfile, $data);
            $extension_loadlist[] = $embedfile;
        }
    }
}

if (count($extensions) > 0) {
    res_set($outfile, "PHP", "EXTENSIONS", implode(';', $extension_loadlist));
}

if (file_exists($iconfile)) {
    $icondata   = file_get_contents($iconfile);
    $offset     = 4;
    $icon_count = unpack("S", substr($icondata, $offset, 2));
    $icon_count = $icon_count[1];
    $offset     += 2;
    $icons      = array();
    for ($i = 0; $i < $icon_count; $i++) {
        $icon = array();

        $val         = unpack("C", substr($icondata, $offset, 1));
        $icon[width] = $val[1];
        $offset++;

        $val          = unpack("C", substr($icondata, $offset, 1));
        $icon[height] = $val[1];
        $offset++;

        $val          = unpack("C", substr($icondata, $offset, 1));
        $icon[colors] = $val[1];
        $offset       += 2;

        $val          = unpack("S", substr($icondata, $offset, 2));
        $icon[planes] = $val[1];
        $offset       += 2;

        $val            = unpack("S", substr($icondata, $offset, 2));
        $icon[bitcount] = $val[1];
        $offset         += 2;

        $val        = unpack("L", substr($icondata, $offset, 4));
        $icon[size] = $val[1];
        $offset     += 4;

        $val          = unpack("L", substr($icondata, $offset, 4));
        $icon[offset] = $val[1];
        $offset       += 4;

        $icons[] = $icon;
    }
    for ($i = 0; $i < count($icons); $i++) {
        $data            = substr($icondata, $icons[$i][offset], $icons[$i][size]);
        $icons[$i][data] = $data;
    }

    $icon_group = "";
    $icon_group .= pack("S", 0);
    $icon_group .= pack("S", 1);
    $icon_group .= pack("S", $icon_count);
    for ($i = 0; $i < $icon_count; $i++) {
        $icon_group .= pack("C", $icons[$i][width]);
        $icon_group .= pack("C", $icons[$i][height]);
        $icon_group .= pack("C", $icons[$i][colors]);
        $icon_group .= pack("C", 0); // "RESERVED"
        $icon_group .= pack("S", $icons[$i][planes]);
        $icon_group .= pack("S", $icons[$i][bitcount]);
        $icon_group .= pack("L", $icons[$i][size]);
        $icon_group .= pack("S", $i + 1);
    }

    print "Updating icon...";
    res_set($outfile, "RT_GROUP_ICON", "#1", $icon_group);
    for ($i = 0; $i < $icon_count; $i++) {
        $worked = res_set($outfile, RT_ICON, "#" . ($i + 1), $icons[$i][data]);
    }
    print "done\n";
}

if ($option_compress) {
    print "Compressing final exe..\n";
    $upx = file_get_contents("upx.exe");
    $f   = fopen("bamcompile_upx.exe", "w");
    fwrite($f, $upx);
    fclose($f);

    $stub = file_get_contents("stub.exe");
    exec("bamcompile_upx -9 $outfile", $out, $ret);
    if ($ret > 0) {
        print "Compression failed! Is upx.exe available?\n";
    } else {
        print "Compression done\n";
    }
    unlink("bamcompile_upx.exe");
}

print "\n$outfile created successfully!\n";
