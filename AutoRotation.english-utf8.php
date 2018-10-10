<?php
/**********************************************************************************
* AutoRotation.english-utf8.php                                                        *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:   *
*	http://opensource.org/licenses/BSD-2-Clause                                   *
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but	  *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY	  *
* or FITNESS FOR A PARTICULAR PURPOSE.											  *
**********************************************************************************/
global $helptxt;

$txt['img_rotate_confirm'] = 'Are you sure you want to apply the image rotate/flip changes?';
$txt['img_orientation'] = 'Rotate/Flip';
$txt['img_orientation1'] = 'No Change';
$txt['img_orientation2'] = 'Horizontal Flip';
$txt['img_orientation3'] = 'Rotate 180&deg;';
$txt['img_orientation4'] = 'Vertical Flip';
$txt['img_orientation5'] = 'Vertical Flip, Rotate 90&deg; Right';
$txt['img_orientation6'] = 'Rotate 90&deg; Right';
$txt['img_orientation7'] = 'Horizontal Flip, Rotate 90&deg; Right';
$txt['img_orientation8'] = 'Rotate 90&deg; Left';
$txt['img_rotate'] = 'Rotate/Flip Images';
$txt['attachment_clear_rotation_title'] = 'Reset Orientation Flag';
$txt['attachment_clear_rotation_desc'] = 'This function will clear the orientation flags in the database for all image attachments.  Please note that this function cannot fix images that do not contain the EXIF information, such as images that SMF has re-encoded.  This operation does <strong>NOT</strong> change the images at this time, but signals the images to be reprocessed at post display time.';
$txt['attachment_clear_rotation_button'] = 'Clear Orientation Flags';
$txt['AutoRotation_log_error'] = 'Log memory error while rotating image?';
$txt['AutoRotation_memory_issue'] = 'Unable to allocate %1$d of memory for image rotation.';

// Added for Automatic Attachment Rotation (and Resize).
$txt['attachment_auto_rotate'] = 'Automatically rotate images<div class="smalltext">(Only possible for JPEG images containing EXIF orientation data)</div>';
$txt['attachment_image_reformat'] = 'Reformat non-JPEG images to JPEG';
$txt['attachment_resize_existing'] = 'Resize existing images';
$txt['attachment_resize_backup'] = 'Keep backup of original image file when resizing';
$txt['attachment_jpeg_quality'] = 'JPEG quality factor<div class="smalltext">(Maximum 100, default 100)</div>';
$txt['attachment_image_width'] = 'Maximum width of attached images<div class="smalltext">(0 for no maximum width)</div>';
$txt['attachment_image_height'] = 'Maximum height of attached images<div class="smalltext">(0 for no maximum height)</div>';

$txt['pm_attachment_image_reformat'] = 'Reformat non-JPEG images to JPEG';
$txt['pm_attachment_jpeg_quality'] = 'JPEG quality factor<div class="smalltext">(Maximum 100, default 100)</div>';
$txt['pm_attachment_image_width'] = 'Maximum width of attached images<div class="smalltext">(0 for no maximum width)</div>';
$txt['pm_attachment_image_height'] = 'Maximum height of attached images<div class="smalltext">(0 for no maximum height)</div>';

$helptxt['attachment_image_reformat'] = 'Selecting this option will reformat non-JPEG images as JPEG';
$helptxt['attachment_resize_existing'] = 'Selecting this option will resize any existing images which are larger than the set dimensions for attached images.';
$helptxt['attachment_resize_backup'] = 'If this option is enabled (and the option <i>\'Resize existing images\'</i> is also enabled) the original image file is saved in the directory <i>attachmentsPreREI</i> in the default forum directory.';
$helptxt['attachment_jpeg_quality'] = 'This sets the JPEG quality factor. A higher number increases image quality but also increases the attachment file size.';
$helptxt['pm_attachment_image_reformat'] = 'Selecting this option will reformat non-JPEG images as JPEG';
$helptxt['pm_attachment_jpeg_quality'] = 'This sets the JPEG quality factor. A higher number increases image quality but also increases the attachment file size.';

?>
