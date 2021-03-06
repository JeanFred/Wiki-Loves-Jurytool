<?php session_start(); 
/**
 * Wiki Loves Jurytool jury
 *
 * @author Ruben Demus
 * @copyright 2015 Ruben Demus
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once "../config/config.php"; // config stuff
require_once "../lib/lib.php"; // file functions
require_once "../l10n/".$config['language'].".php"; // lang

require_once "../lib/login.php"; // login functions
require_once "../lib/jury/menue.php"; // prejury menue
require_once "../lib/jury/vote.php"; // vote menue
require_once "../lib/jury/next_foto.php"; // prejury menue


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $config['language']; ?>" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $config['title'] . " - " . $text['jury']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="../theme/styles.css">

<?php

$menue = $uploader = $settings = "";
$log = "-";

// mysql
$db = new mysqli($config['dbhost'], $config['dbuser'], $config['dbpassword'], $config['dbname']);

if ($db->connect_error)
{
	// log error
	if($config['log']!="NO")
	{
		append_file("../log/jury.txt","\n".date(DATE_RFC822)."\tdb connect_error\tmain()");
	}
}
else
{
	$log = login($db,"");

	if($log == "OK")
	{
		// user settings width
		if(isset($_GET["width"]))
		{
			$width = $db->real_escape_string($_GET['width']);
			$_SESSION['width'] = $width;
			$user = $db->real_escape_string($_SESSION['us']);

			$sql = "UPDATE `" . $config['dbprefix'] . "jury` SET `width`='".$width."' WHERE `lname`= '".$user."'";
			$db->query($sql);
		}
		
		// menue
		// set hide menue
		if(isset($_GET["h"]))
		{
			$_SESSION['h']=$_GET["h"];
		}
		$menue = menue();
		$log = menue2();

		// user and programm
		$user = $db->real_escape_string($_SESSION['us']);
		$programm = "";
		if(isset($_GET["p"]))
		{
			$programm = $_GET["p"];
		}
		
		// save vote
		vote($db);

		// which fotos & display
		$uploader = next_foto($db, $programm);
		
		// menue with settings
		$settings = menue3();
		
	} // end logged in

	$db->close();
}

?>

</head>
<body>
	<center>
<?php

// if menue
if(!isset($_SESSION['h']) || ($_SESSION['h']!=1))
{
?>
	<table border=0 cellpadding=0px width=1000px style="text-align: center;">
		<tr>
			<td width=200><img src="<?php echo $config["logo"]; ?>" width="100"></td>
			<td valign=top width=600>
				<br><?php echo $config["title"] . " <b>" . $text["jury"]; ?></b><br>

<?php
	echo $menue;
?>

			</td>
			<td width=200> <?php echo $log; ?>
	</table>

	<p>
	<?php
	echo $uploader;

}
else // no menue
{
	echo $uploader;
} // end if no menue
?>

<?php echo $settings; ?>

<p><?php echo $text['footer'];?>, <a href="https://www.gnu.org/licenses/agpl-3.0.en.html">GNU AGPL v3</a> </p>

</center>

</body>
