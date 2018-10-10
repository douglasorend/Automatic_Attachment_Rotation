--------

## AUTOMATIC ATTACHMENT ROTATION (AND RESIZE) v5.7

[**By Dougiefresh**](http://www.simplemachines.org/community/index.php?action=profile;u=253913) -> [Link to Mod](http://custom.simplemachines.org/mods/index.php?mod=4087)

--------

[hr]

**## Introduction**
This mod allows the automatic rotation and/or flipping of images **ONLY IF** the EXIF information contained within (if such exists) indicates that such processing is required in order to show the picture  in the correct orientation.

**NOTE:** SMF contains an option called **Re-encode potentially dangerous image attachments**, which is turned on by default.  This re-encoding removes the orientation information from attachments (amongst other things), which means attachments uploaded prior to this mod being installed will **NOT** be able to rotated properly, as the EXIF information is missing from the re-encoded attachment file!

**Major changes introduced in v4.0**

- Option to enable/disable automatic image rotation.
- Code update to enable automatic image resizing/reformatting in posts (and PMs if the [PM Attachments](https://custom.simplemachines.org/mods/index.php?mod=1974) mod is installed).

**Major change introduced in v5.0**

- Option to automatically, manually or batch resize/reformat existing images using-'Attachment Settings' options (non-JPEG images will only be reformatted to JPEG if the *Reformat non-JPEG images to JPEG* option is enabled).


**## Post Screen Changes**
Beside each image attachment a dropbox will be displayed listing the following rotation options:

- No Change
- Rotate 90[sup]o[/sup] Right
- Rotate 90[sup]o[/sup] Left
- Rotate 180[sup]o[/sup]
- Horizontal Flip
- Vertical Flip
- Horizontal Flip, Rotate 90[sup]o[/sup] Right
- Vertical Flip, Rotate 90[sup]o[/sup] Right


## Automatic Resizing of Existing Images
When a post is dispayed, and if the Attachment Setting *Resize existing images* option is enabled, images in the post will be automatically resized (and reformatted to JPEG if the Attachment Setting option *Reformat non-JPEG images to JPEG* is enabled) using the same Attachment Setting options (ie, Reformat non-JPEG images to JPEG, JPEG quality factor and/or maximum width/height) that are applied to new image attachments in posts.  In addition, if the Attachment Setting option *Create backup of original image file when resizing* is also enabled the original image file will be saved to the attachments directiory with the extension '.rei'.

## Manual Resizing of Existing Images
This feature, which can be accessed via **Admin => Forum => Attachments and Avatars => Browse Files => Resize Existing Images**, can be used to selectively resize/reformat existing images using the same Attachment Setting options (ie, Reformat non-JPEG images to JPEG, JPEG quality factor and/or maximum width/height) that are applied to new image attachments in posts.

## Batch Resizing of Existing Images
This feature, which can be accessed via **Admin => Forum => Attachments and Avatars => File Maintenance => Batch Resize Existing Images**, will resize/reformat **all** existing images using the same Attachment Setting options (ie, Reformat non-JPEG images to JPEG, JPEG quality factor and/or maximum width/height) that are applied to new image attachments in posts.
 
The forum should be configured as follows **prior** to commencing batch resizing:

- *Admin => Configuration => Server Settings => General => Enable Maintenance Mode* should be **enabled**.
- *Admin => Configuration => Security and Moderation => General => Disable administration security* should be **disabled**.
- *Admin => Forum => Posts and Topics => Topic Settings => Number of posts per page in a topic page* should be configured to between 15 and 25 via  (make a note of the current value).
- *Admin => Forum => Attachments and Avatars => Attachment Settings* - Maximum size per attachment, Reformat non-JPEG images to JPEG, JPEG quality factor, Maximum width of attached images and Maximum height of attached images should be configured.
- *Admin => Forum => Attachments and Avatars => File Maintenance => Attachment Integrity Check* should be run and issues corrected.

**Important notes for batch resizing:**

- Non-JPEG images will **only** be reformatted to JPEG if the *Reformat non-JPEG images to JPEG* option is enabled.
- Existing *'attachments'* database table will be copied to *'attachmentsPreREI'* database table. *
- Original image files are saved to the *'attachmentsPreREI'* directory/folder. *
- File timestamp for resized image file is set to match timestamp of original image file.
- Batch processing progress information is displayed and updated.
- The results of the batch resizing process will be displayed on completion and also written to the forum error log file.

* The *'attachmentsPreREI'* database table and directory/folder can be removed after checks have been done to confirm successful resizing - this will free up additional disk space on the server.

The forum should be configured as follows **after** batch resizing has finished:

- *Admin => Configuration => Security and Moderation => General => Disable administration security* should be **enabled**.
- *Admin => Configuration => Server Settings => General => Enable Maintenance Mode* should be **disabled**.
- *Admin => Forum => Posts and Topics => Topic Settings => Number of posts per page in a topic page* should be configured to previous value.

## Admin Changes
In **Admin => Forum => Attachments and Avatars**:

- **Browse Files**: There is a new column (and associated button) for rotating/flipping images - the options for rotating/flipping images are only shown for image attachments.  There is also a new horizontal tab labelled *'Resize Existing Images'* that can be used for manually resizing/reformatting existing images.
- **Attachment Settings**: Options to enable/disable automatic image rotation and automatic resizing of existing images, option to reformat non-JPEG images to JPEG and options to set JPEG quality factor and max width/height values for attached images.
- **File Maintenance**: There is a new section for *Batch Resize Existing Images*.  There is also a new section for *Reset Orientation Flag*.

## Compatibility Notes
This mod was tested on SMF 2.0.15 but should work on SMF 2.0 and up.
It has also been tested on SMF 2.1 Beta 3 and there are some known issues with manual image rotation not always working and with the manual image rotation options not being displayed in posts.
This mod is not currently compatible with SMF 2.1 Beta 4.
SMF 1.x is not and will not be supported.

The [Image Processing Memory Limit](https://custom.simplemachines.org/mods/index.php?mod=4111) mod should be installed if 'white screen' issues are encountered when uploading and/or rotating images.

The [Improved Attachment Error Handling](https://custom.simplemachines.org/mods/index.php?mod=3255) mod (if so desired) should be installed **BEFORE** this mod to avoid install errors.

The [Resize Attachment Images](https://custom.simplemachines.org/mods/index.php?mod=2206) mod **MUST** be uninstalled prior to installing this version as the two mods are not compatible (this mod contains similar and updated functionality).

## Translators
o Dutch: [@rjen](https://www.simplemachines.org/community/index.php?action=profile;u=287786)
o Spanish Latin: [Rock Lee](https://www.simplemachines.org/community/index.php?action=profile;u=322597).

## Special Credit
This mod relies on the [phpExifRW](http://www.phpclasses.org/package/1042-PHP-EXIF-information-reader-and-writer.html) class, which is licensed under the [GNU Lesser General Public License](http://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html), in order to read the EXIF information from image files.  This class makes the requirement of having EXIF support built-in, which some servers do not have, not important to the task of successfully pulling the orientation out of the image file.  The **exifReader.inc** file was renamed to **Class-exifReader.php** in order to name the file in accordance with the naming convention of SMF and included in this mod.

Test images with EXIF orientation values embedded in them are available at [Galloway.me.uk](http://www.galloway.me.uk/2012/01/uiimageorientation-exif-orientation-sample-images/) and at the [ Image Orientation](http://www.elkarte.net/community/index.php?topic=2509.0) thread over at the ElkArte forum.

## Changelog
The changelog can be seen at [XPtsp.com](http://www.xptsp.com/board/free-modifications/automatic-attachment-rotation/msg9/#msg9).

## License
Copyright (c) 2016 - 2018, Douglas Orend
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
