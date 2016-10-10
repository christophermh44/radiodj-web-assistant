<?php

class Queries {
	private static $queries;

	public static function static_init() {
		self::$queries = Data::get('queries');
	}

	public static function load() {
		return self::$queries;
	}

	public static function save($contents) {
		self::$queries = $contents;
		Data::set('queries', $contents);
	}

	public static function reset() {
		self::save('[]');
	}

	public static function exists($query) {
		return array_key_exists($query, self::$queries );
	}

	public static function getCriteria($query) {
		if (self::exists($query)) {
			return self::$queries[$query];
		} else {
			return null;
		}
	}

	public static function isInHistory($choosen, $history, $start, $sard, $ssrd) {
		// empty history
		if (count($history) == 0) {
			return false;
		}
		// timestamp after when selection is not allowed
		$lastArtistPoint = $start - $sard * 60;
		$lastTitlePoint = $start - $ssrd * 60;
		// choosen song
		$choosenArtist = $choosen->artist;
		$choosenTitle = $choosen->title;
		$songsFromHistory = [];
		foreach ($history as $key => $value) {
			$songsFromHistory[$value['ID']] = [
				'song' => Songs::getById($value['ID']),
				'start' => $value['start']
			];
		}
		// nothing inside history that can be treated
		if ($songsFromHistory == null || count($songsFromHistory) == 0) {
			return false;
		}
		// Getting too recent songs
		$filteredHistory = array_filter($songsFromHistory, function($item) use ($choosenArtist, $choosenTitle, $lastArtistPoint, $lastTitlePoint) {
			if (strcasecmp($item['song']->artist, $choosenArtist)) {
				if ($item['start'] > $lastArtistPoint) {
					return true;
				} else {
					if (strcasecmp($item['song']->title, $choosenTitle)) {
						if ($item['start'] > $lastTitlePoint) {
							return true;
						} else {
							return false;
						}
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		});
		// if still have songs in recent history, cannot choose it
		return count($filteredHistory) > 0;
	}

	public static function process($model, $start, $history) {
		$query = $model['params']['query'];
		$criteria = self::getCriteria($query);
		$song = null;
		if ($criteria != null) {
			$song = Songs::pickSong($criteria, $start, $history);
		}
		return $song;
	}
}