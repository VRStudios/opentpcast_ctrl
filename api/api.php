<?php
	header('Content-Type: application/json');

	// API to issue commands to TPCast
	switch($_GET["ctrl"])
	{
		case "camerastatus": {
			$response = array('response' => 'camerastatus', 'status' => (exec("sudo opentpcast-ctrl camera status") === "1" ? 'started' : 'stopped'));
			exit(json_encode($response));
			break;
		}

		case "camerastart": {
			exec("sudo opentpcast-ctrl camera start");
			$response = array('response' => 'camerastarted', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . ':8080/?action=stream');
			exit(json_encode($response));
			break;
		}

		case "camerastop": {
			exec("sudo opentpcast-ctrl camera stop");
			$response = array('response' => 'camerastopped');
			exit(json_encode($response));
			break;
		}
	}

	$response = array('response' => 'invalidrequest');
	exit(json_encode($response));
?>
