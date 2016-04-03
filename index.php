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

// How long will this take?
$start = time();

// Internals ---------------------------------

$roomSize = $_POST["roomSize"] != null ? $_POST["roomSize"] : $defaultRoomSize;
$mapSize = $_POST["mapSize"] != null ? $_POST["mapSize"] : $defaultMapSize;
$startX = $_POST["startX"] != null ? $_POST["startX"] : $defaultStartX;
$startY = $_POST["startY"] != null ? $_POST["startY"] : $defaultStartY;
$z = $_POST["z"] != null ? intval($_POST["z"]) : $defaultZ;
$p = $_POST["plane_id"] != null ? intval($_POST["plane_id"]) : $defaultP;
if (!$_POST)
	$showAbove = $defaultShowAbove;
else
	$showAbove = $_POST["showAbove"] == "on" ? true : false;
if (!$_POST)
	$showBelow = $defaultShowBelow;
else
	$showBelow = $_POST["showBelow"] == "on" ? true : false;
if (!$_POST)
	$showUndescribed = $defaultShowUndescribed;
else
	$showUndescribed = $_POST["showUndescribed"] == "on" ? true : false;
$message = null;
$error = false;

?>
<html>
<head>
<title>RMBT</title>
<link href="style.css" rel="stylesheet" type="text/css"/>
<style>
.room,.nonRoom,.selectedRoom,.selectedNonRoom {
	height: <?=$roomSize - 2?>px;
	width: <?=$roomSize - 2?>px;
}
</style>
</head>
<body style="background-color: #eeeeff;" onload="onload()" id="body">
<?

// Save any edits --------------------------------------

if ($debug)
	$message .= "Debug mode enabled: SQL statements are shown.<br/>";

if ($_POST["submitEdit"] != null) {
	
	$fieldsInUpdateQuery = explode(",", $_POST["fieldsInUpdateQuery"]);
	array_splice($fieldsInUpdateQuery, count($fieldsInUpdateQuery) - 1);
	if (count($fieldsInUpdateQuery) < 1) {
		$error = true;
		$message = "No room fields were changed.";

	}
	else {
		$ids = explode(",", $_POST["ids"]);
		array_splice($ids, count($ids) - 1);
		foreach ($ids as $id) {
			if ($id == "")
				continue;

			$dbCols = array();
			foreach ($_POST as $key => $value) {
				if (strchr($key, "field_") == false)
					continue;
				if (!in_array($key, $fieldsInUpdateQuery))
					continue;
				$dbCols[substr($key, 6, strlen($key) - 6)] = $value;
			}



			$sql = "UPDATE $tablePlaces ";
			$first = true;
			foreach ($dbCols as $field => $value) {
				if ($first) {
					$sql .= "SET ";
					$first = false;
				}
				else
					$sql .= ", ";
				$escapedVal = mysql_real_escape_string($value);
				$escapedField = mysql_real_escape_string($field);
				$sql .= "$escapedField='". $escapedVal ."'";

				// Keep a record of this change
				$recordSql = "INSERT INTO $tableRoomChanges
					(user_id, field, room, $tableRoomChanges.to, $tableRoomChanges.when)
					VALUES
					($userId, '$escapedField', $id, '$escapedVal', NOW())";
				if ($debug)
					$message .= $recordSql . "<br/>";
				mysql_query($recordSql) or $message .="Couldn't update log.<br/>";
			}


			$sql .= " WHERE id=$id";

			if ($debug)
				$message .= $sql . "<br/>";

			$success = mysql_query($sql);
			if (!$success) {
				$error = true;
				$message .= "Error updating room $id<br/>";
			}

		}
		$count = count($ids);
		if ($count == 0) {
			$message .= "No rooms selected to update!";
			$error = true;
		}
		else if (!$error)
			$message .= "Updated $count rooms.";
	}
}

// Create any new rooms --------------------------------

if ($_POST["submitCreate"] != null) {

	$newRoomCount = 0;
	$positions = explode(";", $_POST["positions"]);
	foreach ($positions as $position) {
		$coordinates = explode(",", $position);
		$x = $coordinates[0];
		$y = $coordinates[1];
		if ($x == "" || $y == "") {
			$error = true;
			$message .= "No locations selected.<br/>";
			break;
		}
		$sql = "INSERT INTO $tablePlaces (x, y, z, plane_id, creator, whencreated)" .
			" VALUES ($x, $y, $z, $p, '". $_SERVER["PHP_AUTH_USER"] ."', NOW())"; 
		$result = mysql_query($sql);
		if ($debug)
			$message .= $sql . "<br/>";
		if (!$result) {
			$error = true;
			$message .= "Error creating room at $x, $y, $z, $p!<br/>";
		}
		else {
			$newRoomCount++;
			if ($_POST["createLinks"]) {
				$sql = "SELECT id FROM $tablePlaces WHERE x=$x AND y=$y AND z=$z AND plane_id=$p";
				$result = mysql_query($sql) or die("Could not create room.");
				$row = mysql_fetch_assoc($result);
				$id = $row["id"];
				link_rooms($id, 0, 1, 0);
				link_rooms($id, 1, 0, 0);
				link_rooms($id, 0, -1, 0);
				link_rooms($id, -1, 0, 0);
				link_rooms($id, 0, 0, 1);
				link_rooms($id, 0, 0, -1);
			}
		}
	}

	$message .= "Created $newRoomCount " . ($newRoomCount == 1 ? "room." : "rooms");

}

// Delete any rooms whose deletion was requested -------

if ($_POST["submitDelete"] != null) {

	$ids = explode(",", $_POST["ids"]);
	array_splice($ids, count($ids) - 1);
	$count = 0;
	foreach ($ids as $id) {
		$sql = "DELETE FROM $tablePlaces WHERE id=$id LIMIT 1";
		if ($debug)
			$message .= "$sql<br/>";
		$success = mysql_query($sql) or die("Could not delete rooms.");
		if ($success)
			$count++;
		else {
			$error = true;
			$message .= "Could not delete room $id<br/>";
		}
	}
	if ($count == 0) {
		$message .= "No rooms deleted.";
		$error = true;
	}
	else if ($count == 1) {
		$message .= "1 room deleted.";
	}
	else {
		$message .= "$count rooms deleted.";
	}

}

// Create / remove /edit links ------------------------------

