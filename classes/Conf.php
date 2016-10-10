<?php
class Conf {
	private static $vars = [];

	public static function static_init() {
		self::$vars = Data::get('settings');
	}

	public static function exists($key) {
		return array_key_exists($key, self::$vars);
	}

	public static function get($key) {
		return self::exists($key) ? self::$vars[$key] : null;
	}

	public static function set($key, $value) {
		self::$vars[$key] = $value;
		Data::set('settings', self::$vars);
	}

	public static function reset() {
		self::set("radio_name", 'Radio');
		self::set("language", 'en');
		self::set("db_base", 'radiodj');
		self::set("db_host", '127.0.0.1');
		self::set("db_port", 3306);
		self::set("db_user", 'root');
		self::set("db_pass", 'changeme');
		self::set("ftp_host", '127.0.0.1');
		self::set("ftp_port", 21);
		self::set("ftp_user", 'radiodj');
		self::set("ftp_pass", 'changeme');
		self::set("ftp_ssl", 0);
		self::set("ftp_bind", '/');
		self::set("mc_channel", 'your-radio');
		self::set("mc_clientid", '1234');
		self::set("mc_clientsecret", 'abcd');
	}
}