<?php
if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
	echo file_get_contents('.flag');
} else {
	echo 'IP "' . $_SERVER['REMOTE_ADDR'] . '" forbidden !';
}
?>