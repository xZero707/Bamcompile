<?
@$inifile = file_get_contents("res:///PHP/PHP.INI");

if($inifile)
{
$f = fopen("phpini.bam","w");
fwrite($f,$inifile);
fclose($f);
}
?>