<?php
/**********************************************************************************
* Subs-AutoRotation.php                                                           *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:   *
*	http://opensource.org/licenses/BSD-2-Clause                                   *
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but	  *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY	  *
* or FITNESS FOR A PARTICULAR PURPOSE.											  *
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

//==============================================================================
// PHP < 5.5 doesn't have "imageflip" function.  Replacement function here:
//==============================================================================
if (!function_exists('imageflip')) 
{
	define('IMG_FLIP_HORIZONTAL', 0);
	define('IMG_FLIP_VERTICAL', 1);
	define('IMG_FLIP_BOTH', 2);

	function imageflip($image, $mode) 
	{
		switch ($mode) 
		{
			case IMG_FLIP_HORIZONTAL:
				$max_x = imagesx($image) - 1;
				$half_x = $max_x / 2;
				$sy = imagesy($image);
				$temp_image = imageistruecolor($image) ? imagecreatetruecolor(1, $sy) : imagecreate(1, $sy);
				for ($x = 0; $x < $half_x; ++$x) 
				{
					imagecopy($temp_image, $image, 0, 0, $x, 0, 1, $sy);
					imagecopy($image, $image, $x, 0, $max_x - $x, 0, 1, $sy);
					imagecopy($image, $temp_image, $max_x - $x, 0, 0, 0, 1, $sy);
				}
				break;

			case IMG_FLIP_VERTICAL: 
				$sx = imagesx($image);
				$max_y = imagesy($image) - 1;
				$half_y = $max_y / 2;
				$temp_image = imageistruecolor($image) ? imagecreatetruecolor($sx, 1) : imagecreate($sx, 1);
				for ($y = 0; $y < $half_y; ++$y) 
				{
					imagecopy($temp_image, $image, 0, 0, 0, $y, $sx, 1);
					imagecopy($image, $image, 0, $y, 0, $max_y - $y, $sx, 1);
					imagecopy($image, $temp_image, 0, $max_y - $y, 0, 0, $sx, 1);
				}
				break;

			case IMG_FLIP_BOTH: 
				$sx = imagesx($image);
				$sy = imagesy($image);
				$temp_image = imagerotate($image, 180, 0);
				imagecopy($image, $temp_image, 0, 0, 0, 0, $sx, $sy);
				break;
		}
		imagedestroy($temp_image);
		return $image;
	}
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments:
//==============================================================================
function AutoRotation_Process($filename, $format, $preferred_format = 0, $orientation = false)
{
	global $sourcedir;

	// A known and supported format?
	if (!$format || !function_exists('imagecreatefrom' . $format))
		return false;

	// Make sure it is an orientation that we can fix:
	if ($orientation === false)
	{
		if (function_exists('exif_read_data'))
		{
			// Find this with native code if the function already exists:
			$exif = @exif_read_data($filename);
			$orientation = isset($exif['IFD0']['Orientation']) ? $exif['IFD0']['Orientation'] : 0;
		}
		else
		{
			// Find this with the class if the function doesn't exist:
			require_once($sourcedir . '/Class-exifReader.php');
			$er = new phpExifReader($filename);
			$er->processFile();
			$orientation = isset($er->ImageInfo[TAG_ORIENTATION]) ? $er->ImageInfo[TAG_ORIENTATION] : 0;
		}
	}
	if ($orientation < 2)
		return false;

	// Load up the image and rotate the image so that it is correct:
	$imagecreatefrom = 'imagecreatefrom' . $format;
	if (!$src_img = @$imagecreatefrom($filename))
		return false;
	switch($orientation)
	{
		case 2:
			$src_img = imageflip($src_img, IMG_FLIP_HORIZONTAL);
			break;
		case 3:
			$src_img = imagerotate($src_img, 180, 0);
			break;
		case 4:
			$src_img = imageflip($src_img, IMG_FLIP_VERTICAL);
			break;
		case 5:
			$src_img = imageflip($src_img, IMG_FLIP_VERTICAL);
			$src_img = imagerotate($src_img, -90, 0);
			break;
		case 6:
			$src_img = imagerotate($src_img, -90, 0);
			break;
		case 7:
			$src_img = imageflip($src_img, IMG_FLIP_HORIZONTAL);
			$src_img = imagerotate($src_img, -90, 0);
			break;
		case 8:
			$src_img = imagerotate($src_img, 90, 0);
			break;
	}

	// Save the image as ...
	if (!empty($preferred_format) &&($preferred_format == 3) && function_exists('imagepng'))
		$success = imagepng($src_img, $filename);
	elseif (!empty($preferred_format) &&($preferred_format == 1) && function_exists('imagegif'))
		$success = imagegif ($src_img, $filename);
	elseif (function_exists('imagejpeg'))
		$success = imagejpeg($src_img, $filename);
	return ($success ? $orientation : false);
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments during downloads:
//==============================================================================
function AutoRotation_Download($img_name, $img_ext, $id_thumb, $img_type)
{
	global $smcFunc, $context;

	// Rotate and/or flip the image according to EXIF information:
	$orientation = - 1;
	if ($img_type == 3)
	{
		// This is a thumbnail!  Drats...  Gotta find the original and process it, too.... :(
		$request = $smcFunc['db_query']('', '
			SELECT a.id_folder, a.filename, a.file_hash, a.fileext, a.id_attach, a.attachment_type
			FROM {db_prefix}attachments AS a
				INNER JOIN {db_prefix}attachments AS thumb ON(a.id_thumb = thumb.id_attach)
			WHERE a.id_attach = {int:thumbnail}
			LIMIT 1',
			array(
				'thumbnail' => $id_thumb,
			)
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			// Get necessary information about the full-sized image:
			list($id_folder, $real_filename, $image_hash, $img_ext, $id_attach, $attachment_type) = $smcFunc['db_fetch_row']($request);

			// If the full-size image hasn't been processed, let's do so now:
			$attachment = getAttachmentFilename($real_filename, $id_attach, $id_folder, false, $image_hash);
			$orientation = AutoRotation_Process($attachment, $img_ext);
			$context['AR_full'] = array(
				'filename' => $attachment,
				'size' => $size = @getimagesize($attachment)
			);

			// Change the width, height and processing status of this attachment:
			AutoRotation_Update($id_attach, $size[0], $size[1]);
		}
		$smcFunc['db_free_result']($request);
	}

	// Let's process this thumbnail properly now:
	AutoRotation_Process($img_name, $image_ext, 0, $orientation);
	$context['AR_thumb'] = array(
		'filename' => $img_name,
		'size' => $size = @getimagesize($img_name)
	);

	// Change the width, height and processing status of this attachment:
	AutoRotation_Update($id_thumb, $size[0], $size[1]);
}

//==============================================================================
// Support functions:
//==============================================================================
function AutoRotation_Update($id_attach, $width, $height)
{
	global $smcFunc;
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}attachments
		SET proper_rotation = {int:rotated}, width = {int:width}, height = {int:height}
		WHERE id_attach = {int:id_attach}',
		array(
			'id_attach' => $id_attach,
			'rotated' => 1,
			'width' => $width,
			'height' => $height
		)
	);
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments during display:
//==============================================================================
function AutoRotation_Display($row)
{
	global $smcFunc;

	// Check if full-sized image has been processed for auto-rotation:
	$orientation = false;
	if (empty($row['img_rotation']))
	{
		$img = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);
		$orientation = AutoRotation_Process($img, $row['file_ext']);
		$size = @getimagesize($img);
		AutoRotation_Update($row['id_attach'], $row['width'] = $size[0], $row['height'] = $size[1]);
	}

	// Check if thumbnail image has been processed for auto-rotation:
	if (empty($row['thumb_rotation']))
	{
		$request = $smcFunc['db_query']('', '
			SELECT thumb.id_folder, thumb.filename, thumb.file_hash, thumb.fileext, thumb.id_attach, thumb.attachment_type
			FROM {db_prefix}attachments AS thumb
				INNER JOIN {db_prefix}attachments AS a ON (thumb.id_attach = a.id_thumb)
			WHERE a.id_attach = {int:thumbnail}
			LIMIT 1',
			array(
				'thumbnail' => $row['id_attach'],
			)
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			list($id_folder, $real_filename, $image_hash, $img_ext, $id_attach, $attachment_type) = $smcFunc['db_fetch_row']($request);
			$attachment = getAttachmentFilename($real_filename, $id_attach, $id_folder, false, $image_hash);
			AutoRotation_Process($attachment, $img_ext, 0, $orientation);
			$size = @getimagesize($attachment);
			AutoRotation_Update($id_attach, $row['thumb_width'] = $size[0], $row['thumb_height'] = $size[1]);
		}
	}

	// Return all this information back to the caller:
	return $row;
}

?>