<?php

class Podcasts {
	private static $baseUrl = 'https://api.mixcloud.com/';

	public static function static_init() {}

	private static function query($method, $url, $params = []) {
		$url = self::$baseUrl.$url;
		$ch = curl_init();
		$curlopts = [
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_RETURNTRANSFER => 1
		];
		curl_setopt_array($ch, $curlopts);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	}

	public static function getPodcasts($channel, $limit = null) {
		$r = self::query('GET', $channel.'/cloudcasts/');
		$podcasts = [];
		$index = 0;
		foreach ($r->data as $data) {
			$podcasts[] = $data;
			if (is_numeric($limit) && $index == $limit) {
				break;
			}
			$index++;
		}
		return $podcasts;
	}

	private static function addParam($cmd, $key, $value) {
		return $cmd.' -F "'.$key.'='.$value.'"';
	}

	private static function addSectionParam($cmd, $id, $chapter, $startTime, $artist = null, $title = null) {
		$cmd = self::addParam($cmd, 'sections-'.$id.'-start_time', $startTime);
		if ($artist != null && $title != null) {
			$cmd = self::addParam($cmd, 'sections-'.$id.'-artist', $artist);
			$cmd = self::addParam($cmd, 'sections-'.$id.'-song', $title);
		} else {
			$cmd = self::addParam($cmd, 'sections-'.$id.'-chapter', $chapter);
		}
		return $cmd;
	}

	public static function getAccessToken($oauth_token, $redirect_uri) {
		$response = file_get_contents('https://www.mixcloud.com/oauth/access_token?client_id='.Conf::get('mc_clientid').'&client_secret='.Conf::get('mc_clientsecret').'&code='.$oauth_token.'&redirect_uri='.urlencode($redirect_uri));
		return json_decode($response)->access_token;
	}

	public static function upload($guide, $file, $photo, $oauth_token, $redirect_uri) {
		$cmd = 'curl';
		$podcastFile = __DIR__.'/podcast';
		$imgFile = __DIR__.'/photo';
		move_uploaded_file($file['tmp_name'], $podcastFile);
		move_uploaded_file($photo['tmp_name'], $imgFile);

		$access_token = self::getAccessToken($oauth_token, $redirect_uri);

		$cmd = self::addParam($cmd, 'mp3', '@'.$podcastFile);
		$cmd = self::addParam($cmd, 'picture', '@'.$imgFile);
		$cmd = self::addParam($cmd, 'name', $guide['title']);
		$index = 0;
		$start = 0;
		foreach ($guide['lines'] as $line) {
			if (!is_string($line['additionnals'])) {
				$cmd = self::addSectionParam($cmd, ''.$index, $line['designation'], $start, $line['additionnals']['artist'], $line['additionnals']['title']);
			} else {
				$cmd = self::addSectionParam($cmd, ''.$index, $line['designation'], $start);
			}
			$start+= round($line['duration'], 0);
			$index++;
		}

		$cmd = self::addParam($cmd, 'description', $guide['template']['title'].' - '.$guide['template']['description']);
		$cmd.= ' https://api.mixcloud.com/upload/?access_token='.$access_token;

		$result = json_decode(shell_exec($cmd));
		return $result;
	}
}