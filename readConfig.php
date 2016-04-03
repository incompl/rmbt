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


// Read in and verify configuration.  If you're trying to edit
// the configuration, edit config.yml.  Note that editing this
// file is covered under the RMBT license allowances for
// modifying the RMBT source code, as it is part of the RMBT
// source code and not a mere configuration file.

require("spyc.php5");

$configFileName = "config.yml";

if (!file_exists($configFileName)) {
	die("Could not find config file.  It should be in the same directory as RMBT and be called " .
		$configFileName);
}	
$config = Spyc::YAMLLoad($configFileName);

$debug = $config["debug"];
$hiddenFields = $config["hiddenFields"];
$specialFields = $config["specialFields"];
$dirFields = $config["dirFields"];

$defaultRoomSize = $config["defaultRoomSize"];
$defaultMapSize = $config["defaultMapSize"];
$defaultStartX = $config["defaultStartX"];
$defaultStartY = $config["defaultStartY"];
$defaultZ = $config["defaultZ"];
$defaultP = $config["defaultP"];
$defaultShowAbove = $config["defaultShowAbove"];
$defaultShowBelow = $config["defaultShowBelow"];
$defaultShowUndescribed = $config["defaultShowUndescribed"];

// Connect to DB -----------------------------

$dbhost = $config["dbHost"];
$dbuser = $config["dbUser"];
$dbpass = $config["dbPassword"];
$dbname = $config["dbName"]; 
$tablePlaces = $config["dbRoomsTable"];
$tableExits = $config["dbLinksTable"];
$tableAreas = $config["dbAreasTable"];
$tableRoomChanges = $config["dbRoomChangesTable"];
$tableUsers = $config["dbUsersTable"];

$conn = mysql_connect($dbhost, $dbuser, $dbpass)
or die ('Error connecting to mysql');

mysql_select_db($dbname);

// Get user ID of this user ----------------------

$userName = $_SERVER["PHP_AUTH_USER"];
$sql = "SELECT id FROM $tableUsers WHERE name='$userName'";
$result = mysql_query($sql) or die("Couldn't get your user ID.  Does the user table exist?");
$row = mysql_fetch_assoc($result);
if (!$row)
	die("'$userName' is not an RMBT user.");
$userId = $row["id"];

?>
