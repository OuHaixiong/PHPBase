<?php
$filePath = '/data/www/PHPBase/data/发票.pdf';
$filename = preg_replace('/^.+[\\\\\\/]/', '', $filePath); 
var_dump($filename);