<?php
class Data {
	public static function static_init() {
	}
	
	public static function get($file) {
		return json_decode(file_get_contents(__DIR__.'/../data/'.$file.'.json'), true);
	}

	public static function set($file, $contents) {
		file_put_contents(__DIR__.'/../data/'.$file.'.json', json_encode($contents));
	}

	public static function list($folder) {
		$path = __DIR__.'/../data/'.$folder.'/';
		$files = array_diff(scandir($path), ['.', '..']);
		$list = [];
		foreach ($files as $key => $value) {
			if (!is_dir($path.$value)) {
				$name = str_replace('.json', '', $value);
				$list[$name] = self::get($folder.'/'.$name);
			}
		}
		return $list;
	}
}