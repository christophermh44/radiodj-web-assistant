<?php include_once '../init.php'; ?>

window.TR = function(key) {
	window.MESSAGES = <?= json_encode(T::getMessages()) ?>;
	if (window.MESSAGES.hasOwnProperty(key)) {
		return window.MESSAGES[key];
	} else {
		return key;
	}
}