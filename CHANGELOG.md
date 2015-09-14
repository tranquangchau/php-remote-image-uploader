#### Version 5.2.17; Jun 13, 2015
* Postimage fully fixes

#### Version 5.2.15; May 27, 2015
* No longer support Flickr login with accounts. Must use API
* Add new PicasaNew uploader that use API version 2 with OAuth 2.0

#### Version 5.2.14; Mar 07, 2015
* Fix Postimage not found image url in some cases.

#### Version 5.2.13; Jan 19, 2015
* Clean all plugins
* Optimize, rewrite a part of Picasa plugin and fixed a bug while checking permission; increase session expires time to 900 seconds.
* Use new version of ChipVN_Http_Client to get higher performance when upload large file.

#### Version 5.2.12; Oct 14, 2014
* Update: Flickr `requestToken` method to avoid error if headers has sent.

##### Version 5.2.8; Oct 06, 2014
* Update: Imgur plugin to use API version 3 (require API Client ID, Secret)

##### Version 5.2.3: Jul 10, 2014
* Update Flickr API (SSL required)
* Update vendor, ChipVN library
* Update Picasa plugin to get URL not resized, use account by custom email
* New Postimage plugin

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