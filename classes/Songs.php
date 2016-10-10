<?php

class Songs {
	private static $q;

	private static $categories;
	private static $subcategories;
	private static $genres;

	public static function static_init() {
		self::$q = Query::getInstance();
		$categories = self::$q->process('SELECT * FROM category');
		self::$categories = [];
		foreach ($categories as $category) {
			$k = $category->ID;
			self::$categories[$k] = $category;
		}
		$subcategories = self::$q->process('SELECT * FROM subcategory');
		self::$subcategories = [];
		foreach ($subcategories as $subcategory) {
			$k = $subcategory->ID;
			self::$subcategories[$k] = $subcategory;
		}
		$genres = self::$q->process('SELECT * FROM genre');
		self::$genres = [];
		foreach ($genres as $genre) {
			$k = $genre->id;
			self::$genres[$k] = $genre;
		}
	}

	public static function countSongs() {
		$field = "COUNT(*)";
		$count = self::$q->process('SELECT COUNT(*) FROM songs;')[0]->$field;
		return $count;
	}

	public static function countArtists() {
		$field = "COUNT(DISTINCT(artist))";
		$count = self::$q->process('SELECT COUNT(DISTINCT(artist)) FROM songs;')[0]->$field;
		return $count;
	}

	private static function getCategory($subCategoryObject) {
		return $subCategoryObject != null && array_key_exists($subCategoryObject->parentid, self::$categories) ? self::$categories[$subCategoryObject->parentid] : null;
	}

	private static function getSubCategory($id) {
		return array_key_exists($id, self::$subcategories) ? self::$subcategories[$id] : null;
	}

	private static function getGenre($id) {
		return array_key_exists($id, self::$genres) ? self::$genres[$id] : null;
	}

	public static function getTypes() {
		return ([
			0 => 'Music',
			1 => 'Jingle',
			2 => 'Sweeper',
			3 => 'Voiceover',
			4 => 'Commercial',
			5 => 'InternetStream',
			6 => 'Other',
			7 => 'VDF',
			8 => 'Podcast',
			9 => 'Request',
			10 => 'News',
			11 => 'PlaylistEvent',
			12 => 'FileByDate',
			13 => 'NewestFromFolder',
			14 => 'Teaser'
		]);
	}

	private static function getTypeName($typeId) {
		return self::getTypes()[$typeId];
	}

	private static function getCuePoints($cueExpr) {
		$cues = [];
		$cuesExprs = explode('&', $cueExpr);
		foreach ($cuesExprs as $val) {
			if (strpos($val, '=')) {
				$members = explode('=', $val);
				$cues[$members[0]] = $members[1];
			}
		}
		return $cues;
	}

	public static function getById($id) {
		$song = self::$q->process('SELECT * FROM songs WHERE ID = :id', [
			'id' => $id
		])[0];
		return self::fillSong($song);
	}

	public static function fillSong($song) {
		if ($song == null) return null;
		$song->name = $song->title.' - '.$song->artist;
		$song->designation = $song->name;
		$song->subCategory = self::getSubCategory($song->id_subcat);
		$song->category = self::getCategory($song->subCategory);
		$song->genre = self::getGenre($song->id_genre);
		$song->typeName = self::getTypeName($song->song_type);
		$song->cues = self::getCuePoints($song->cue_times);
		$song->isSong = true;
		return $song;
	}

	public static function save($id, $fields) {
		$set = [];
		foreach ($fields as $key => $value) {
			$set[] = $key.'=:'.$key;
		}
		$fields['id'] = $id;
		self::$q->process('UPDATE songs SET '.implode(', ', $set).' WHERE ID = :id', $fields);
	}

	public static function getSubCategories() {
		$map = array_map(function($item) {
			$item->category = Songs::getCategory($item);
			$item->category_name = $item->category->name . ' / ' . $item->name;
			return $item;
		}, self::$subcategories);
		usort($map, function($left, $right) {
			return $left->category_name > $right->category_name;
		});
		return $map;
	}

	public static function getGenres() {
		return self::$genres;
	}

	public static function getAll() {
		$songs = self::$q->process('SELECT * FROM songs');
		$s = [];
		foreach ($songs as $k => $song) {
			$s[$k] = self::fillSong($song);
		}
		return $s;
	}

	public static function getLastSongsOf($id_subcat) {
		$songs = self::$q->process('SELECT * FROM songs WHERE enabled=1 AND id_subcat=:idsc ORDER BY ID DESC', [
			'idsc' => $id_subcat
		]);
		return $songs;
	}

	public static function getNewSongs() {
		return self::getLastSongsOf(31);
	}

	public static function searchForTags($terms) {
		$terms = implode(' ', array_map(function($item) {
			return "+$item*";
		}, explode(' ', $terms)));

		$songs = self::$q->process('SELECT ID FROM songs WHERE MATCH (artist, title) AGAINST (:val IN BOOLEAN MODE) LIMIT 8', [
			'val' => $terms
		]);

		$songs = array_map(function($item) {
			return Songs::getById($item->ID);
		}, $songs);

		return $songs;
	}

