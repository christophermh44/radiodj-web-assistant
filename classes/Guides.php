<?php

class Guides {
	private static $guides;
	private static $q;

	public static function static_init() {
		self::$guides = Data::listFrom('guides');
		self::$q = Query::getInstance();
	}

	public static function getFileName($guide, $ext) {
		$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
		$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
		$name = mb_strtolower($guide['title']);
		$name = str_replace($search, $replace, $name);
		$name = preg_replace('#[^a-zA-Z0-9_]#i', '_', $name);
		$date = date('Ymd', $guide['date']);
		$time = date('His', strtotime($guide['template']['start']));
		return $date.'.'.$time.'.'.$name.'.'.$ext;
	}

	public static function getList($limit = null) {
		if ($limit !== null) {
			$list = self::$guides;
			uasort($list, function($left, $right) {
				$ldate = date('Ymd', $left['date'] + 3600).date('His', strtotime($left['template']['start']) + 3600);
				$rdate = date('Ymd', $right['date'] + 3600).date('His', strtotime($right['template']['start']) + 3600);
				return ($ldate < $rdate ? 1 : -1);
			});
			$list = array_slice($list, 0, $limit);
			return $list;
		} else {
			return self::$guides;
		}
	}

	public static function exists($id) {
		return is_file(__DIR__.'/../data/guides/'.$id.'.json');
	}

	public static function create($tid) {
		$id = uniqid();
		$guide = [
			'title' => '',
			'details' => '',
			'template' => Templates::load($tid),
			'date' => 0,
			'lines' => []
		];
		$guide = self::generate($guide);
		self::save($id, $guide);
		return $id;
	}

	public static function duplicate($gid) {
		if (!self::exists($gid)) return null;
		$guide = self::load($gid);
		$id = uniqid();
		$guide['title'].= ' (Duplication)';
		self::save($id, $guide);
		return $id;
	}

	public static function reset() {
		$list = self::getList();
		foreach ($list as $key => $value) {
			self::remove($key);
		}
	}

	public static function remove($id) {
		unlink(__DIR__.'/../data/guides/'.$id.'.json');
	}

	public static function load($id) {
		return Data::get('guides/'.$id);
	}

	public static function save($id, $contents) {
		Data::set('guides/'.$id, $contents);
	}

	private static function generate($guide) {
		$history = [];
		$lines = $guide['template']['lines'];
		$start = self::getTimestampFromIsoDate($guide['template']['start']);
		foreach ($lines as $model) {
			$newLine = self::generateLine($model, $start, $history);
			if (!is_string($newLine['additionnals'])) {
				$history[] = [
					'start' => $start,
					'ID' => $newLine['ID']
				];
			}
			$start+= $newLine['duration'];
			$guide['lines'][] = $newLine;
		}
		return $guide;
	}

	private static function getSongEntry($id) {
		return Songs::getById($id);
	}

	private static function generateLine($model, $start, $history) {
		$line = [
			'ID' => null,
			'start' => $start,
			'designation' => $model['params']['topic'],
			'duration' => 0,
			'ressource' => null,
			'additionnals' => ''
		];
		$durationType = $model['itemType'];
		if ($durationType == 'fixed') {
			$line['duration'] = self::getTimestampFromIsoDate($model['duration']);
		} else if ($durationType == 'elastic') {
			$line['duration'] = self::getTimestampFromIsoDate($model['params']['endsAt']) - $start;
		} 
		$ressourceType = $model['itemRessource'];
		if ($ressourceType  == 'fixed') {
			$line['ID'] = $model['params']['choosen']['ID'];
			$line['duration'] = self::getTimestampFromIsoDate($model['duration']);
			$line['additionnals'] = self::getSongEntry($model['params']['choosen']['ID']);
			$line['locked'] = true;
		} else if ($ressourceType  == 'query') {
			$output = Queries::process($model, $start, $history);
			if ($output == null) {
				$line['additionnals'] = '- '.T::ranslate('No song could be selected').' -';
			} else {
				$song = Songs::getById($output->ID);
				$line['ID'] = $song->ID;
				$line['additionnals'] = $song;
			}
			if ($durationType == 'driven') {
				$line['duration'] = intval($line['additionnals']->duration);
			}
			$line['locked'] = true;
		}
		return $line;
	}

	private static function getTimestampFromIsoDate($date) {
		$timestamp = date("U", strtotime($date));
		return intval($timestamp);
	}
}