function edit_links($id1, $xOffset, $yOffset, $zOffset) {

	global $error, $message, $debug, $dirFields, $tablePlaces, $tableExits;

	$sql = "SELECT x, y, z, plane_id FROM $tablePlaces WHERE id=$id1";
	if ($debug)
		$message .= $sql . "<br/>";
	$result = mysql_query($sql) or die("Could not get room when editing links. $sql");
	$row = mysql_fetch_assoc($result);
	$x = $row["x"] + $xOffset;
	$y = $row["y"] + $yOffset;
	$z = $row["z"] + $zOffset;
	$p = $row["plane_id"];
	$sql = "SELECT id FROM $tablePlaces WHERE ";
	$sql .= "x=$x AND y=$y AND z=$z AND plane_id=$p";
	if ($debug)
		$message .= $sql . "<br/>";
	$result = mysql_query($sql) or die("Could not edit links");
	$row = mysql_fetch_assoc($result);
	if (!$row) {
		if ($debug)
			$message .= "No room to edit link to at $x, $y, $z<br/>";

		return;
	}
	$id2 = $row["id"];

	$sql = "UPDATE $tableExits";
	$first = true;
	foreach ($dirFields as $field) {

		$newValue = mysql_real_escape_string($_POST[$field["dbField"]]);
		if ($field["inputType"] == "checkbox") {
			$newValue = $_POST[$field["dbField"]] ? 1 : 0;
		}
		if ($first) {
			$first = false;
			$sql .= " SET";
		}
		else {
			$sql .= ",";
		}
		$sql .= " " . $field["dbField"] . "='$newValue'";

	}

	$whereClause = " WHERE fromLocation_id = $id1 AND toLocation_id = $id2";
	if ($debug)
		$message .= $sql . $whereClause . "<br/>";
	$result1 = mysql_query($sql . $whereClause) or die("Could not get from room: $sql");
	$whereClause = " WHERE fromLocation_id = $id2 AND toLocation_id = $id1";
	if ($debug)
		$message .= $sql . $whereClause . "<br/>";
	$result2 = mysql_query($sql . $whereClause) or die("Could not get to room");

}

function unlink_rooms($id1, $xOffset, $yOffset, $zOffset) {

	global $error, $message, $debug, $tablePlaces, $tableExits;

	$sql = "SELECT x, y, z, plane_id FROM $tablePlaces WHERE id=$id1";
	if ($debug)
		$message .= $sql . "<br/>";
	$result = mysql_query($sql) or die("Could not get room info when trying to unlink: $sql");
	$row = mysql_fetch_assoc($result);
	$x = $row["x"] + $xOffset;
	$y = $row["y"] + $yOffset;
	$z = $row["z"] + $zOffset;
	$p = $row["plane_id"];
	$sql = "SELECT id FROM $tablePlaces WHERE ";
	$sql .= "x=$x AND y=$y AND z=$z AND plane_id=$p";
	if ($debug)
		$message .= $sql . "<br/>";
	$result = mysql_query($sql) or die("Could not unlink rooms");
	$row = mysql_fetch_assoc($result);
	if (!$row) {
		if ($debug)
			$message .= "No room to unlink from at $x, $y, $z<br/>";

		return;
	}
	$id2 = $row["id"];

	$sql = "DELETE FROM $tableExits WHERE fromLocation_id = $id1 AND toLocation_id = $id2";
	if ($debug)
		$message .= $sql . "<br/>";
	$result1 = mysql_query($sql) or die("Could not delete link");
	$sql = "DELETE FROM $tableExits WHERE fromLocation_id = $id2 AND toLocation_id = $id1";
	if ($debug)
		$message .= $sql . "<br/>";
	$result2 = mysql_query($sql) or die("Could not delete second link");

}

function link_rooms($id1, $xOffset, $yOffset, $zOffset) {

	global $error, $message, $debug, $tablePlaces, $tableExits;

	// Get position info for room we're linking from
	$sql = "SELECT x, y, z, plane_id FROM $tablePlaces WHERE id=$id1";
	if ($debug)
		$message .= $sql . "<br/>";
	$result = mysql_query($sql) or die("Could not get position of room we're linking to: $sql");
	$row = mysql_fetch_assoc($result);
	$x = $row["x"] + $xOffset;
	$y = $row["y"] + $yOffset;
	$z = $row["z"] + $zOffset;
	$p = $row["plane_id"];

	// Get ID of room we're linking to
	$sql = "SELECT id FROM $tablePlaces WHERE ";
	$sql .= "x=$x AND y=$y AND z=$z AND plane_id=$p";
	if ($debug)
		$message .= $sql . "<br/>";
	$result = mysql_query($sql) or die("Could not get room to link to: $sql");
	$row = mysql_fetch_assoc($result);
	if (!$row) {
		if ($debug)
			$message .= "No room to link to at $x, $y, $z<br/>";
		if ($_POST["createRoomsFromLinks"] != null) {
			$sql = "INSERT INTO $tablePlaces (x,y,z,plane_id) VALUES ($x,$y,$z,$p)";
			$result = mysql_query($sql);
			if (!$result) {
				$error = true;
				$message .= "Problem creating a room to link to.<br/>";
				return;
			}
			$sql = "SELECT id FROM $tablePlaces WHERE ";
			$sql .= "x=$x AND y=$y AND z=$z AND plane_id=$p";
			if ($debug)
				$message .= $sql . "<br/>";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
		}
		else
			return;
	}
	$id2 = $row["id"];

	// Find out if they're already linked
	$sql = "SELECT COUNT(fromLocation_id) count FROM $tableExits WHERE fromLocation_id=$id1 AND toLocation_id=$id2";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	if ($row["count"] > 0) {
		$message .= "$id1 and $id2 are already linked.<br/>";
		return;
	
	}
	$sql = "SELECT COUNT(fromLocation_id) count FROM $tableExits WHERE fromLocation_id=$id2 AND toLocation_id=$id1";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	if ($row["count"] > 0) {
		$message .= "$id1 and $id2 are already linked.<br/>";
		return;
	}

	
	// Link one way...
	$sql = "INSERT INTO $tableExits (fromLocation_id, toLocation_id) VALUES ($id1, $id2)";
	if ($debug)
		$message .= $sql . "<br/>";
	$success = mysql_query($sql);
	if (!$success) {
		$message .= "Could not create link(s)<br/>";
		$error = true;
	}

	// ...and the other.
	$sql = "INSERT INTO $tableExits (fromLocation_id, toLocation_id) VALUES ($id2, $id1)";
	if ($debug)
		$message .= $sql . "<br/>";
	$success = mysql_query($sql);
	if (!$success) {
		$message .= "Could not create link(s)<br/>";
		$error = true;
	}
}

