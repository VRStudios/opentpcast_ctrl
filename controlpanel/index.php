<?php
	// Disable Caching
	header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang=en>
	<head>
		<meta charset=utf-8>
		<meta name="author" content="OpenTPCast">
		<title>OpenTPCast - Control Panel</title>
		<style>
			body {
				margin: 0px;
				padding: 0px;
				height: 100%;
				font-family: sans-serif;
			}

			#container {
				min-height: 100%;
			}

			#navigation {
				float: left;
				width: 200px;
				padding: 10px;
				height: 100%;
				margin-right: 100px;
			}

			#content {
				float: left;
				padding: 10px;
			}

			#footer {
				position: absolute;
				bottom: 0px;
				width: 100%;
				height: 40px;
				background-color: #D92B4B;
				color: white;
				text-align: center;
				line-height: 40px;
				font-size: 0.8em;
				font-weight: bold;
			}

			#footer a {
				color: white;
			}

			select {
				display: block;
				width: 100%;
				padding: 16px 20px;
				border: none;
				border-radius: 4px;
				background-color: #f1f1f1;
			}

			input[type=button], input[type=submit], input[type=reset] {
				background-color: #D92B4B;
				color: white;
				border: none;
				padding: 16px 32px;
				text-decoration: none;
				margin: 8px 4px;
				cursor: pointer;
			}

			input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover {
				background-color: #E87D91;
				-webkit-transition: 0.1s;
				transition: 0.1s;
			}

			/* Firefox: Disable spinner */
			input[type='number'] {
				-moz-appearance:textfield;
			}

			/* Chrome: Disable spinner */
			input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}

			input[type=text], input[type=number] {
				display: block;
				width: 100%;
				padding: 12px 20px;
				margin: 8px 0;
				box-sizing: border-box;
				border: 3px solid #ccc;
				-webkit-transition: 0.1s;
				transition: 0.1s;
				outline: none;
			}

			input[type=text]:focus, input[type=number]:focus {
				border: 3px solid #555;
			}

			input[type=button]:disabled, input[type=submit]:disabled, input[type=reset]:disabled {
				background-color: #ccc;
			}

			#navigation > h1 {
				font-size: 1.2em;
				padding: 0px;
				margin: 0px 0px 10px 0px;
				width: 267px;
				height: 79px;
				text-indent: -9999px;
				background-image: url("opentpcast-logo.png");
			}

			#navigation ul {
				margin: 0px;
				padding: 0px;
				list-style: none;
			}

			#navigation li a {
				display: block;
				width: 100%;
				background-color: #D92B4B;
				border: none;
				border-bottom: 1px solid #FFFFFF;
				color: white;
				text-decoration: none;
				padding: 16px 32px;
				cursor: pointer;
			}

			#navigation li a:hover {
				background-color: #E87D91;
				-webkit-transition: 0.1s;
				transition: 0.1s;
			}

			#settings-camera .running, #settings-virtualhere .running {
				color: #0D8C36;
				font-weight: bold;
			}

			#settings-camera .notrunning, #settings-virtualhere .notrunning {
				color: #D92B4B;
				font-weight: bold;
			}

			#settings-general, #settings-camera, #settings-virtualhere {
				display: none;
			}

			input[name=updatevirtualhere] {
				width: 300px;
			}

			.blur {
				filter: blur(5px);
			}

			.blur:hover {
				filter: none;
			}
		</style>
		<script>
			function editedConf() {
				var form = document.getElementsByName("editconf")[0];
				form.querySelectorAll("[name=saveconf]")[0].disabled = false;
				form.querySelectorAll("[name=saveconfreboot]")[0].disabled = false;
				form.querySelectorAll("[name=confdefault]")[0].disabled = false;
			}

			function loadConfDefaults() {
				var form = document.getElementsByName("editconf")[0];
				form.querySelectorAll("[name=ssid]")[0].value = "";
				form.querySelectorAll("[name=passphrase]")[0].value = "";
				form.querySelectorAll("[name=confdefault]")[0].disabled = true;
				form.querySelectorAll("[name=saveconf]")[0].disabled = false;
				form.querySelectorAll("[name=saveconfreboot]")[0].disabled = false;
			}

			function editedCameraConf() {
				var form = document.getElementsByName("editcameraconf")[0];
				form.querySelectorAll("[name=savecameraconf]")[0].disabled = false;
				form.querySelectorAll("[name=cameraconfdefault]")[0].disabled = false;
			}

			function loadCameraConfDefaults() {
				var form = document.getElementsByName("editcameraconf")[0];
				form.querySelectorAll("[name=camerareswidth]")[0].value = "480";
				form.querySelectorAll("[name=cameraresheight]")[0].value = "360";
				form.querySelectorAll("[name=cameraframerate]")[0].value = "30";
				form.querySelectorAll("[name=cameraconfdefault]")[0].disabled = true;
				form.querySelectorAll("[name=savecameraconf]")[0].disabled = false;
			}

			<?php
				$versionVirtualHere = exec("sudo opentpcast-ctrl virtualhere version");
			?>

			var latestVersionVirtualHere;

			var progressindicator = "...";
			var inprogress = false;

			setInterval(function() {
				progressindicator = progressindicator.length >= 3 ? "" : progressindicator + ".";

				if(inprogress) {
					var form = document.getElementsByName("vhupdate")[0];
					form.querySelectorAll("[name=updatevirtualhere]")[0].value = form.querySelectorAll("[name=updatevirtualhere]")[0].value.replace(/\.+$/, "") + progressindicator;
				}
			}, 750);

			function checkVirtualHereServerVersion() {
				var form = document.getElementsByName("vhupdate")[0];
				form.querySelectorAll("[name=updatevirtualhere]")[0].disabled = true;
				form.querySelectorAll("[name=updatevirtualhere]")[0].value = "Checking For Updates" + progressindicator;
				inprogress = true;

				var request = new XMLHttpRequest();
				request.onreadystatechange = function() {
					if(request.readyState === 4) {
						if(request.status === 0 || request.status === 200) {
							latestVersionVirtualHere = request.response.substring(request.response.indexOf("<virtualhere_server_version>") + 28, request.response.indexOf("</virtualhere_server_version>"));

							var updateAvailable = (latestVersionVirtualHere && latestVersionVirtualHere !== "<?php echo $versionVirtualHere; ?>");
							form.querySelectorAll("[name=updatevirtualhere]")[0].disabled = false;
							form.querySelectorAll("[name=updatevirtualhere]")[0].value = updateAvailable ? ("Install Latest Version (" + latestVersionVirtualHere + ")") : "No Updates Found";
							inprogress = false;
							if(updateAvailable) form.querySelectorAll("[name=updatevirtualhere]")[0].onclick = updateVirtualHere;
						}
					}
				}

				request.open("GET", "https://www.virtualhere.com/latest_version", true);
				request.send(null);
			}

			function updateVirtualHere() {
				var form = document.getElementsByName("vhupdate")[0];
				form.querySelectorAll("[name=updatevirtualhere]")[0].disabled = true;
				form.querySelectorAll("[name=updatevirtualhere]")[0].value = "Downloading Update" + progressindicator;
				inprogress = true;

				var request = new XMLHttpRequest();
				request.responseType = "blob";
				request.onreadystatechange = function() {
					if(request.readyState === 4) {
						if(request.status === 0 || request.status === 200) {
							form.querySelectorAll("[name=updatevirtualhere]")[0].disabled = true;
							form.querySelectorAll("[name=updatevirtualhere]")[0].value = "Installing Update" + progressindicator;
							inprogress = true;

							var formdata = new FormData(form);
							formdata.append("vhserverfile", request.response, "vhusbdtpcast");

							var uploadrequest = new XMLHttpRequest();
							uploadrequest.onreadystatechange = function() {
								if(uploadrequest.readyState === 4) {
									if(uploadrequest.status === 0 || uploadrequest.status === 200) {
										form.querySelectorAll("[name=updatevirtualhere]")[0].value = "Finishing Installation" + progressindicator;
										inprogress = true;
										location.reload();
									}
								}
							}

							uploadrequest.open("POST", "index.php");
							uploadrequest.send(formdata);
						}
					}
				}

				request.onprogress = function(event) {
					if(event.lengthComputable) {
						form.querySelectorAll("[name=updatevirtualhere]")[0].value = "Downloading Update (" + Math.floor(event.loaded / event.total * 100) + "%)" + progressindicator;
						inprogress = true;
					}
				}

				request.open("GET", "https://www.virtualhere.com/sites/default/files/usbserver/vhusbdtpcast", true);
				request.send(null);
			}

			function showContent(id) {
				var contentpanes = document.getElementById("content").querySelectorAll("[id^='settings-']");
				for(var i = 0; i < contentpanes.length; ++i) {
					contentpanes[i].style.display = "none";
				}

				if(!id && window.location.hash && window.location.hash.startsWith("#settings-")) id = window.location.hash.substring(1);
				if(!id) id = "settings-general";

				document.getElementById(id).style.display = "block";

				if(id === 'settings-virtualhere') checkVirtualHereServerVersion();
			}

			function cameraFullscreenPreview() {
				var el = document.getElementById("camerapreview");
				if(el && (document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled)) (el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen).bind(el)();
			}
		</script>
	</head>
	<body onload="showContent()" onhashchange="showContent()">
		<?php
			// Read configuration file
			$conf_file_path = "/boot/opentpcast.txt";
			$conf = array();

			$line = strtok(file_get_contents($conf_file_path), "\r\n");
			while($line !== false) {
				$line = ltrim($line);
				if($line[0] !== "#") {
					$pair = explode("=", $line);
					$conf[$pair[0]] = $pair[1];
				}

				$line = strtok("\r\n");
			}

			// Create template configuration with defaults
			$confTemplate = array(
				'network' => array(
					'title' => 'Network Settings',
					'description' => 'Specifies which Wi-Fi network to prioritize connecting the TPCast to.  If left blank, the default configuration will be used.  An empty file called "initwlan" must be created in the boot folder after updating these settings for the changes to take effect on the next boot.',
					'properties' => array(
						'ssid' => array('default' => 'tpcastXXXX', 'value' => ''),
						'passphrase' => array('default' => '12345678', 'value' => '')
					)
				),
				'camera' => array(
					'title' => 'Camera Settings',
					'description' => 'Specifies the resolution and frame rate that the camera service will stream at.  If left blank, the default configuration will be used. ',
					'properties' => array(
						'camerareswidth' => array('default' => '480', 'value' => ''),
						'cameraresheight' => array('default' => '360', 'value' => ''),
						'cameraframerate' => array('default' => '30', 'value' => '')
					)
				)
			);

			// Update template configuration with current values
			foreach($confTemplate as &$section) {
				foreach($section['properties'] as $propname => &$property) {
					if(array_key_exists($propname, $conf)) $property['value'] = $conf[$propname];
				}

				unset($property);
			}

			unset($section);

			function writeOpenTPCastConfig($confTemplate) {
				/*
				# Network Settings
				# Specifies which Wi-Fi network to prioritize connecting the TPCast to.  If left blank, the default configuration will be used.  An empty file called "initwlan" must be created in the boot folder after updating these settings for the changes to take effect on the next boot.
				# ssid=tpcastXXXX
				# passphrase=12345678
				ssid=
				passphrase=

				# Camera Settings
				# Specifies the resolution and frame rate that the camera service will stream at.  If left blank, the default configuration will be used. 
				# camerareswidth=480
				# cameraresheight=360
				# cameraframerate=30
				camerareswidth=
				cameraresheight=
				camframerate=
				*/
				$conffile = fopen("opentpcast.txt", "w") or die("Failed to open OpenTPCast configuration file for writing!");

				foreach($confTemplate as $sectionkey => $section) {
					if($section['title'] && $section['title'] !== '') fwrite($conffile, "# " . $section['title'] . "\n");
					if($section['description'] && $section['description'] !== '') fwrite($conffile, "# " . $section['description'] . "\n");

					foreach($section['properties'] as $propname => $property) {
						fwrite($conffile, "# " . $propname . "=" . $property['default'] . "\n");
					}

					foreach($section['properties'] as $propname => $property) {
						fwrite($conffile, $propname . "=" . $property['value'] . "\n");
					}

					if(end(array_keys($confTemplate)) !== $sectionkey) fwrite($conffile, "\n");
				}

				fclose($conffile);
				exec("sudo opentpcast-ctrl applyconfig");
			}

			if($_POST["saveconf"] || $_POST["saveconfreboot"]) {
				$confTemplate["network"]["properties"]["ssid"]["value"] = str_replace(array("\r", "\n"), '', htmlspecialchars_decode($_POST["ssid"]));
				$confTemplate["network"]["properties"]["passphrase"]["value"] = str_replace(array("\r", "\n"), '', htmlspecialchars_decode($_POST["passphrase"]));

				writeOpenTPCastConfig($confTemplate);
				exec("sudo opentpcast-ctrl initwlan");
				if($_POST["saveconfreboot"]) exec("sudo opentpcast-ctrl reboot");
			} else if($_POST["savecameraconf"]) {
				$_POST["camerareswidth"] = (int)htmlspecialchars_decode($_POST["camerareswidth"]);
				if($_POST["camerareswidth"] <= 0) $_POST["camerareswidth"] = "";

				$_POST["cameraresheight"] = (int)htmlspecialchars_decode($_POST["cameraresheight"]);
				if($_POST["cameraresheight"] <= 0) $_POST["cameraresheight"] = "";

				$_POST["cameraframerate"] = (int)htmlspecialchars_decode($_POST["cameraframerate"]);
				if($_POST["cameraframerate"] <= 0) $_POST["cameraframerate"] = "";

				$confTemplate["camera"]["properties"]["camerareswidth"]["value"] = $_POST["camerareswidth"];
				$confTemplate["camera"]["properties"]["cameraresheight"]["value"] = $_POST["cameraresheight"];
				$confTemplate["camera"]["properties"]["cameraframerate"]["value"] = $_POST["cameraframerate"];

				writeOpenTPCastConfig($confTemplate);
				exec("sudo opentpcast-ctrl camera reload");
			}

			if($_POST["cameratoggle"]) {
				exec("sudo opentpcast-ctrl camera toggle");
			} else if($_POST["cameraboottoggle"]) {
				exec("sudo opentpcast-ctrl camera boottoggle");
			}

			if($_FILES["vhserverfile"]) {
				move_uploaded_file($_FILES["vhserverfile"]["tmp_name"], "/var/www/html/vhusbdtpcast");
				exec("sudo opentpcast-ctrl virtualhere update");
			}

			$isCameraServiceEnabled = (exec("sudo opentpcast-ctrl camera bootstatus") === "1");
			$isCameraServiceRunning = (exec("sudo opentpcast-ctrl camera status") === "1");

			$isVirtualHereServiceRunning = (exec("sudo opentpcast-ctrl virtualhere status") === "1");
			$licenseVirtualHere = exec("sudo opentpcast-ctrl virtualhere license");
		?>
		<div id="container">
			<div id="navigation">
				<h1>OpenTPCast Control Panel</h1>
				<ul>
					<li><a href="#settings-general">General</a></li>
					<li><a href="#settings-camera">Camera</a></li>
					<li><a href="#settings-virtualhere">VirtualHere USB Server</a></li>
				</ul>
			</div>

			<div id="content">
				<div id="settings-general">
					<h1>General</h1>
					<form name="editconf" method="post" autocomplete="off">
						<h2>Network</h2>
						<label for="ssid">SSID</label><input type="text" oninput="editedConf()" placeholder="Auto-detect" name="ssid"<?php if($confTemplate["network"]["properties"]["ssid"]["value"]) echo " value=\"" . htmlspecialchars($confTemplate["network"]["properties"]["ssid"]["value"]) . "\""; ?>/>
						<label for="passphrase">Passphrase</label><input type="text" oninput="editedConf()" name="passphrase"<?php if($confTemplate["network"]["properties"]["passphrase"]["value"]) echo " value=\"" . htmlspecialchars($confTemplate["network"]["properties"]["passphrase"]["value"]) . "\""; ?>/>
						<input type="submit" name="saveconf" value="Save" disabled /><input type="submit" name="saveconfreboot" value="Save & Reboot TPCast" disabled /><input type="button" name="confdefault" value="Load Defaults" onclick="loadConfDefaults()" />
					</form>
				</div>

				<div id="settings-camera">
					<h1>Camera</h1>
					<h2>Camera Service</h2>
					<p>Service status: <span class="<?php echo $isCameraServiceRunning ? "running" : "notrunning"; ?>"><?php echo $isCameraServiceRunning ? "Running" : "Not Running"; ?></span></p>
					<form name="camservice" method="post" autocomplete="off">
						<input type="submit" name="cameraboottoggle" value="<?php echo $isCameraServiceEnabled ? "Disable" : "Enable" ?> Camera Service" />
						<input type="submit" name="cameratoggle" value="<?php echo $isCameraServiceRunning ? "Stop" : "Start"; ?> Camera Service"<?php if(!$isCameraServiceEnabled) echo " disabled"; ?> />
					</form>
					<?php if($isCameraServiceRunning) echo "\t\t\t\t\t<h3>Preview</h3>\t\t\t\t\t<img id=\"camerapreview\" src=\"http://" , $_SERVER['HTTP_HOST'] , ":8080/?action=stream\" alt=\"Camera Stream\" ondblclick=\"cameraFullscreenPreview()\" />"; ?>
					<h2>Settings</h2>
					<form name="editcameraconf" method="post" autocomplete="off">
						<label for="camerareswidth">Resolution Width</label><input type="number" oninput="editedCameraConf()"<?php if($confTemplate["camera"]["properties"]["camerareswidth"]["default"]) echo " placeholder=\"" . htmlspecialchars($confTemplate["camera"]["properties"]["camerareswidth"]["default"]) . "\""; ?> name="camerareswidth"<?php if($confTemplate["camera"]["properties"]["camerareswidth"]["value"]) echo " value=\"" . htmlspecialchars($confTemplate["camera"]["properties"]["camerareswidth"]["value"]) . "\""; ?>/>
						<label for="cameraresheight">Resolution Height</label><input type="number" oninput="editedCameraConf()"<?php if($confTemplate["camera"]["properties"]["cameraresheight"]["default"]) echo " placeholder=\"" . htmlspecialchars($confTemplate["camera"]["properties"]["cameraresheight"]["default"]) . "\""; ?> name="cameraresheight"<?php if($confTemplate["camera"]["properties"]["cameraresheight"]["value"]) echo " value=\"" . htmlspecialchars($confTemplate["camera"]["properties"]["cameraresheight"]["value"]) . "\""; ?>/>
						<label for="cameraframerate">Frame Rate</label><input type="number" oninput="editedCameraConf()" placeholder="Auto" name="cameraframerate"<?php if($confTemplate["camera"]["properties"]["cameraframerate"]["value"]) echo " value=\"" . htmlspecialchars($confTemplate["camera"]["properties"]["cameraframerate"]["value"]) . "\""; ?>/>
						<input type="submit" name="savecameraconf" value="Save" disabled /><input type="button" name="cameraconfdefault" value="Load Defaults" onclick="loadCameraConfDefaults()" />
					</form>
				</div>

				<div id="settings-virtualhere">
					<h1>VirtualHere USB Server</h1>
					<p>Service status: <span class="<?php echo $isVirtualHereServiceRunning ? "running" : "notrunning"; ?>"><?php echo $isVirtualHereServiceRunning ? "Running" : "Not Running"; ?></span></p>
					<p>License Key: <span class="<?php echo $licenseVirtualHere ? "running blur" : "notrunning"; ?>"><?php echo $licenseVirtualHere ? $licenseVirtualHere : "Unregistered"; ?></span></p>
					<p>Installed Version: <strong><?php echo $versionVirtualHere; ?></strong></p>
					<form name="vhupdate" method="post" autocomplete="off">
						<input type="button" name="updatevirtualhere" value="Check For Updates" onclick="checkVirtualHereServerVersion()" />
					</form>
				</div>
			</div>
		</div>

		<div id="footer">
			Powered by <a href="https://github.com/OpenTPCast/">OpenTPCast</a> - <?php echo php_uname(); ?>
		</div>
	</body>
</html>
