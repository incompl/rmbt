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

$sql = "SELECT p.to, p.field, sm.name, p.when".
" FROM $tableRoomChanges  p".
" JOIN $tableUsers sm".
" ON p.user_id = sm.id".
" WHERE p.room=".$_GET["rid"].
" ORDER BY p.when desc";
$result = mysql_query($sql) or die("Error'd!  Did you supply a room id?");

echo "<table><tr><td>Who</td><td>When</td><td>Field</td><td>New Value</td></td>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<tr><td>". $row["name"] . "</td>";
	echo "<td>". $row["when"] . "</td>";
	echo "<td>". $row["field"] . "</td>";
	echo "<td>". $row["to"] . "</td></tr>";
}
echo "</table>";
?>

