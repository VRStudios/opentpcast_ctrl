<?php
	header('Content-Type: application/json');

	// API to issue commands to TPCast
	switch($_GET["ctrl"])
	{
		case "camera_status": {
			$response = array('response' => 'camerastatus', 'status' => (exec("sudo opentpcast-ctrl camera status") === "1" ? 'started' : 'stopped'));
			exit(json_encode($response));
			break;
		}

		case "camera_start": {
			exec("sudo opentpcast-ctrl camera start");
			$response = array('response' => 'camerastarted', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . ':8080/?action=stream');
			exit(json_encode($response));
			break;
		}

		case "camera_stop": {
			exec("sudo opentpcast-ctrl camera stop");
			$response = array('response' => 'camerastopped');
			exit(json_encode($response));
			break;
		}

		case "info_opentpcast": {
			$response = array('response' => 'info', 'type' => 'opentpcast', 'name' => 'OpenTPCast', 'version' => exec("sudo opentpcast-ctrl version"), 'kernel' => php_uname('r'), 'arch' => php_uname('m'));
			exit(json_encode($response));
			break;
		}

		case "info_virtualhere": {
			$licenseVirtualHere = exec("sudo opentpcast-ctrl virtualhere license");
			$response = array('response' => 'info', 'type' => 'virtualhere', 'name' => 'VirtualHere USB Server', 'version' => exec("sudo opentpcast-ctrl virtualhere version"), 'status' => (exec("sudo opentpcast-ctrl virtualhere status") === "1" ? 'started' : 'stopped'), 'licence' => ($licenseVirtualHere ? $licenseVirtualHere : ''));
			exit(json_encode($response));
			break;
		}
	}

	$response = array('response' => 'invalidrequest');
	exit(json_encode($response));
?>
