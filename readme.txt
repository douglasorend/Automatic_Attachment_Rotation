[hr]
[center][color=red][size=16pt][b]AUTOMATIC ATTACHMENT ROTATION (AND RESIZE) v5.1[/b][/size][/color]
[url=http://www.simplemachines.org/community/index.php?action=profile;u=253913][b]By Dougiefresh[/b][/url] -> [url=http://custom.simplemachines.org/mods/index.php?mod=4087]Link to Mod[/url]
[/center]
[hr]

[color=blue][b][size=12pt][u]Introduction[/u][/size][/b][/color]
This mod allows the automatic rotation and/or flipping of images [b]ONLY IF[/b] the EXIF information contained within (if such exists) indicates that such processing is required in order to show the picture upright.

[b]NOTE:[/b] SMF contains an option called [b]Re-encode potentially dangerous image attachments[/b], which is turned on by default.  This re-encoding removes the orientation information from attachments (amongst other things), which means that attachments uploaded prior to this mod being installed will [b]NOT[/b] be able to rotated properly, as the EXIF information is missing from the re-encoded attachment file!

[b]Major changes introduced in v4.0[/b]
o Option to enable/disable automatic image rotation.
o Code update to enable automatic image resizing/reformatting in posts (and PMs if the [url=https://custom.simplemachines.org/mods/index.php?mod=1974]PM Attachments[/url] mod is installed).

[b]Major change introduced in v5.0[/b]
o Option to manually or batch resize/reformat existing images using 'Attachment Settings' options (non-JPEG images will only be reformatted to JPEG if the [i]Reformat non-JPEG images to JPEG[/i] option is enabled).


[color=blue][b][size=12pt][u]Post Screen Changes[/u][/size][/b][/color]
Beside each image attachment a dropbox will be displayed listing the following rotation options:
o No Change
o Rotate 90[sup]o[/sup] Right
o Rotate 90[sup]o[/sup] Left
o Rotate 180[sup]o[/sup]
o Horizontal Flip
o Vertical Flip
o Horizontal Flip, Rotate 90[sup]o[/sup] Right
o Vertical Flip, Rotate 90[sup]o[/sup] Right


[color=blue][b][size=12pt][u]Batch Resizing of Existing Images[/u][/size][/b][/color]
This feature will resize/reformat [b]all[/b] existing images using the same option settings (ie, Reformat non-JPEG images to JPEG, JPEG quality factor and/or maximum width/height) that are applied to new image attachments in posts and/or PMs.
 
The forum should be configured as follows [b]prior[/b] to commencing batch resizing:
o [i]Admin => Configuration => Server Settings => General => Enable Maintenance Mode[/i] should be [b]enabled[/b].
o [i]Admin => Configuration => Security and Moderation => General => Disable administration security[/i] should be [b]disabled[/b].
o [i]Admin => Forum => Posts and Topics => Topic Settings => Number of posts per page in a topic page[/i] should be configured to between 15 and 25 via  (make a note of the current value).
o [i]Admin => Forum => Attachments and Avatars => Attachment Settings[/i] - Maximum size per attachment, Reformat non-JPEG images to JPEG, JPEG quality factor, Maximum width of attached images and Maximum height of attached images should be configured.
o [i]Admin => Forum => Attachments and Avatars => File Maintenance => Attachment Integrity Check[/i] should be run and issues corrected.

[b]Important notes for batch resizing:[/b]
o Non-JPEG images will [b]only[/b] be reformatted to JPEG if the [i]Reformat non-JPEG images to JPEG[/i] option is enabled.
o Existing [i]'attachments'[/i] database table will be copied to [i]'attachmentsPreREI'[/i] database table. *
o Original image files will be moved to [i]'attachmentsPreREI'[/i] directory/folder. *
o File timestamp for resized image file is set to match timestamp of original image file.
o Batch processing progress information is displayed and updated.
o The results of the batch resizing process will be displayed on completion and also written to the forum error log file.

* The [i]'attachmentsPreREI'[/i] database table and directory/folder can be removed after checks have been done to confirm successful resizing - this will free up additional disk space on the server.

The forum should be configured as follows [b]after[/b] batch resizing has finished:
o [i]Admin => Configuration => Security and Moderation => General => Disable administration security[/i] should be [b]enabled[/b].
o [i]Admin => Configuration => Server Settings => General => Enable Maintenance Mode[/i] should be [b]disabled[/b].
o [i]Admin => Forum => Posts and Topics => Topic Settings => Number of posts per page in a topic page[/i] should be configured to previous value.


[color=blue][b][size=12pt][u]Admin Changes[/u][/size][/b][/color]
In [b]Admin[/b] => [b]Forum[/b] => [b]Attachments and Avatars[/b]:
o [b]Browse Files[/b]: There is a new column (and associated button) for rotating/flipping images - the options for rotating/flipping images are only shown for image attachments.  There is also a new horizontal tab labelled [i]'Resize Existing Images'[/i] that can be used for manually resizing/reformatting existing images.
o [b]Attachment Settings[/b]: Options to enable/disable automatic image rotation and automatic resizing of existing images, option to reformat non-JPEG images to JPEG and options to set JPEG quality factor and max width/height values for attached images.
o [b]File Maintenance[/b]: There is a new section for [i]Batch Resize Existing Images[/i].  There is also a new section for [i]Reset Orientation Flag[/i].


[color=blue][b][size=12pt][u]Compatibility Notes[/u][/size][/b][/color]
This mod was tested on SMF 2.0.15 but should work on SMF 2.0 and up.
It has also been tested on SMF 2.1 Beta 3 and there are some known issues with manual image rotation not always working and with the manual image rotation options not being displayed in posts.
This mod is not currently compatible with SMF 2.1 Beta 4.
SMF 1.x is not and will not be supported.

The [url=https://custom.simplemachines.org/mods/index.php?mod=4111]Image Processing Memory Limit[/url] mod should be installed if 'white screen' issues are encountered when uploading and/or rotating images.

The [url=https://custom.simplemachines.org/mods/index.php?mod=3255]Improved Attachment Error Handling[/url] mod (if so desired) should be installed [b]BEFORE[/b] this mod to avoid install errors.

The [url=https://custom.simplemachines.org/mods/index.php?mod=2206]Resize Attachment Images[/url] mod [b]MUST[/b] be uninstalled prior to installing this version as the two mods are not compatible (this mod contains similar and updated functionality).


[color=blue][b][size=12pt][u]Translators[/u][/size][/b][/color]
o Dutch: [url=https://www.simplemachines.org/community/index.php?action=profile;u=287786]@rjen[/url]
o Spanish Latin: [url=https://www.simplemachines.org/community/index.php?action=profile;u=322597]Rock Lee[/url].


[color=blue][b][size=12pt][u]Special Credit[/u][/size][/b][/color]
This mod relies on the [url=http://www.phpclasses.org/package/1042-PHP-EXIF-information-reader-and-writer.html]phpExifRW[/url] class, which is licensed under the [url=http://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html]GNU Lesser General Public License[/url], in order to read the EXIF information from image files.  This class makes the requirement of having EXIF support built-in, which some servers do not have, not important to the task of successfully pulling the orientation out of the image file.  The [b]exifReader.inc[/b] file was renamed to [b]Class-exifReader.php[/b] in order to name the file in accordance with the naming convention of SMF and included in this mod.

Test images with EXIF orientation values embedded in them are available at [url=http://www.galloway.me.uk/2012/01/uiimageorientation-exif-orientation-sample-images/]Galloway.me.uk[/url] and at the [url=http://www.elkarte.net/community/index.php?topic=2509.0] Image Orientation[/url] thread over at the ElkArte forum.


[color=blue][b][size=12pt][u]Changelog[/u][/size][/b][/color]
The changelog has been removed and can be seen at [url=http://www.xptsp.com/board/index.php?topic=662.msg975#msg975]XPtsp.com[/url].


[color=blue][b][size=12pt][u]License[/u][/size][/b][/color]
[quote]Copyright (c) 2016 - 2018, Douglas Orend
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
[/quote]
