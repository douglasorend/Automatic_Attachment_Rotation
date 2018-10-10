[hr]
[center][color=red][size=16pt][b]AUTOMATIC ATTACHMENT ROTATION v3.0[/b][/size][/color]
[url=http://www.simplemachines.org/community/index.php?action=profile;u=253913][b]By Dougiefresh[/b][/url] -> [url=http://custom.simplemachines.org/mods/index.php?mod=4087]Link to Mod[/url]
[/center]
[hr]

[color=blue][b][size=12pt][u]Introduction[/u][/size][/b][/color]
This mod allows the automatic rotation and/or flipping of images [b]ONLY IF[/b] the EXIF information contained within (if such exists) indicates that such processing is required in order to show the picture upright.

[b]NOTE:[/b] SMF contains an option called [b]Re-encode potentially dangerous image attachments[/b], which is turned on by default.  This re-encoding removes the orientation information from attachments (amongst other things), which means that attachments uploaded prior to this mod being installed will [b]NOT[/b] be able to rotated properly, as the EXIF information is missing from the re-encoded attachment file!

[color=blue][b][size=12pt][u]Admin Changes[/u][/size][/b][/color]
In [b]Admin[/b] => [b]Forum[/b] => [b]Attachments and Avatars[/b]:
o [b]Browse Files[/b]: There is a new column for image rotation.  Note that this column isn't shown when there are no images in the list.
o [b]File Maintenance[/b]: There is a new section with a button to clear the orientation flags in the database.

[color=blue][b][size=12pt][u]Post Screen Changes[/u][/size][/b][/color]
Beside each attachment, there is a dropbox for image rotation, listing the options in the following section.

[color=blue][b][size=12pt][u]Manual Rotation Options[/u][/size][/b][/color]
o No Change
o Horizontal Flip
o Rotate 180 degrees
o Vertical Flip
o Vertical Flip, Rotate 270 degrees clockwise. (90 degrees counter-clockwise)
o Rotate 270 degrees clockwise. (90 degrees counter-clockwise)
o Horizontal Flip, Rotate 270 degrees clockwise. (90 degrees counter-clockwise)
o Rotate 90 degrees clockwise.

[color=blue][b][size=12pt][u]Compatibility Notes[/u][/size][/b][/color]
This mod was tested on SMF 2.0.12 and 2.1 Beta 2, but should work on SMF 2.0 and up.  SMF 1.x is not and will not be supported.

[color=blue][b][size=12pt][u]Special Credit[/u][/size][/b][/color]
This mod relies on the [url=http://www.phpclasses.org/package/1042-PHP-EXIF-information-reader-and-writer.html]phpExifRW[/url] class, which is licensed under the [url=http://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html]GNU Lesser General Public License[/url], in order to read the EXIF information from image files.  This class makes the requirement of having EXIF support built-in, which some servers do not have, not important to the task of successfully pulling the orientation out of the image file.  The [b]exifReader.inc[/b] file was renamed to [b]Class-exifReader.php[/b] in order to name the file in accordance with the naming convention of SMF and included in this mod.

Test images with EXIF orientation values embedded in them are available at [url=http://www.galloway.me.uk/2012/01/uiimageorientation-exif-orientation-sample-images/]Galloway.me.uk[/url] and at the [url=http://www.elkarte.net/community/index.php?topic=2509.0] Image Orientation[/url] thread over at the ElkArte forum.

[color=blue][b][size=12pt][u]Changelog[/u][/size][/b][/color]
The changelog has been removed and can be seen at [url=http://www.xptsp.com/board/index.php?topic=662.msg975#msg975]XPtsp.com[/url].

[color=blue][b][size=12pt][u]License[/u][/size][/b][/color]
Copyright (c) 2016, Douglas Orend
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
