<?php 
return array (
  'domain' => 
  array (
    'mobile' => 'http://testdata.souxw.com',
    'main' => 'http://testdata.souxw.com',
  ),
  'version' => 
  array (
    0 => 
    array (
      'no' => '4.1',
      'path' => '/shouji/',
    ),
    1 => 
    array (
      'no' => '5.0',
      'path' => '/phone/',
    ),
  ),
  'rulesReplace' => false,
  'rules' => '{PHP_EOL}RewriteCond %{HTTP_HOST} ^{host}${PHP_EOL}RewriteCond %{REQUEST_URI} !^/uploads/ {PHP_EOL} RewriteRule (.*) {path}/$1 [L]{PHP_EOL}',
  'delimiterLeft' => '#mobile start',
  'delimiterRight' => '#mobile end',
);