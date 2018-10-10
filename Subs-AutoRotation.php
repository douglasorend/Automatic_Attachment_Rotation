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

if (!function_exists('imageflip')) 
{
	define('IMG_FLIP_HORIZONTAL', 0);
	define('IMG_FLIP_VERTICAL', 1);
	define('IMG_FLIP_BOTH', 2);
}

//==============================================================================
// Function that gets the orientation stated in the image:
//==============================================================================
function AutoRotation_GetOrientation($filename)
{
	global $sourcedir;
	if (function_exists('exif_read_data'))
	{
		// FASTER: Find this with native code if native function already exists...
		$exif = @exif_read_data($filename);
		return isset($exif['IFD0']['Orientation']) ? $exif['IFD0']['Orientation'] : 0;
	}
	else
	{
		// SLOWER: Find this with the class if native function doesn't exist...
		require_once($sourcedir . '/Class-exifReader.php');
		$er = new phpExifReader($filename);
		$er->processFile();
		return isset($er->ImageInfo[TAG_ORIENTATION]) ? $er->ImageInfo[TAG_ORIENTATION] : 0;
	}
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments:
//==============================================================================
function AutoRotation_Process($filename, $format, $preferred_format = 0, $orientation = false)
{
	// A known and supported format?
	$format = str_replace('jpg', 'jpeg', $format);
	if (!$format || !function_exists('imagecreatefrom' . $format) && file_exists($filename))
		return false;

	// Make sure it is an orientation that we can fix:
	if ($orientation === false)
		$orientation = AutoRotation_GetOrientation($filename);
	if ($orientation < 2 || $orientation > 8)
		return false;

	// Load up the image.  Abort if failure:
	$imagecreatefrom = 'imagecreatefrom' . $format;
	if (!($src_img = @$imagecreatefrom($filename)))
		return false;
		
	// Rotate and/or flip the image so that it is correct:
	$success = true;
	if ($orientation == 2)		// -> Flipped horizontally
		$success = imageflip($src_img, IMG_FLIP_HORIZONTAL);
	elseif ($orientation == 3)	// -> Rotated 180 degrees clockwise
		$src_img = imagerotate($src_img, 180, 0);
	elseif ($orientation == 4)	// -> Flipped vertically
		$success = imageflip($src_img, IMG_FLIP_VERTICAL);
	elseif ($orientation == 5)	// -> Flipped vertically, then rotated 90 degrees counterclockwise
	{
		if ($success = imageflip($src_img, IMG_FLIP_VERTICAL))
			$src_img = imagerotate($src_img, -90, 0);
	}
	elseif ($orientation == 6)	// -> Rotated 90 degrees counterclockwise
		$src_img = imagerotate($src_img, -90, 0);
	elseif ($orientation == 7)	// -> Flipped horizontally, then rotated 90 degrees counterclockwise
	{
		if ($success = imageflip($src_img, IMG_FLIP_HORIZONTAL))
			$src_img = imagerotate($src_img, -90, 0);
	}
	elseif ($orientation == 8)	// -> Rotated 90 degrees clockwise
		$src_img = imagerotate($src_img, 90, 0);

	// Make sure that we are still dealing with an image in memory!
	if ($success && is_resource($src_img))
	{
		// Save the image as...
		if (!empty($preferred_format) && ($preferred_format == 3) && function_exists('imagepng'))
			$success = imagepng($src_img, $filename);
		elseif (!empty($preferred_format) && ($preferred_format == 1) && function_exists('imagegif'))
			$success = imagegif($src_img, $filename);
		elseif (function_exists('imagejpeg'))
			$success = imagejpeg($src_img, $filename);
	}
	if (is_resource($src_img))
		imagedestroy($src_img);
	return ($success ? $orientation : false);
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments during downloads:
//==============================================================================
function AutoRotation_Download($img_name, $img_ext, $id_thumb, $img_type)
{
	global $smcFunc, $context;

	// Rotate and/or flip the image according to EXIF information:
	$orientation = -1;
	if ($img_type == 3)
	{
		// This is a thumbnail!  Drats...  Gotta find the original and process it, too.... :(
		$request = $smcFunc['db_query']('', '
			SELECT a.id_folder, a.filename, a.file_hash, a.fileext, a.id_attach, a.attachment_type
			FROM {db_prefix}attachments AS a
				INNER JOIN {db_prefix}attachments AS thumb ON (a.id_thumb = thumb.id_attach)
			WHERE thumb.id_attach = {int:thumbnail}
			LIMIT 1',
			array(
				'thumbnail' => $id_thumb,
			)
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			// Get necessary information about the full-sized image:
			list($id_folder, $real_filename, $image_hash, $img_ext, $id_attach, $attachment_type) = $smcFunc['db_fetch_row']($request);
			$attachment = getAttachmentFilename($real_filename, $id_attach, $id_folder, false, $image_hash);

			// Prevent other instances of the forum from processing the image right now:
			AutoRotation_Update($id_attach);

			// Let's process this full image properly now, then update the database:
			$orientation = AutoRotation_Process($attachment, $img_ext);
			$size = @getimagesize($attachment);
			AutoRotation_Update($id_attach, $size[0], $size[1], $orientation);
		}
		$smcFunc['db_free_result']($request);
	}

	// Prevent other instances of the forum from processing the image right now:
	AutoRotation_Update($id_thumb);

	// Let's process this thumbnail properly now, then update the database:
	AutoRotation_Process($img_name, $image_ext, 0, $orientation);
	$size = @getimagesize($img_name);
	AutoRotation_Update($id_thumb, $size[0], $size[1], $orientation);
}

//==============================================================================
// Support functions:
//==============================================================================
function AutoRotation_Update($id_attach, $width = 0, $height = 0, $orientation = 1)
{
	global $smcFunc;
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}attachments
		SET proper_rotation = {int:rotated}' . (!empty($width) ? ', width = {int:width}' : '') . (!empty($height) ? ', height = {int:height}' : '') . '
		WHERE id_attach = {int:id_attach}',
		array(
			'id_attach' => (int) $id_attach,
			'rotated' => max(1, (int) $orientation),
			'width' => (int) $width,
			'height' => (int) $height
		)
	);
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments during display:
//==============================================================================
function AutoRotation_Display($row)
{
	global $smcFunc;

	// Is this an image OR has already been processed?  Abort if not:
	if (empty($row['width']) || empty($row['height']) || !empty($row['img_rotation']))
		return $row;

	// Rotate this image if necessary:
	$img = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);
	$orientation = AutoRotation_Process($img, $row['file_ext']);
	$size = @getimagesize($img);
	AutoRotation_Update($row['id_attach'], $row['width'] = $size[0], $row['height'] = $size[1], $orientation);

	// If there is a thumbnail that needs rotating, then let's rotate the thumbnail:
	if (isset($row['thumb_rotation']) && empty($row['thumb_rotation']) && $orientation > 1)
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
			// Get necessary information about the full-sized image:
			list($id_folder, $real_filename, $image_hash, $img_ext, $id_attach, $attachment_type) = $smcFunc['db_fetch_row']($request);
			$attachment = getAttachmentFilename($real_filename, $id_attach, $id_folder, false, $image_hash);

			// Prevent other instances of the forum from processing the image right now:
			AutoRotation_Update($id_attach);

			// Let's process this thumbnail properly now, then update the database:
			AutoRotation_Process($attachment, $img_ext, 0, $orientation);
			$size = @getimagesize($attachment);
			AutoRotation_Update($id_attach, $row['thumb_width'] = $size[0], $row['thumb_height'] = $size[1], $orientation);
		}
		$smcFunc['db_free_result']($request);
	}

	// Return all this information back to the caller:
	return $row;
}