if ($_POST["submitLink"]) {

	$ids = explode(",", $_POST["ids"]);
	array_splice($ids, count($ids) - 1); // last item is junk, since string ends with a comma
	$linkNorth = $_POST["linkNorth"];
	$linkEast = $_POST["linkEast"];
	$linkSouth = $_POST["linkSouth"];
	$linkWest = $_POST["linkWest"];
	$linkUp = $_POST["linkUp"];
	$linkDown = $_POST["linkDown"];
	if (!$linkUp && !$linkDown && !$linkNorth && !$linkEast && !$linkSouth && !$linkWest) {
		$message = "You need to select at least one direction to link/unlink/edit in.";
		$error = true;
	}
	else if ($_POST["link"] == link) {
		foreach ($ids as $id1) {
			if ($linkNorth) link_rooms($id1, 0, 1, 0);
			if ($linkEast) link_rooms($id1, 1, 0, 0);
			if ($linkSouth) link_rooms($id1, 0, -1, 0);
			if ($linkWest) link_rooms($id1, -1, 0, 0);
			if ($linkUp) link_rooms($id1, 0, 0, 1);
			if ($linkDown) link_rooms($id1, 0, 0, -1);
		}
		$message .= "Done.";
	}
	else if ($_POST["link"] == "unlink") {
		foreach ($ids as $id1) {
			if ($linkNorth) unlink_rooms($id1, 0, 1, 0);
			if ($linkEast) unlink_rooms($id1, 1, 0, 0);
			if ($linkSouth) unlink_rooms($id1, 0, -1, 0);
			if ($linkWest) unlink_rooms($id1, -1, 0, 0);
			if ($linkUp) unlink_rooms($id1, 0, 0, 1);
			if ($linkDown) unlink_rooms($id1, 0, 0, -1);
		}
		$message .= "Done.";
	}
	else if ($_POST["link"] == "edit") {

		foreach ($ids as $id1) {
			if ($linkNorth) edit_links($id1, 0, 1, 0);
			if ($linkEast) edit_links($id1, 1, 0, 0);
			if ($linkSouth) edit_links($id1, 0, -1, 0);
			if ($linkWest) edit_links($id1, -1, 0, 0);
			if ($linkUp) edit_links($id1, 0, 0, 1);
			if ($linkDown) edit_links($id1, 0, 0, -1);
		}
		$message .= "Done.";
	}
	else {
		$message = 'You need to select what you want to do first.';
		$error = true;
	}

}

// Move any rooms moved --------------------------------

// TODO

// Show dialogue if needed -----------------------------

if ($message != null) {
?>
<span id="message" style="position: absolute;
	z-index: 999999;
	padding: 4px;
	background-color: white;
	border: solid black 1px;">
<span style="font-weight:bold;
	color:<?= $error ? "red" : "black" ?>;">
	<?=$message?>
	<?=mysql_error($conn) == "" ? "" : "<br/>MySQL said: " . mysql_error($conn)?>
</span>
<span style="margin-left:5px;">
[<a href=""
   onclick="document.getElementById('message').style.display='none';return false">X</a>]
</span>
</span>
<?
}


// Javascript ------------------------------
?><script type="text/javascript">

 var shift = false
 var hud

function ajaxObj() {

  var xmlHttp
  try {
    xmlHttp=new XMLHttpRequest(); // Firefox, Opera 8.0+, Safari
  }
  catch (e) {
    try {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP"); // IE
    }
    catch (e) {
      try {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch (e) {
        alert("Your browser does not support AJAX!");
      }
    }
  }
  return xmlHttp
}

function showHistory() {

	room = hud.selected[0]

	if (room)
		window.open("roomHistory.php?rid=" + room.id);
	else
		alert("No room selected.");

}

function onload() {
	document.getElementById("linkField").checked = true
}

var currentGetRdescId = 0;
function getRdesc(id) {
	currentGetRdescId = id
	var xmlHttp = ajaxObj()
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			var response = xmlHttp.responseText;
			document.getElementById("infoBoxRdesc").innerHTML = response

		}
	}

	function go(id) {
		if (currentGetRdescId != id)
			return
		if (document.getElementById("info").style.display == 'none')
			return

		xmlHttp.open("GET", "getRdesc.php?id=" + id, true)
		xmlHttp.send(null)
	}

	setTimeout(function(){go(id)}, 500)
}

function showFieldValues() {

	var response
	var xmlHttp = ajaxObj()
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			var response = xmlHttp.responseText;
			if (response.substr(0,5) == "error") {
				alert("Could not get field values from server.")
				return;
			}
			var fieldValuePairs = response.split(',,,')
			for (var i = 0; i < fieldValuePairs.length; i++) {
				var fieldValuePair = fieldValuePairs[i].split(/;;;/)
				var fieldId = "field_" + fieldValuePair[0]
				var field = document.getElementById(fieldId)
				if (!field) {
					continue
				}
				var value = fieldValuePair[1]

				var cbox = document.getElementById("checkbox_" + fieldValuePair[0])
				if (cbox) {
					cbox.checked = (value == 1)
				}

				field.value = value
			}
		}
	}

	if (hud.selected.length == 0)
		return;

	var id = hud.selected[0].id;

	xmlHttp.open("GET", "getFieldValues.php?id=" + id, true)
	xmlHttp.send(null)

}

function clearDirFields() {

	<?
	$i = 0;
	foreach ($dirFields as $field) {

		if ($field["inputType"] == "checkbox") {
			?>document.getElementById('<?=$field["dbField"]?>').checked = false;<?
		}
		else {
			?>document.getElementById('<?=$field["dbField"]?>').value = "";<?
		}

	}
	$i++;
	?>
	return;
}

