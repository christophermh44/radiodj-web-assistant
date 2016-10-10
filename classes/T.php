<?php

class T {
	private static $messages;
	private static $current;

	public static function static_init() {
		self::$current = Conf::get('language');
		self::$messages = Data::get('languages/'.self::$current);
	}

	static function getMessages() {
		return self::$messages;
	}

	static function ranslate($message) {
		$out = array_key_exists($message, self::$messages) ? self::$messages[$message] : $message;
		return $out;
	}
}