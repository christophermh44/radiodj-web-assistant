<?php
$patterns = [
	__DIR__.'/cache/implicit_ftp*',
	__DIR__.'/cache/pack-*.zip'
];

foreach ($patterns as $pattern) {
	shell_exec('rm -fr ' . $pattern);
}