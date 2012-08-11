<?php 
$ts = mktime(0,0,0,$monat,$tagnummer,$jahr);
$wd = date("w", $ts);
$kw = date("W", $ts);
$teas = easter_date($jahr);
$eastertag =  date("j", $teas);
$eastermon = date("n", $teas);
$himmelfahrt = "$teas" + "3447400";
$pfingstmontag = "$teas" + "4320000";
$pfingstsonntag = "$teas" + "4233600";
$fron = "$teas" + "5184000";

$wd = date("w", $ts);
$kw = date("W", $ts);

//Datum
$eastertag =  date("j", $teas);
$eastermon = date("n", $teas);
$himmeltag =  date("j", $himmelfahrt);
$himmelmon = date("n", $himmelfahrt);
$pfingstsotag =  date("j", $pfingstsonntag);
$pfingstsomon = date("n", $pfingstsonntag);
$pfingstmotag =  date("j", $pfingstmontag);
$pfingstmomon = date("n", $pfingstmontag);
$frontag =  date("j", $fron);
$fronmon = date("n", $fron);

if($tagnummer == $eastertag and $monat == $eastermon)
{
$found = "1";
$ft = "Ostersonntag";
}
$karfreitag = "$eastertag" - "2";
if($tagnummer == $karfreitag and $monat == $eastermon)
{
$found = "1";
$ft = "Karfreitag";
}
$ostermontag = "$eastertag" + "1";
if($tagnummer == $ostermontag and $monat == $eastermon)
{
$found = "1";
$ft = "Ostermontag";
}
if($tagnummer == $himmeltag and $monat == $himmelmon)
{
$found = "1";
$ft = "Christi Himmelfahrt";
}
if($tagnummer == $pfingstsotag and $monat == $pfingstsomon)
{
$found = "1";
$ft = "Pfingstsonntag";
}
if($tagnummer == $pfingstmotag and $monat == $pfingstmomon)
{
$found = "1";
$ft = "Pfingstmontag";
}
if($tagnummer == $frontag and $monat == $fronmon)
{
$found = "1";
$ft = "Fronleichnam";
}
if($found != "1")
{
$feiertag = "feiertage.txt";
$search = file($feiertag);
while (list ($line_num, $line) = each($search)) 
{
$ziffern = explode("&&","$line");
if($tagnummer == $ziffern[0] and $monat == $ziffern[1])
{
$ft = $ziffern[2];
$found = "1";
}
}}
?>