var editDirMode = false
function showDirFieldValues() {

	if (!editDirMode)
		return;

	var response
	var xmlHttp = ajaxObj()
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			var response = xmlHttp.responseText;
			if (response.match(/^error/)) {
				clearDirFields()
				return
			}
			var fieldValues = response.split(',,,')
			<?
			$i = 0;
			foreach ($dirFields as $field) {
				if ($field["inputType"] == "checkbox") {
					?>document.getElementById('<?=$field["dbField"]?>').checked = (fieldValues[<?=$i?>] == 1);<?
				}
				else {
					?>document.getElementById('<?=$field["dbField"]?>').value = fieldValues[<?=$i?>];<?
				}
				$i++;
			}
			?>
		}
	}
	var dir = "none"
	dir = document.getElementsByName("linkNorth")[0].checked == true ? "north" : dir
	dir = document.getElementsByName("linkEast")[0].checked == true ? "east" : dir
	dir = document.getElementsByName("linkSouth")[0].checked == true ? "south" : dir
	dir = document.getElementsByName("linkWest")[0].checked == true ? "west" : dir
	dir = document.getElementsByName("linkUp")[0].checked == true ? "up" : dir
	dir = document.getElementsByName("linkDown")[0].checked == true ? "down" : dir

	if (hud.selected.length == 0) {
		clearDirFields();
		return;
	}

	var id = hud.selected[0].id;

	xmlHttp.open("GET",
	"getDirFieldValues.php?id=" + id + "&dir=" + dir, true)
	xmlHttp.send(null)

}

function disableLinkFields(enableMeId) {
	var linkFieldOptions = document.getElementById("linkFieldOptions")
	var unlinkFieldOptions = document.getElementById("unlinkFieldOptions")
	var editLinkFieldOptions = document.getElementById("editLinkFieldOptions")
	linkFieldOptions.style.display = "none"
	unlinkFieldOptions.style.display = "none"
	editLinkFieldOptions.style.display = "none"
	document.getElementById(enableMeId).style.display = "block"
}

function showInfo(event, roomX, roomY) {

	var infoBox = document.getElementById('info')
	var room = rooms[roomX][roomY];
	
	// Set the info	
	getRdesc(room.id)
	infoBox.innerHTML = '<div style="font-weight: bold;">'
		+ room.name + ' [' + room.id + "] (" +
		room.x + "," + room.y + "," + room.z +
		')</div><span id="infoBoxRdesc">Loading...</span>';

	// Display the info
	infoBox.style.top = event.clientY;
	infoBox.style.left = event.clientX + 30;
	infoBox.style.display = 'block'

}

function hideInfo() {
	document.getElementById('info').style.display = 'none'
}

function checkFieldCheckbox(checkbox, fieldId) {
	field = document.getElementById(fieldId);
	field.value = checkbox.checked ? "1" : "0";
}

function updateFieldCheckbox(field) {
	var checkbox = document.getElementById(field.name.replace("field_", "checkbox_"));
	checkbox.checked = field.value == "1" ? true : false;
}

function clearFieldCheckbox(field) {
	var checkbox = document.getElementById(field.name.replace("field_", "checkbox_"));
	if (checkbox)
		checkbox.checked = false;
}

// When you click a room, check or uncheck it
function checkChildCheckbox(me, event) {
	if (event)
		shift = event.shiftKey
	var checkbox = me.getElementsByTagName("input");
	checkbox[0].click();
}

// Show the squares for places that are not rooms
var nonRooms = [];
function showNonRoomSpace() {

	// Create the squares if they don't exist yet
	if (nonRooms.length == 0) {
		for (var x = <?=$startX?>; x < <?=$startX + $mapSize ?>; x++) {
			for (var y = <?=$startY?>; y < <?=$startY + $mapSize ?>; y++) {
				if (rooms[x][y] != null)
					continue;
				var div = document.createElement('div');
				var input = document.createElement('input');
				
				div.onclick = function(){checkChildCheckbox(this);}
				div.className = "nonRoom";
				div.name = "nonRoom";
				div.id = '' + x + ',' + y;
				div.style.left = (x - <?=$startX?>) * <?=$roomSize?> ;
				div.style.top = (<?=$mapSize?> - (y - <?=$startY?>)) * <?=$roomSize?>;

				input.onclick = function(){highlight(this);}
				input.name = "nonRoomCheckbox";
				input.id = '' + x + ',' + y;
				input.className = "checkbox";
				input.type = "checkbox";
				input.disabled = "disabled";

				document.body.appendChild(div);
				div.appendChild(input);
				nonRooms.push(div);
			}
		}
	}

	// Show them
	for (var i = 0; i < nonRooms.length; i++) {
		if (nonRooms[i].style.display == "block")
			return;
		nonRooms[i].style.display = "block";
	}
	var roomCheckboxes = document.getElementsByName("roomCheckbox");
	for (var i = 0; i < roomCheckboxes.length; i++) {
		roomCheckboxes[i].disabled = true;
		roomCheckboxes[i].checked = false;
	}
	var nonRoomCheckboxes = document.getElementsByName("nonRoomCheckbox");
	for (var i = 0; i < nonRoomCheckboxes.length; i++) {
		nonRoomCheckboxes[i].disabled = false;
		nonRoomCheckboxes[i].checked = false;
	}
}

// Hide the squares for places that are not rooms
function hideNonRoomSpace() {
	for (var i = 0; i < nonRooms.length; i++) {
		if (nonRooms[i].style.display == "none" || nonRooms[i].style.display == "")
			return;
		nonRooms[i].style.display = "none";
	}
	var roomCheckboxes = document.getElementsByName("roomCheckbox");
	for (var i = 0; i < roomCheckboxes.length; i++) {
		roomCheckboxes[i].disabled = false;
		roomCheckboxes[i].checked = false;
	}
	var nonRoomCheckboxes = document.getElementsByName("nonRoomCheckbox");
	for (var i = 0; i < nonRoomCheckboxes.length; i++) {
		nonRoomCheckboxes[i].disabled = true;
		nonRoomCheckboxes[i].checked = false;
	}
}

var allTabIds = ["view", "edit", "create", "delete", "link"]
function hideAllTabs() {
	for (var i = 0; i < allTabIds.length; i++) {
		document.getElementById(allTabIds[i]).style.display = "none"
	}
}

// Show the tab with given id
function selectTab(link, id) {
	hideAllTabs();
	document.getElementById(id).style.display = "block";

	var links = document.getElementsByName("tabLink");
	for (var i = 0; i < links.length; i++) {
		links[i].className = "tab";
	}
	link.parentNode.className = "selectedTab";
	
}


