<?php

require '../vendor/autoload.php';
$cacher = new Doctrine\Common\Cache\ArrayCache();
// $cacher = new Doctrine\Common\Cache\FilesystemCache('/Volumes/Data/Work/Localhost/www/test/tmp');

$uploader = RemoteImageUploader\Factory::create('Postimage', array(
    'cacher'   => $cacher,
    'username' => 'gavn2015@gmail.com',
    'password' => 'xxxx'
));
$uploader->login();

$url = $uploader->upload('/Volumes/Data/Data/Photos/My Icon/ninja.JPG');
var_dump($url);

$url = $uploader->transload('http://s26.postimg.org/f0lrm6vqh/ninja.jpg');
var_dump($url);