//==============================================================================
// This replacement "imageflip" function gets used when using PHP < 5.5.
//==============================================================================
// Credit: Daniel Klein => http://php.net/manual/en/function.imageflip.php#118774
//==============================================================================
if (!function_exists('imageflip')) 
{
	function imageflip($image, $mode) 
	{
		if ($mode == IMG_FLIP_HORIZONTAL)
		{
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
		}
		elseif ($mode == IMG_FLIP_VERTICAL)
		{
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
		}
		elseif ($mode == IMG_FLIP_BOTH)
		{
			$sx = imagesx($image);
			$sy = imagesy($image);
			$temp_image = imagerotate($image, 180, 0);
			imagecopy($image, $temp_image, 0, 0, 0, 0, $sx, $sy);
		}
		else
			return false;

		imagedestroy($temp_image);
		return true;
	}
}

//==============================================================================
// This replacement "imagerotate" function gets used when using PHP < 4.3.
// NOTE: Parameters "bgd_color" and "ignore_transparent" are completely ignored!
//==============================================================================
// Credit: Ajenbo => http://php.net/manual/en/function.imagerotate.php#85329
//==============================================================================
if (!function_exists('imagerotate'))
{
	function imagerotate($image, $angle, $bgd_color = NULL, $ignore_transparent = NULL)
	{
		if ($angle == 180)
			return imagerotate(imagerotate($image, 90), 90);
		if ($angle != 270 && $angle != 90)
			return $image;
		$width = imagesx($image);
		$height = imagesy($image);
		$side = $width > $height ? $width : $height;
		$imageSquare = imagecreatetruecolor($side, $side);
		imagecopy($imageSquare, $image, 0, 0, 0, 0, $width, $height);
		imagedestroy($image);
		$imageSquare = imagerotate($imageSquare, $angle, 0, -1);
		$image = imagecreatetruecolor($height, $width);
		$x = $angle == 90 ? 0 : ($height > $width ? 0 : ($side - $height));
		$y = $angle == 270 ? 0 : ($height < $width ? 0 : ($side - $width));
		imagecopy($image, $imageSquare, 0, 0, $x, $y, $height, $width);
		imagedestroy($imageSquare);
		return $image;	
	}
}