// Called when user submits with nonroom squares checked
function prepareToSubmitNonRooms() {
	var fieldValue = "";
	var checkboxes = document.getElementsByName("nonRoomCheckbox");
	for (i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked)
			fieldValue += checkboxes[i].id + ";";
	}
	fieldValue = fieldValue.substring(0, fieldValue.length - 1);
	var field = document.getElementById("positions");
	field.value = fieldValue;
}

// Called when user submits with rooms checked
function prepareToSubmitRooms() {

	// Create hidden form field with fields to update if doing an update query
	for (var i = 0; i < fieldsInUpdateQuery.length; i++) {
		document.getElementById("fieldsInUpdateQuery").value += fieldsInUpdateQuery[i] + ",";
	}

	// Create a hidden form field with the room IDs that are selected
	var ids = "";
	for (x = <?=$startX?>; x < <?=$startX + $mapSize?>; x++) {
		roomsRow = rooms[x];
		if (!roomsRow)
			continue;
		for (y = <?=$startY?>; y < <?=$startY + $mapSize?>; y++) {
			room = roomsRow[y];
			if (room && room.selected) {
				ids += room.id + ",";
			}
		}
	}
	document.getElementById("ids").value = ids;	

}

// Interface for the HUD (the sidebar with the tools)
function HUD() {

	// Contains the selected rooms
	this.selected = [];

}
HUD.prototype.update = function() {
	// Clear fields
	var fields = document.getElementsByTagName("input");
	for (var i = 0; i < fields.length; i++) {
		element = fields[i];
		if (element.name.indexOf("field_") < 0)
			continue;
		element.value = "";
		clearFieldCheckbox(element);
	}
	var textareas = document.getElementsByTagName("textarea");
	for (var i = 0; i < textareas.length; i++) {
		textareas[i].value = "";
	}

	// Set fields
	for (var i = 0; i < this.selected.length; i++) {
		room = this.selected[i];
		for (member in room) {
			var element = document.getElementById("field_" + member);
			if (element) {
				if (element.value != "" && element.value != room[member]) {
					element.style.color = "red";
				}
				else {
					element.style.color = "black";
				}
				element.value = room[member];
				if (element.type == "hidden")
					updateFieldCheckbox(element);
			}
		}
	}
}

hud = new HUD();

var rooms = new Array();
for (i = <?=$startX?>; i < <?=$startX + $mapSize ?>; i++) {
	rooms[i] = [];
}

function highlight(checkbox) {
	if (!doneLoading) {
		return;
	}
	var node = checkbox.parentNode;
	if (node.className == "room") {
		node.className = "selectedRoom";
	}
	else if (node.className == "selectedRoom")
		node.className = "room";
	else if (node.className == "nonRoom")
		node.className = "selectedNonRoom";
	else if (node.className == "selectedNonRoom")
		node.className = "nonRoom";
}

// When you click a checkbox for a room on the page
function selectRoom(x, y) {

	if (!doneLoading) {
		return;
	}

	room = rooms[x][y];

	var wasSelected = room.selected
	var roomSelectedBefore = hud.selected[0]
	if (wasSelected) {
		var i;
		for (i = 0; i < hud.selected.length; i++) {
			if (hud.selected[i] == room)
				break;
		}
		hud.selected.splice(i, 1);
	}
	else {
		hud.selected.push(room);
	}

	room.selected = !room.selected;

	// Maybe select some more stuff too if they're holding shift
	if (shift && !wasSelected && roomSelectedBefore) {

		var oldRoomX = Number(roomSelectedBefore.x)
		var roomX = Number(room.x)
		var oldRoomY = Number(roomSelectedBefore.y)
		var roomY = Number(room.y)

		var x1 = (oldRoomX < roomX) ? oldRoomX : roomX
		var x2 = (oldRoomX >= roomX) ? oldRoomX : roomX
		var y1 = (oldRoomY < roomY) ? oldRoomY : roomY
		var y2 = (oldRoomY >= roomY) ? oldRoomY : roomY

		for (var x = x1; x <= x2; x++) {
			for (var y = y1; y <= y2; y++) {
				var room = rooms[x][y]
				if (room.selected == false) {
					hud.selected.push(room)
					room.selected = true
					var checkbox = document.getElementById("" + x + "," + y)
					highlight(checkbox)
				}
			}
		} 
	}

	showDirFieldValues();
	showFieldValues();

	document.getElementById("numSelected").innerHTML = hud.selected.length;
	hud.update();
}

var fieldsInUpdateQuery = [];

</script><?

// Figure out what fields there are -----------
$fields = array();
$sql = "SELECT * FROM $tablePlaces LIMIT 1";
$result = mysql_query($sql) or die("Could not find out what fields there are.");

while ($row = mysql_fetch_assoc($result)) {

	// Make a list of what fields we find
	if (count($fields) == 0) {
		foreach($row as $fieldName => $fieldValue) {
			$fields[$fieldName] = $fieldValue;
		}
	}

}

// Query DB for rooms -------------------------

$sql = "SELECT id, x, y, z, name FROM $tablePlaces 
WHERE x >= $startX
AND x < " . ($startX + $mapSize) . "
AND y >= $startY
AND y < " . ($startY + $mapSize) . "
AND z =$z
AND plane_id = $p";
$result = mysql_query($sql) or die("Could not query DB for rooms");

