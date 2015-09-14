# PHP Remote Image Uploader
This project mainly help to remote upload images to some hosting services like Picasa, Imageshack, Imgur, Postimage, etc.

## Note
I'm developing 6.x branch, but i don't have much free time, if you're interesting with this project, please contribute for it.


## Plugins
* Upload tools: http://code.ptcong.com/?p=10
* XenForo add-on: http://code.ptcong.com/?p=23
* Wordpress plugin: http://code.ptcong.com/?p=1105

## Features
* Upload image to remote service like (picasa, imageshack, imgur)
* Remote: can free upload to imgur, imageshack or upload to your account. Picasa must be login to upload
* Easy to make new plugin for uploading to another service

## Usage
###### If you use composer
Add require `"ptcong/php-image-uploader": "dev-master"` to _composer.json_ and run `composer update`, if you catch an issue about stability, should add `"minimum-stability" : "dev"` to your `composer.json`

###### If you don't use composer
Download
- https://github.com/ptcong/php-chipvn-classloader and put it to `ChipVN/ClassLoader` folder
- https://github.com/ptcong/php-http-class (2.x) and put it to `ChipVN/Http` folder
- https://github.com/ptcong/php-cache-manager and put it to `ChipVN/Cache` folder

and include the code on the top of your file:

    include '/path/path/ChipVN/ClassLoader/Loader.php';
    ChipVN_ClassLoader_Loader::registerAutoLoad();

then

### ~~Upload to Picasa - ver 1~~
~~To upload image to Picasa, you need to have some AlbumIds otherwise the image will be uploaded to _default_ album.
To create new AlbumId faster, you may use echo `$uploader->addAlbum('testing 1');`~~

```php
$uploader = ChipVN_ImageUploader_Manager::make('Picasa');
$uploader->login('your account', 'your password');
// you can set upload to an albumId by array of albums or an album, system will get a random album to upload
//$uploader->setAlbumId(array('51652569125195125', '515124156195725'));
//$uploader->setAlbumId('51652569125195125');
echo $uploader->upload(getcwd(). '/test.jpg');
// this plugin does not support transload image
```


### Upload to Picasanew - ver 2 (use OAuth 2.0)
To upload image to Picasanew, you need to have some AlbumIds otherwise the image will be uploaded to _default_ album.
To create new AlbumId faster, you may use echo `$uploader->addAlbum('testing 1');`

```php
$uploader = ChipVN_ImageUploader_Manager::make('Picasanew');
$uploader->login('your user name', ''); // we don't need password here
$uploader->setApi('Client ID'); // register in console.developers.google.com
$uploader->setSecret('Client secret');
// you can set upload to an albumId by array of albums or an album, system will get a random album to upload
//$uploader->setAlbumId(array('51652569125195125', '515124156195725'));
//$uploader->setAlbumId('51652569125195125');
if (!$uploader->hasValidToken()) {
    $uploader->getOAuthToken('http://yourdomain.com/test.php');
}
echo $uploader->upload(getcwd(). '/test.jpg');
// this plugin does not support transload image
```

### Upload to Flickr
To upload image to Picasa, you need to have some AlbumIds otherwise the image will be uploaded to _default_ album.
To create new AlbumId faster, you may use echo `$uploader->addAlbum('testing 1');`
```php
$uploader = ChipVN_ImageUploader_Manager::make('Flickr');
$uploader->setApi('API key');
$uploader->setSecret('API secret');
$token = $uploader->getOAuthToken('http://yourdomain.com/test.php');
$uploader->setAccessToken($token['oauth_token'], $token['oauth_token_secret']);
echo $uploader->upload(getcwd(). '/test.jpg');
// this plugin does not support transload image
```

### Upload to Imageshack
```php
$uploader = ChipVN_ImageUploader_Manager::make('Imageshack');
$uploader->login('your account', 'your password');
$uploader->setApi('your api here');
echo $uploader->upload(getcwd(). '/a.jpg');
echo $uploader->transload('http://img33.imageshack.us/img33/6840/wz7u.jpg');
```

### Upload to Imgur
```php
$uploader = ChipVN_ImageUploader_Manager::make('Imgur');
$uploader->setApi('your client id');
$uploader->setSecret('your client secret');
// you may upload with anonymous account but may be the image will be deleted after a period of time
// $uploader->login('your account here', 'your password here');
echo $uploader->upload(getcwd(). '/a.jpg');
echo $uploader->transload('http://img33.imageshack.us/img33/6840/wz7u.jpg');
```

### Upload to Postimage
```php
$uploader = ChipVN_ImageUploader_Manager::make('Postimage');
// you may upload with anonymous account but may be the image will be deleted after a period of time
// $uploader->login('your account here', 'your password here');
echo $uploader->upload(getcwd(). '/a.jpg');
echo $uploader->transload('http://img33.imageshack.us/img33/6840/wz7u.jpg');
```

