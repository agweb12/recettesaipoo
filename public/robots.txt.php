<?php
require_once '../app/config/config.php';
require_once '../app/helpers/SEO.php';

use App\Helpers\SEO;

header('Content-Type: text/plain; charset=utf-8');

echo SEO::generateRobotsTxt();