// Create the rooms ----------------------------
$rooms = array();
while ($row = mysql_fetch_assoc($result)) {

	// Keep track of what rooms exist
	if ($rooms[$row["x"]] == null)
		$rooms[$row["x"]] = array();
	$rooms[$row["x"]][$row["y"]] = $id;

	// Find out where we link to----------
	$sql = "SELECT toLocation_id, fromLocation_id, door
		FROM $tableExits 
		WHERE fromLocation_id=" . $row["id"];
	$linkResult = mysql_query($sql) or die("Failed to get exits to find out where room links to: $sql");
	$links = array();
	while ($linkRow = mysql_fetch_assoc($linkResult)) {
		$sql = "SELECT *
			FROM $tablePlaces
			WHERE id=" . $linkRow["fromLocation_id"] . "
			OR id=". $linkRow["toLocation_id"];
		$linkedRoomResult = mysql_query($sql) or die("Could not get destination room");
		while ($linkedRoom = mysql_fetch_assoc($linkedRoomResult)) {
			$linkVal = $linkRow["door"] == 1 ? "door" : "normal";
			if ($linkedRoom["x"] == ($row["x"] + 1)) $links["east"] = $linkVal;
			if ($linkedRoom["x"] == ($row["x"] - 1)) $links["west"] = $linkVal;
			if ($linkedRoom["y"] == ($row["y"] + 1)) $links["north"] = $linkVal;
			if ($linkedRoom["y"] == ($row["y"] - 1)) $links["south"] = $linkVal;
			if ($linkedRoom["z"] == ($row["z"] + 1)) $links["up"] = $linkVal;
			if ($linkedRoom["z"] == ($row["z"] - 1)) $links["down"] = $linkVal;
		}
	}

	// Decide how to display it
	$roomId = null;
	$roomColor = null;
	$sql = "SELECT road, indoors, falling, color FROM $tablePlaces WHERE id=".$row["id"];
	$dispResult = mysql_query($sql) or die("Could not get fields used for coloring: $sql");
	$dispRow = mysql_fetch_assoc($dispResult);
	if ($dispRow["falling"] == 1)
		$roomId = "room_falling";
	if ($dispRow["indoors"] == 1)
		$roomId = "room_indoors";
	if ($dispRow["road"] == 1)
		$roomId = "room_road";
	if ($dispRow["color"])
		$roomColor = mysql_real_escape_string($dispRow["color"]);

	// Output the room visually------------
	?><div onclick="checkChildCheckbox(this, event)"
		class="room"
		id="<?=$roomId != null ? $roomId : ''?>"
		onmouseover="showInfo(event, <?=$row["x"]?>, <?=$row["y"]?>)"
		onmouseout="hideInfo()"
		style="left:<?= ($row["x"] -$startX) * $roomSize?>;
		top:<?= ($mapSize - ($row["y"] - $startY)) * $roomSize ?>;
		<?=$roomColor ? "background-color:$roomColor;" : ""?>
		<?=$links["north"] === "normal" ? "border-top-color:#C0C0C0;" : ""?>
		<?=$links["east"] === "normal" ? "border-right-color:#C0C0C0;" : ""?>
		<?=$links["south"] === "normal" ? "border-bottom-color:#C0C0C0;" : ""?>
		<?=$links["west"] === "normal" ? "border-left-color:#C0C0C0;" : ""?>
		<?=$links["north"] === "door" ? "border-top-color:red; border-top-style:dashed;" : ""?>
		<?=$links["east"] === "door" ? "border-right-color:red; border-right-style:dashed;" : ""?>
		<?=$links["south"] === "door" ? "border-bottom-color:red; border-bottom-style: dashed;" : ""?>
		<?=$links["west"] === "door" ? "border-left-color:red; border-left-style: dashed;" : ""?>
">
	<input name="roomCheckbox" class="checkbox" type="checkbox" id="<?= $row["x"] . "," . $row["y"] ?>"
		onclick="highlight(this);selectRoom(<?=$row["x"] . "," . $row["y"]?>)"
		/>
	<?= stripslashes($row["name"]) ?><br/>
	<?= $links["up"] != null ? '<span class="up">U</span>' : ""?>
	<?= $links["down"] != null ? '<span class="down">D</span>' : ""?>
	<?= $links["up"] != null || $links["down"] != null ? "<br/>" : ""?>
	<?= $row["x"] . "," . $row["y"] ?>
	</div><?

	// Create the data object------------
	?><script type="text/javascript">
	var room = new function() {
		<?
		// Dump the database fields into the JS room object
		foreach ($row as $fieldName => $fieldValue) {
			echo "this." . $fieldName . " = '" . str_replace("'", "\'", str_replace("\'", "\\'", str_replace("\n", "\\n", str_replace("\r", "\\r", $fieldValue)))) . "';";
		}
		?>
		this.selected = false;
	}
	rooms[<?=$row["x"]?>][<?=$row["y"]?>] = room;
	</script>
<?
}

// Show adjacent maps if needed ------------

$sql = "SELECT x, y FROM $tablePlaces 
WHERE x >= $startX
AND x < " . ($startX + $mapSize) . "
AND y >= $startY
AND y < " . ($startY + $mapSize);

$markerAdjust = 0;

function showAdjecentMap($color, $sql) {
	global $markerAdjust, $roomSize, $mapSize, $startX, $startY, $z, $p;

	$result = mysql_query($sql) or die("Could not show adjecent map");
	while ($row = mysql_fetch_assoc($result)) {
?><div class="adjacent" style="left: <?= ($row["x"] - $startX) * $roomSize + $markerAdjust?>;
	top: <?= ($mapSize - ($row["y"] - $startY)) * $roomSize ?>;
	background-color: <?=$color;?>;">
</div><?
	}
	$markerAdjust += 5;
}

if ($showAbove)
	showAdjecentMap("red", $sql . " AND z = " . ($z + 1) . " AND plane_id = $p");
if ($showBelow)
showAdjecentMap("blue", $sql . " AND z = " . ($z - 1) . " AND plane_id = $p");
if ($showUndescribed)
showAdjecentMap("yellow", $sql . " AND z=$z AND plane_id=$p AND description=''");

if (count($rooms) < 1) {
	echo '<div style="position: absolute;">No rooms in this view.</div>';
}

// Make the dashboard -------------------------------
?><div id="dashboard">
<div style="text-align: center; background-color: #eeeeff;">
<span name="tabLink" class="selectedTab"><a href="" onclick="selectTab(this, 'view'); return false;">View</a></span>
<span name="tabLink" class="tab"><a href="" onclick="selectTab(this, 'edit'); hideNonRoomSpace(); return false;">Edit</a></span>
<span name="tabLink" class="tab"><a href="" onclick="selectTab(this, 'create'); showNonRoomSpace(); return false;">Create</a></span>
<span name="tabLink" class="tab"><a href="" onclick="selectTab(this, 'delete'); hideNonRoomSpace(); return false;">Delete</a></span>
<span name="tabLink" class="tab"><a href="" onclick="selectTab(this, 'link'); hideNonRoomSpace(); return false;">Link</a></span>
</div>
<hr/>
<form name="dashboard" action="." method="post">

<!-- EDIT TAB -->

<div id="edit" style="display: none;" name="tab">
Edit <span id="numSelected">0</span> rooms<br/><br/>

