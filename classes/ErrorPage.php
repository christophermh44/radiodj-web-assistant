<?php

class ErrorPage {
	public static function static_init() {
	}
	
	public static function send($message) {
		$_SESSION['error_message'] = $message;
		header('Location: error.php');
	}

	public static function receive() {
		return $_SESSION['error_message'];
	}
}