	public static function searchForEvents($terms) {
		$events = self::$q->process('SELECT * FROM events WHERE name LIKE :name', [
			'name' => "%$terms%"
		]);

		$events = array_map(function($item) {
			$item->designation = 'EVENT - '.$item->name;
			$item->isEvent = true;
			$item->duration = 0.0;
			return $item;
		}, $events);

		return $events;
	}

	public static function pickSong($criteria, $start, $history) {
		$category = $criteria['category']['ID'];
		$subCategory = $criteria['subCategory']['ID'] ?? '';
		$genre = $criteria['genre']['ID'] ?? '';
		$mode = $criteria['mode'] ?? 'random';
		$sard = $criteria['sard'] ?? 45;
		$ssrd = $criteria['ssrd'] ?? 90;
		$baseQuery = 'SELECT S.ID as songID, S.artist as songArtist, S.title as songTitle, S.count_played as count_played, S.date_played as date_played, S.weight as weight
			FROM songs S
			LEFT JOIN (
				subcategory U LEFT JOIN category C ON U.parentid = C.ID
			) ON S.id_subcat = U.ID
			LEFT JOIN genre G ON S.id_genre = G.ID
			WHERE U.parentid = :category';
		$baseParams = ['category' => $category];
		$joins = '';
		if (!empty($subCategory)) {
			$joins.= ' AND S.id_subcat = :subcategory';
			$baseParams['subcategory'] = $subCategory;
		}
		if (!empty($genre)) {
			$joins.= ' AND S.id_genre = :genre';
			$baseParams['genre'] = $genre;
		}
		$baseQuery.= $joins;
		$filterColumn = '';
		$filterOrder = '';
		if ($mode == 'lfp') {
			$filterColumn = 'count_played';
			$filterOrder = 'ASC';
		} else if ($mode == 'mfp') {
			$filterColumn = 'count_played';
			$filterOrder = 'DESC';
		/* } else if ($mode == 'lrp') {
			$baseQuery.= ' ORDER BY date_played ASC';
			$filterColumn = 'date_played';
		} else if ($mode == 'mrp') {
			$baseQuery.= ' ORDER BY date_played DESC';
			$filterColumn = 'date_played'; */
		} else if ($mode == 'priority') {
			$filterColumn = 'weight';
			$filterOrder = 'DESC';
		}
		if ($filterColumn != '') {
			$baseQuery.= ' ORDER BY '.$filterColumn.' '.$filterOrder;
		}
		// selecting songs by query filters (mode, category, …)
		$songs = self::$q->process($baseQuery, $baseParams);
		// getting all distinct values from column that represent the mode
		if ($filterColumn != '') {
			$filterValues = array_map(function($item) use ($filterColumn) {
				return $item->$filterColumn;
			}, $songs);
			usort($filterValues, function ($left, $right) use ($filterOrder) {
				if ($left == $right) {
					return 0;
				}
				if ($filterOrder == 'ASC') {
					return $left < $right ? -1 : 1;
				} else {
					return $left < $right ? 1 : -1;
				}
			});
		}
		$alreadyChoosen = [];
		$choosen = null;
		// itering on different values, starting with the most matching ones
		if ($filterColumn != '') {
			foreach ($filterValues as $filterValue) {
				// if a song has been correctly choosen, exit loop
				if ($choosen != null) {
					break;
				}
				// filtering song by mode
				$filteredSongs = array_filter($songs, function($s) use ($filterColumn, $filterValue) {
					return $s->$filterColumn == $filterValue;
				});
				shuffle($filteredSongs);
				do {
					// picking one
					$choosen = $songs[array_rand($songs)];
					$choosen->ID = $choosen->songID;
					$choosen->artist = $choosen->songArtist;
					$choosen->title = $choosen->songTitle;
					$alreadyChoosen[] = $choosen;
					$inHistory = Queries::isInHistory($choosen, $history, $start, $sard, $ssrd);
				} while (count($alreadyChoosen) < count($filteredSongs) && $inHistory);
				// if all songs doesn't match query, allowing another loop iteration
				if (count($alreadyChoosen) == count($songs) && $inHistory) {
					$choosen = null;
				}
			}
			// can be null if no song match
			return $choosen;
		} else {
			do {
				// picking one
				$choosen = $songs[array_rand($songs)];
				$choosen->ID = $choosen->songID;
				$choosen->artist = $choosen->songArtist;
				$choosen->title = $choosen->songTitle;
				$alreadyChoosen[] = $choosen;
				$inHistory = Queries::isInHistory($choosen, $history, $start, $sard, $ssrd);
			} while (count($alreadyChoosen) < count($songs) && $inHistory);
			// if all songs doesn't match query, allowing another loop iteration
			if (count($alreadyChoosen) == count($songs) && $inHistory) {
				$choosen = null;
			}
			// can be null if no song match
			return $choosen;
		}
	}
}