<a href="." onclick="showHistory(); return false;">Room History</a><br/><br/>

<?

foreach ($fields as $fieldName => $fieldValue) {
	
	if (!in_array($fieldName, $hiddenFields)) {
		?>
<?=ucfirst($fieldName)?>:
<script type="text/javascript">
function <?=$fieldName?>_anchor_func() {
	inUpdateQuery = false;
	for (var i = 0; i < fieldsInUpdateQuery.length; i++) {
		if (fieldsInUpdateQuery[i] == 'field_<?=$fieldName?>') {
			inUpdateQuery = true;
			break;
		}
	}
	if (inUpdateQuery) {
		document.getElementById('<?=$fieldName?>_anchor').innerHTML = 'open';
		document.getElementById('field_<?=$fieldName?>_container').style.display = 'none';
		fieldsInUpdateQuery.splice(i, 1);
	}
	else {
		document.getElementById('<?=$fieldName?>_anchor').innerHTML = 'close';
		document.getElementById('field_<?=$fieldName?>_container').style.display = 'block'; 		fieldsInUpdateQuery.push('field_<?=$fieldName?>');
	}
	return false;
}
</script>
 <span><a id="<?=$fieldName?>_anchor" href="" onclick="return <?=$fieldName?>_anchor_func()">open</a><br/><?
	}
	?>
	<div id="field_<?=$fieldName?>_container" style="display:none;">
	<?
	$type = "text";
	if ($specialFields[$fieldName] != null)
		$type = $specialFields[$fieldName]; 
	
	if ($type == "text_big" || $type == "text_small") {
	?><textarea name="field_<?=$fieldName?>"
		<?= $type == "text_small" ? 'style="height:100px;"' : "" ?>
		id="field_<?=$fieldName?>"
		onchange="if (this.value.length > 1500) {alert(this.name + ' is being shortened to 1500 characters long.'); this.value = this.value.slice(0,1500);}"
		></textarea>
	<?}
	else if ($type == "checkbox") {
	?>
	<input id="checkbox_<?=$fieldName?>"
		onclick="checkFieldCheckbox(this, 'field_<?=$fieldName?>')"
		type="checkbox"/>
	<input type="hidden"
		name="field_<?=$fieldName?>"
		id="field_<?=$fieldName?>"

		value=""/>
	<?
	}

	else {
	?><input type="<?=$type?>"
		name="field_<?=$fieldName?>"
		id="field_<?=$fieldName?>"
		maxlength="80"
		value=""/><?
	}
	?></div>
	<?
}
?>
<input type="hidden" name="fieldsInUpdateQuery" id="fieldsInUpdateQuery" value=""/>
<input type="submit" name="submitEdit" value="Save Changes" onclick="prepareToSubmitRooms()"/>
</div>

<!-- CREATE TAB -->

<div id="create" style="display: none;" name="tab">
Create New Rooms<br/><br/>
Select the locations where you would like
to create new rooms then click the button below.<br/><br/>
<input type="checkbox" checked="checked" name="createLinks"/> Automatically link new rooms to their adjacent rooms<br/><br/>
<input type="submit" name="submitCreate" value="Create New Rooms" onclick="prepareToSubmitNonRooms()"/>
</div>

<!-- DELETE TAB -->

<div id="delete" style="display: none;" name="tab">
Delete Rooms<br/><br/>
Select the rooms you would like to delete
then click the button below.<br/><br/>
<input type="submit" name="submitDelete" value="Delete Rooms" onclick="if (!confirm('Are you sure you want to delete these rooms?')) return true; prepareToSubmitRooms()"/>
</div>

<div id="view" name="tab">
Navigation<br/>

<table style="width: 300px;">
<tr>
<td/><td/><td/>
<td><a href="" onclick="document.getElementById('y').value = <?=$startY + $mapSize?>;document.dashboard.submit();return false;">N <?=$mapSize?></a></td>
<td/><td/><td/>
</tr>
<tr>
<td/><td/><td/>
<td><a href="" onclick="document.getElementById('y').value = <?=$startY + ($mapSize / 2)?>;document.dashboard.submit();return false;">N <?=$mapSize / 2?></a></td>
<td/>
<td><a href="" onclick="document.getElementById('z').value = <?=$z + 1?>;document.dashboard.submit();return false;">Up</a></td>
<td/>
</tr>
<tr>
<td/><td/><td/>
<td><a href="" onclick="document.getElementById('y').value = <?=$startY + 1?>;document.dashboard.submit();return false;">N 1</a></td>
<td/><td/><td/>
</tr>
<tr>
<td><a href="" onclick="document.getElementById('x').value = <?=$startX - $mapSize?>;document.dashboard.submit();return false;">W <?=$mapSize?></a></td>
<td><a href="" onclick="document.getElementById('x').value = <?=$startX - ($mapSize / 2)?>;document.dashboard.submit();return false;">W <?=$mapSize / 2?></a></td>
<td><a href="" onclick="document.getElementById('x').value = <?=$startX - 1?>;document.dashboard.submit();return false;">W 1</a></td>
<td/>
<td><a href="" onclick="document.getElementById('x').value = <?=$startX + 1?>;document.dashboard.submit();return false;">E 1</a></td>
<td><a href="" onclick="document.getElementById('x').value = <?=$startX + ($mapSize / 2)?>;document.dashboard.submit();return false;">E <?=$mapSize / 2?></a></td>
<td><a href="" onclick="document.getElementById('x').value = <?=$startX + $mapSize?>;document.dashboard.submit();return false;">E <?=$mapSize?></a></td>
</tr>
<tr>
<td/><td/><td/>
<td><a href="" onclick="document.getElementById('y').value = <?=$startY - 1?>;document.dashboard.submit();return false;">S 1</a></td>
<td/><td/><td/>
</tr>
<tr>
<td/><td/><td/>
<td><a href="" onclick="document.getElementById('y').value = <?=$startY - ($mapSize / 2)?>;document.dashboard.submit();return false;">S <?=$mapSize / 2?></a></td>
<td/>
<td><a href="" onclick="document.getElementById('z').value = <?=$z - 1?>;document.dashboard.submit();return false;">Down</a></td>
<td/>
</tr>
<tr>
<td/><td/><td/>
<td><a href="" onclick="document.getElementById('y').value = <?=$startY - $mapSize?>;document.dashboard.submit();return false;">S <?=$mapSize?></a></td>
<td/><td/><td/>
</tr>
<tr>
</table>

