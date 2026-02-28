<?php
// Single-execution wrapper for phel run
define('PHEL_CRAWLER_MAIN', true);
require __DIR__ . '/vendor/autoload.php';
\Phel\Phel::run(__DIR__, 'phel-crawler\main');
