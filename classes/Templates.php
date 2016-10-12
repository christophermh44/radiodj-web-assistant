<?php

class Templates {
	private static $templates;

	public static function static_init() {
		self::$templates = Data::listFrom('templates');
	}

	public static function getList($limit = null) {
		if ($limit !== null) {
			$list = self::$templates;
			uasort($list, function($left, $right) {
				$ldate = $left['last_used'];
				$rdate = $right['last_used'];
				return ($ldate < $rdate ? 1 : -1);
			});
			$list = array_slice($list, 0, $limit);
			return $list;
		} else {
			return self::$templates;
		}
	}

	public static function exists($id) {
		return is_file(__DIR__.'/../data/templates/'.$id.'.json');
	}

	public static function create() {
		$id = uniqid();
		self::save($id, [
			'title' => '',
			'description' => '',
			'start' => 0,
			'lines' => []
		]);
		return $id;
	}

	public static function duplicate($id) {
		$newId = self::create();
		$data = self::load($id);
		$data['title'].= ' (Duplication)';
		self::save($newId, $data);
		return $newId;
	}

	public static function reset() {
		$list = self::getList();
		foreach ($list as $key => $value) {
			self::remove($key);
		}
	}

	public static function remove($id) {
		unlink(__DIR__.'/../data/templates/'.$id.'.json');
	}

	public static function load($id) {
		return Data::get('templates/'.$id);
	}

	public static function save($id, $contents) {
		$contents['last_used'] = microtime();
		Data::set('templates/'.$id, $contents);
	}
}
