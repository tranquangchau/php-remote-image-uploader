# PHP-Image-Uploader
This project mainly help to remote upload images to some hosting services like Picasa, Imageshack, Imgur, Postimage, etc.

The library is free, but if you need an add-on for xenforo or web tools to upload images, please purchase PAID version under with $12.5, just like give me a beer.

* Author:     Phan Thanh Cong <ptcong90@gmail.com>
* Copyright:  2010-2014 Phan Thanh Cong.
* License:    MIT
* Version:    5.2.4

### PAID version
* Demo: http://ptcong.com/imageuploader5
* PAID version that include user interface and more features, improved
* Purchase Upload tools: http://ptcong.com/?p=10
* Purchase XenForo add-on: http://ptcong.com/?p=23
* After purchased, you will get emails for new version if the item have updated.
* Just like give me a beer.

## Change Logs
***Note:*** This is a library only, and version here is library version (not version of tools or xenforo add-on, etc)

##### Version 5.2.3: Jul 10, 2014
* Update Flickr API (SSL required)
* Update vendor, ChipVN library
* Update Picasa plugin to get URL not resized, use account by custom email and avoid BadAuthentication; WebLoginRequired
* New Postimage plugin
* Require composer package `"ptcong/php-cache-manager": "dev-master"`

##### Version 5.0.1: Apr 2, 2014
* Change class name from `\ChipVN\ImageUploader\ImageUploader` to `ChipVN_ImageUploader_ImageUploader` for usable on all PHP version >= 5.0

##### Version 5.0: Mar 07, 2014
* Supports composer
* Change factory method from `\ChipVN\Image_Uploader::factory` to `ChipVN_ImageUploader_ImageUploader::make`
* Make it simpler (only 5 php files)
* Remove ~~upload to local server~~
* Update Imageshack plugin

##### Version 4.0.1: Sep, 2013
* Fix Imgur auth

##### Version 4.0: Jul 25, 2013
* ~~Require PHP 5.3 or newer~~
* Rewrite all plugins to clear

##### Version 1.0: June 17, 2010
* Upload image to Imageshack, Picasa

## Features
* ~~Upload image to local server~~
* Upload image to remote service like (picasa, imageshack, imgur)
* Remote: can free upload to imgur, imageshack or upload to your account. Picasa must be login to upload
* Easy to make new plugin for uploading to another service

## Usage
###### If you use composer
Add require `"ptcong/php-image-uploader": "dev-master"` to _composer.json_ and run `composer update`, if you catch an issue about stability, should add `"minimum-stability" : "dev"` to your `composer.json`

###### If you don't use composer
Download
- `Loader.php` from https://github.com/ptcong/php-chipvn-classloader and put it to `ChipVN/ClassLoader` folder
- `Client.php` from https://github.com/ptcong/php-http-class and put it to `ChipVN/Http` folder

and include the code on the top of your file:

    include '/path/path/ChipVN/ClassLoader/Loader.php';
    ChipVN_ClassLoader_Loader::registerAutoLoad();

then
### Upload to Picasa.
To upload image to Picasa, you need to have some AlbumIds otherwise the image will be uploaded to _default_ album.
To create new AlbumId faster, you may use echo `$uploader->addAlbum('testing 1');`

    $uploader = ChipVN_ImageUploader_Manager::make('Picasa');
    $uploader->login('your account here', 'your password here');
    // you can set upload to an albumId by array of albums or an album, system will get a random album to upload
    //$uploader->setAlbumId(array('51652569125195125', '515124156195725'));
    //$uploader->setAlbumId('51652569125195125');
    echo $uploader->upload(getcwd(). '/test.jpg');
    // this plugin does not support transload image

### Upload to Imageshack

    $uploader = ChipVN_ImageUploader_Manager::make('Imageshack');
    $uploader->login('your account here', 'your password here');
    $uploader->setApi('your api here');
    echo $uploader->upload(getcwd(). '/a.jpg');
    echo $uploader->transload('http://img33.imageshack.us/img33/6840/wz7u.jpg');

### Upload to Imgur

    $uploader = ChipVN_ImageUploader_Manager::make('Imgur');
    // you may upload with anonymous account but may be the image will be deleted after a period of time
    // $uploader->login('your account here', 'your password here');
    echo $uploader->upload(getcwd(). '/a.jpg');
    echo $uploader->transload('http://img33.imageshack.us/img33/6840/wz7u.jpg');

### Upload to Postimage

    $uploader = ChipVN_ImageUploader_Manager::make('Postimage');
    // you may upload with anonymous account but may be the image will be deleted after a period of time
    // $uploader->login('your account here', 'your password here');
    echo $uploader->upload(getcwd(). '/a.jpg');
    echo $uploader->transload('http://img33.imageshack.us/img33/6840/wz7u.jpg');
