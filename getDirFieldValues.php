<?
/*
Copyright 2008 Gregory M Smith

This file is part of RMBT.

    RMBT is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    RMBT is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with RMBT.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once("readConfig.php");

$fromLocation_id = $_GET["id"];
$sql = "SELECT x, y, z, plane_id FROM $tablePlaces WHERE id=$fromLocation_id";
$result = mysql_query($sql) or die ("error:Could not find the room you selected, did something happen to it?");
$row = mysql_fetch_assoc($result);
$x = $row["x"];
$y = $row["y"];
$z = $row["z"];
$p = $row["plane_id"];

if (!$_GET["dir"])
	die("error:You forgot to provide a dir, woops");

switch ($_GET["dir"]) { 
case "north":
	$y++;
	break;
case "east":
	$x++;
	break;
case "south":
	$y--;
	break;
case "west":
	$x--;
	break;
case "up":
	$z++;
	break;
case "down":
	$z--;
	break;
case "none":
	die("error:You need to select a direction!");
	break;
}

$sql = "SELECT id FROM $tablePlaces WHERE x=$x AND y=$y AND z=$z AND plane_id=$p";
$result = mysql_query($sql) or die ("error:Could not query for room in that direction from this one.  This is probably a bug.");
$row = mysql_fetch_assoc($result);

if (!$row)
	die ("error:Could not find a room in that direction from this room, is there one?");

$toLocation_id = $row["id"];

$sql = "SELECT *" .
	" FROM $tableExits" .
	" WHERE fromLocation_id = $fromLocation_id" .
	" AND toLocation_id = $toLocation_id";

$result = mysql_query($sql) or die ("error:Could not find a link between those two rooms, is there one?");
$row = mysql_fetch_assoc($result) or die("error:Could not fetch");

if (!$row) {
	$sql = "SELECT *" .
		" FROM $tableExits" .
		" WHERE fromLocation_id = $toLocation_id" .
		" AND toLocation_id = $fromLocation_id";
	$result = mysql_query($sql) or die ("error:Could not find a link between those two rooms, is there one?");
	$row = mysql_fetch_assoc($result) or die("error:Could not fetch");
}
if (!$row) {
	die("error:Could not get link fields between these rooms.");
}

$first = true;

foreach ($dirFields as $field) {
	if ($first)
		$first = false;
	else
		echo ",,,";

	echo $row[$field["dbField"]];

}

?>
