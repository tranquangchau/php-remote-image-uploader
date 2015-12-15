<?php

require '../vendor/autoload.php';
$cacher = new Doctrine\Common\Cache\ArrayCache();
// $cacher = new Doctrine\Common\Cache\FilesystemCache('/Volumes/Data/Work/Localhost/www/test/tmp');

$uploader = RemoteImageUploader\Factory::create('Imgur', array(
    'username' => 'gavn2015@gmail.com',
    'password' => 'xxxx'
));
$uploader->setCacher($cacher);

$uploader->login();

$a = $uploader->upload('/Volumes/Data/Data/Photos/My Icon/ninja.JPG');
// $a = $uploader->transload('http://s26.postimg.org/f0lrm6vqh/ninja.jpg');

var_dump($a);