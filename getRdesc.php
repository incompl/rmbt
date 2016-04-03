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

$rid = $_GET["id"];
$sql = "SELECT description FROM $tablePlaces WHERE id=$rid";
$result = mysql_query($sql) or die ("error:Could not find the room you selected, did something happen to it?");
$row = mysql_fetch_assoc($result);
echo $row["description"]
?>
