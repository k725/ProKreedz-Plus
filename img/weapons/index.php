<?php
	header('Content-type: image/gif');
	if(!empty($_GET['w'])) {
		if ($_GET['w'] == 'usp') {
			readfile('usp.gif');
		} else if ($_GET['w'] == 'knife') {
			readfile('knife.gif');
		} else if ($_GET['w'] == 'scout') {
			readfile('scout.gif');
		} else {
			readfile('other.gif');
		}
	} else {
		readfile('other.gif');
	}