//==============================================================================
// Admin functions for dealing with image rotation
//==============================================================================
function AutoRotation_AdminHook(&$subActions)
{
	global $context;
	$context['autorotation_hook'] = $subActions['remove'];
	$subActions['remove'] = 'AutoRotation_Rotate';
	$subActions['clearflags'] = 'AutoRotation_Clear';
}

function AutoRotation_Rotate()
{
	global $smcFunc, $context;

	// First, let's make sure that the session is valid!!!
	checkSession('post');

	// Start any user-requested image processing....
	if (!empty($_POST['orient']))
	{
		// Sanitize the inputs before we pass it to the database:
		$tmp = array();
		foreach ($_POST['orient'] as $attach => $orient)
			$tmp[(int) $attach] = (int) $orient;

		// This is a thumbnail!  Drats...  Gotta find the original and process it, too.... :(
		$request = $smcFunc['db_query']('', '
			SELECT 
				a.id_attach, a.id_folder, a.file_hash, t.id_attach AS thumb_id, a.filename, a.fileext,
				t.id_folder AS thumb_folder, t.file_hash AS thumb_hash, t.fileext as thumb_ext, t.filename AS thumb_name
			FROM {db_prefix}attachments AS a
				LEFT JOIN {db_prefix}attachments AS t ON (t.id_attach = a.id_thumb)
			WHERE a.id_attach IN ({array_int:message_list})',
			array(
				'message_list' => array_keys($tmp),
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// Get necessary information about the full-sized image:
			$format = ($row['fileext'] == 'png' ? 3 : ($row['fileext'] == 'gif' ? 1 : 0));
			$img = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);

			// Prevent other instances of the forum from processing the image right now:
			AutoRotation_Update($row['id_attach']);

			// Let's process this image properly now, then update the database:
			$orientation = AutoRotation_Process($img, $row['fileext'], $format, $tmp[$row['id_attach']]);
			$size = @getimagesize($img);
			AutoRotation_Update($row['id_attach'], $size[0], $size[1], $orientation);

			// Also process the thumbnail (if available):
			if (!empty($row['thumb_id']))
			{
				// Get necessary information about the full-sized image:
				$format = ($row['thumb_ext'] == 'png' ? 3 : ($row['thumb_ext'] == 'gif' ? 1 : 0));
				$img = getAttachmentFilename($row['thumb_name'], $row['thumb_attach'], $row['thumb_folder'], false, $row['thumb_hash']);

				// Prevent other instances of the forum from processing the image right now:
				AutoRotation_Update($row['thumb_id']);

				// Let's process this thumbnail properly now, then update the database:
				$orientation = AutoRotation_Process($img, $row['thumb_ext'], $format, $tmp[$row['thumb_id']]);
				$size = @getimagesize($img);
				AutoRotation_Update($row['thumb_id'], $size[0], $size[1], $orientation);
			}
		}
	}

	// Since we're chain-calling stuff, this MUST be called after processing the images!!!
	if (is_callable($context['autorotation_hook']))
		$context['autorotation_hook']();
}

function AutoRotation_Clear()
{
	global $smcFunc, $context;

	// Make sure that the session is valid, then clear orientation flags:
	checkSession('get');
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}attachments
		SET proper_rotation = {int:rotated}',
		array(
			'rotated' => 0,
		)
	);
	redirectexit('action=admin;area=manageattachments;sa=maintenance;' . $context['session_var'] . '=' . $context['session_id']);
}

?>