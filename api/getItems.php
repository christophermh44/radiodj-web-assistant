<?php include_once '../init.php';

// ALTER TABLE songs ADD FULLTEXT INDEX (artist, title);
// 
// [mysqld]
// ft_min_word_len = 1
// ft_stopword_file = ""

$val = urldecode($_GET['value']);

$songs = Songs::searchForTags($val);
$events = Songs::searchForEvents($val);

$items = array_merge($songs, $events);

echo json_encode($items);