<hr/>

Jump to<br/> 
x <input style="width: 3em;" id="x" type="text" name="startX" value="<?=$startX?>"/>
y <input style="width: 3em;" id="y" type="text" name="startY" value="<?=$startY?>"/>
z <input style="width: 3em;" id="z" type="text" name="z" value="<?=$z?>"/>
<select name="plane_id" value="<?=$p?>"/>
<?
$sql = "SELECT id, name FROM $tableAreas ORDER BY coordinate desc";
$result = mysql_query($sql) or die("Could not get areas");
while ($row = mysql_fetch_assoc($result)) {
?><option value="<?=$row['id']?>" <?= $p == $row['id'] ? 'selected="selected"' : ''?>><?=$row['name']?></option><?
}
?>
</select>
<input type="submit" value="Go!"/>
<hr/>

Options<br/>
Map Size
<select name="mapSize">
<option value="10" <?=$mapSize == 10 ? 'selected="selected"' : ''?>>Small (100 rooms)</option>
<option value="20" <?=$mapSize == 20 ? 'selected="selected"' : ''?>>Medium (400 rooms)</option>
<option value="30" <?=$mapSize == 30 ? 'selected="selected"' : ''?>>Large (900 rooms)</option>
<option value="40" <?=$mapSize == 40 ? 'selected="selected"' : ''?>>Jumbo (1600 rooms)</option>
</select>
<br/>
Room Display
<select name="roomSize">
<option value="10" <?=$roomSize == 10 ? 'selected="selected"' : ''?>>Small (10 pixels)</option>
<option value="25" <?=$roomSize == 25 ? 'selected="selected"' : ''?>>Medium (25 pixels)</option>
<option value="50" <?=$roomSize == 50 ? 'selected="selected"' : ''?>>Large (50 pixels)</option>
<option value="100" <?=$roomSize == 100 ? 'selected="selected"' : ''?>>Jumbo (100 pixels)</option>
</select>
<br/>
Show Rooms Above (red) <input type="checkbox" name="showAbove" <?=$showAbove ? 'checked="checked"' : ''?>/><br/>
Show Rooms Below (blue) <input type="checkbox" name="showBelow" <?=$showBelow ? 'checked="checked"' : ''?>/><br/>
Show Undescribed (yellow) <input type="checkbox" name="showUndescribed" <?=$showUndescribed ? 'checked="checked"' : ''?>/><br/>
<input type="submit" value="Save Options"/>
<hr/>
<?
$sql = "SELECT count(id) count,
		(max(x) - min(x) + 1) width,
		(max(y) - min(y) + 1) height,
		(max(z) - min(z) + 1) altitude
	FROM $tablePlaces WHERE plane_id=$p";
$result = mysql_query($sql) or die("Could not get width/height/altitude of area");
echo "Info<br/>";
while ($row = mysql_fetch_assoc($result)) {
	echo "Area room count: " . $row["count"] . "<br/>";
	echo "Area height: " . $row["height"] . "<br/>";
	echo "Area width: " . $row["width"] . "<br/>";
	echo "Area depth: " . $row["altitude"] . "<br/>";

}

// Time to load page
$finish = time();
$duration = ($finish - $start);
if ($duration != 1)	
	echo "Page load time: $duration seconds.";
else
	echo "Page load time: $duration second.";

echo "<hr/>Links<br/>";
echo '<a href="info.php">Usage History</a>';

?>

</div>

<!-- LINK TAB -->

<div id="link" style="display: none;" name="tab">

This tab is for editing links between rooms.<br/><br/>
I want to...<br/>
<input id="linkField" type="radio" name="link" value="link" checked="checked"
    onclick="editDirMode=false;disableLinkFields('linkFieldOptions');"/>
    Link<br/>
<input id="unlinkField" type="radio" name="link" value="unlink"
    onclick="editDirMode=false;disableLinkFields('unlinkFieldOptions');"/>
    Unlink<br/>
<input id="editLinkField" type="radio" name="link" value="edit"
    onclick="editDirMode=true;disableLinkFields('editLinkFieldOptions');showDirFieldValues();"/>
    Edit<br/><br/>


In these directions...
<table><tr><td/><td>
N<input type="checkbox" name="linkNorth" onclick="showDirFieldValues()"/>
</td><td/><td>
U<input type="checkbox" name="linkUp" onclick="showDirFieldValues()"/>
</td>
</tr>
<tr><td>
W<input type="checkbox" name="linkWest" onclick="showDirFieldValues()"/>
</td><td/><td>
E<input type="checkbox" name="linkEast" onclick="showDirFieldValues()"/>
</td><td/></tr>
<tr><td/><td>
S<input type="checkbox" name="linkSouth" onclick="showDirFieldValues()"/>
</td><td/><td>
D<input type="checkbox" name="linkDown" onclick="showDirFieldValues()"/>
</td></tr></table><br/>

...from the selected rooms.<br/><br/>

<div id="linkFieldOptions" style="display:block;">
<input type="checkbox" name="createRoomsFromLinks"/>Create rooms when linking to empty space
</div>

<div id="unlinkFieldOptions" style="display:none;">

</div>

<div id="editLinkFieldOptions" style="display:none;">
<table>
<?

foreach ($dirFields as $field) {
	echo "<tr><td>" . $field["label"] . "</td>";
	echo '<td><input type="' . $field["inputType"] . '" ';
	echo 'name="' . $field["dbField"] . '" ';
	echo 'id="' . $field["dbField"] . '">';
	echo "</input></td>";
	echo "</tr>";
}
?>
</table>

</div>
<br/>

<input type="submit" name="submitLink" onclick="prepareToSubmitRooms()" value="Do it!"/>
</div>

<input type="hidden" name="ids" id="ids" value=""/>
<input type="hidden" name="positions" id="positions" value=""/>
</form>
<hr/>
</div>

<div style="height: 160px;
		width: 220px;
		font-size: 9px;
		font-face: sans;
		border: dashed gray 1px;
		background-color: white;
		display: none;
		overflow: hidden;
		z-index: 9999;
		position: absolute;"
	id="info">
info!
</div>

<script type="text/javascript">
doneLoading = true;
</script>



</body>
</html>
