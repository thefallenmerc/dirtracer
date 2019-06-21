<?php

require_once __DIR__ . '/vendor/autoload.php';

use DirTracer\DirTracer;

$dirTracer = new DirTracer('.', ['mp4', 'vtt']);
echo $dirTracer->jsonResponse();
