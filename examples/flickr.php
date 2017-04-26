<?php

require '../vendor/autoload.php';
// $cacher = new Doctrine\Common\Cache\ArrayCache();
$cacher = new Doctrine\Common\Cache\FilesystemCache('/tmp');

//https://www.flickr.com/services/apps/72157683071719655/
//https://www.flickr.com/services/apps/72157683071719655/
$uploader = RemoteImageUploader\Factory::create('Flickr', array(
    'cacher'         => $cacher,
    'api_key'        => '3755fd8fd0b5bbfdf4aa0ed294697d4c',
    'api_secret'     => '6552b90c22f0b268',

    // if you have oauth_token and secret, you can set
    // to the options to pass
    'oauth_token'        => null,
    'oauth_token_secret' => null,
));

$callbackUrl = 'http'.(getenv('HTTPS') == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

$uploader->authorize($callbackUrl);

//$url = $uploader->upload('/Volumes/Data/Data/Photos/My Icon/ninja.JPG');
$url = $uploader->upload('serum-bk-bach-khoa-1.jpg');
var_dump($url);

//$url = $uploader->transload('http://s26.postimg.org/f0lrm6vqh/ninja.jpg');
//var_dump($url,$uploader);
