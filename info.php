<?php
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

$days = $_GET["days"] ? $_GET["days"] : 7;

?>

<form method="GET">

Up to <input type="text" name="days" value="<?=$days?>"/> days ago
<input type="submit" value="Update"/>

</form>

<?


$sql = "SELECT creator, COUNT(id) AS count".
" FROM $tablePlaces" . 
" WHERE DATE_SUB(NOW(), INTERVAL $days DAY) < whenCreated".
" AND plane_id <> 4".
" GROUP BY creator";
$result = mysql_query($sql) or die("Query problems.");

echo "Who created rooms...";
echo "<table><tr><td>Who</td><td>How Many</td></tr>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<tr><td>".$row["creator"]."</td><td>".$row["count"]."</td></tr>";
}
echo "</table><br/>";

$sql = "SELECT n.name, COUNT(p.id) AS count".
" FROM $tablePlaces p" . 
" JOIN $tableAreas n" .
" ON p.plane_id = n.id" .
" WHERE DATE_SUB(NOW(), INTERVAL $days DAY) < p.whenCreated".
" AND plane_id <> 4".
" GROUP BY n.name";
$result = mysql_query($sql) or die("Query problems.");

echo "Where rooms were added...";
echo "<table><tr><td>Where</td><td>How Many</td></tr>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<tr><td>".$row["name"]."</td><td>".$row["count"]."</td></tr>";
}
echo "</table><br/>";


$sql = "SELECT count(distinct pl.id) num, s.name staff" .
" FROM $tableRoomChanges p" .
" JOIN $tableUsers s" .
" ON p.user_id = s.id" .
" JOIN $tablePlaces pl" .
" ON p.room = pl.id" .
" WHERE DATE_SUB(NOW(), INTERVAL $days DAY) < p.when".
" GROUP BY s.name" .
" LIMIT 20";


$result = mysql_query($sql) or die("Query problems..");
echo "Who Modfied Rooms...";
echo "<table><tr><td>Who</td><td>How Many</td></tr>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<tr><td>".$row["staff"]."</td>";
	echo "<td>".$row["num"]."</td></tr>";
}
echo "</table><br/>";

?>
