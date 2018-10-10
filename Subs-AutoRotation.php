<?php
/**********************************************************************************
* Subs-AutoRotation.php														   *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:   *
*	http://opensource.org/licenses/BSD-2-Clause								   *
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
		if (isset($exif['IFD0']['Orientation']))
			return $exif['IFD0']['Orientation'];
		elseif (isset($exif['Orientation']))
			return $exif['Orientation'];
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
function AutoRotation_Process($filename, $orientation = false, $jpegQuality = 100)
{
	global $context, $modSettings;
	static $default_formats = array(
		'1' => 'gif',
		'2' => 'jpeg',
		'3' => 'png',
		'6' => 'bmp',
		'15' => 'wbmp'
	);

	// Does the file even exist?  If not, return false!!
	if (!file_exists($filename))
		return false;

	// A known and supported format?
	$info = @getimagesize($filename);
	if (!$info)
		return false;
	$width = $info[0];
	$height = $info[1];
	$format = isset($default_formats[$info[2]]) ? $default_formats[$info[2]] : false;
	if (!$format || !function_exists('imagecreatefrom' . $format))
		return false;

	// Make sure it is an orientation that we can fix:
	if ($orientation === false)
		$orientation = AutoRotation_GetOrientation($filename);
	if ($orientation < 2 || $orientation > 8)
		return 1;

	// Let's attempt to allocate enough memory for this:
	if (!empty($modSettings['ipmemlimit']) && $modSettings['ipmemlimit'] > 0)
	{
		// Image Processing Memory Limit
		// Do we know the bit depth?
		if (!empty($info['bits']) && !empty($info['channels']))
			$bitDepth = $info['bits'] * $info['channels'];
		else
			$bitDepth = 96; // Probally a bad idea assuming, was 48 now 96 for 32bpp.

		// How much do we need and how much have we used. 8 bits to bytes, 65536 64k and 1.8 to add a little extra.
		$need = round(((($info[0] * $info[1] * $bitDepth / 8) + 65536 + memory_get_usage()) * 1.8) / 1048576);
		$have = (int) ini_get('memory_limit');

		// We need to set a hard limit, can't have them using all the memory
		if ($need > $modSettings['ipmemlimit'])
			return false;

		// Lets not care how much we have and just set it.
		if (ini_set('memory_limit', $modSettings['ipmemlimit'].'M') === false && $need > $have)
			return false; // Server rejected
	}
	elseif (function_exists('memory_get_usage'))
	{
		// Memory calculation goes here:
		$info['bits'] = !empty($info['bits']) ? $info['bits'] : 8;
		$info['channels'] = !empty($info['channels']) ? $info['channels'] : 4;
		$memoryNeeded = ceil($width * $height * $info['bits'] * $info['channels'] / 8 * 2);
		$currentMemory = memory_get_usage() + $memoryNeeded;
		$memoryLimit = (int) ((float) ini_get('memory_limit'));
		$memoryRequested = $memoryLimit + ceil(($currentMemory - $memoryLimit * pow(1024, 2)) / pow(1024, 2));
		
		// Do we have enough memory?  If not, try to allocate enough:
		if ($currentMemory > (integer) ini_get('memory_limit') * pow(1024, 2))
			@ini_set('memory_limit', $memoryRequested);

		// Were we successful?  If not, log the error if option is set:
		if ($currentMemory > (integer) ini_get('memory_limit') * pow(1024, 2))
		{
			if (!empty($modSettings['AutoRotation_log_error']))
			{
				loadLanguage('AutoRotation');
				log_error(sprintf('AutoRotation_memory_issue', $memoryRequested));
			}
			return false;
		}
	}
	else
	// FALLBACK METHOD: Try to allocate 256MB of memory....
		@ini_set('memory_limit', '256M');

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
		if (function_exists($func = 'image' . $format))
			$success = @$func($src_img, $filename);
		else
			$success = imagejpeg($src_img, $filename, $jpegQuality);
	}
	if (is_resource($src_img))
		imagedestroy($src_img);
	return ($success ? $orientation : false);
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments during downloads:
//==============================================================================
function AutoRotation_Download($img_name, $id_thumb, $img_type)
{
	global $smcFunc, $context;

	// Rotate and/or flip the image according to EXIF information:
	$orientation = -1;
	if ($img_type == 3)
	{
		// This is a thumbnail!  Drats...  Gotta find the original and process it, too.... :(
		$request = $smcFunc['db_query']('', '
			SELECT a.id_folder, a.filename, a.file_hash, a.id_attach
			FROM {db_prefix}attachments AS a
				INNER JOIN {db_prefix}attachments AS thumb ON (a.id_thumb = thumb.id_attach)
			WHERE thumb.id_attach = {int:thumbnail}
			LIMIT 1',
			array(
				'thumbnail' => $id_thumb,
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// Prevent other instances of the forum from processing the image right now:
			AutoRotation_Update($id_attach);

			// Let's process this full image properly now, then update the database:
			$attachment = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);
			$orientation = AutoRotation_Process($attachment);
			$size = @getimagesize($attachment);
			AutoRotation_Update($id_attach, $size[0], $size[1], $orientation);
		}
		$smcFunc['db_free_result']($request);
	}

	// Prevent other instances of the forum from processing the image right now:
	AutoRotation_Update($id_thumb);

	// Let's process this thumbnail properly now, then update the database:
	AutoRotation_Process($img_name, $orientation);
	$size = @getimagesize($img_name);
	AutoRotation_Update($id_thumb, $size[0], $size[1], $orientation);
}

//==============================================================================
// Support functions:
//==============================================================================
function AutoRotation_Update($id_attach, $width = 0, $height = 0, $orientation = 1, $jpegQuality = 100)
{
	global $smcFunc;
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}attachments
		SET proper_rotation = {int:rotated}' .
			(!empty($width) ? ', width = {int:width}' : '') .
			(!empty($height) ? ', height = {int:height}' : '') .
			(!empty($jpegQuality) ? ', jpeg_quality = {int:jpeg_quality}' : '') . '
		WHERE id_attach = {int:id_attach}',
		array(
			'id_attach' => (int) $id_attach,
			'rotated' => max(1, (int) $orientation),
			'width' => (int) $width,
			'height' => (int) $height,
			'jpeg_quality' => (int) $jpegQuality
		)
	);
}

//==============================================================================
// Function dealing with Auto-Rotation of attachments during display:
//==============================================================================
function AutoRotation_Display($row)
{
	global $smcFunc, $modSettings;

	// Is this an image OR has already been processed?  Abort if not:
	if (empty($row['width']) || empty($row['height']) || !empty($row['img_rotation']))
		return $row;

	// Rotate this image if necessary:
	$img = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);
	$orientation = AutoRotation_Process($img);
	$size = @getimagesize($img);
	$jpegQuality = min($row['jpeg_quality'], empty($modSettings['attachment_jpeg_quality']) ? 100 : $modSettings['attachment_jpeg_quality']);
	AutoRotation_Update($row['id_attach'], $row['width'] = $size[0], $row['height'] = $size[1], $orientation, $jpegQuality);

	// If there is a thumbnail that needs rotating, then let's rotate the thumbnail:
	if (!empty($row['thumb_id']) && $orientation > 1)
	{
		// Prevent other instances of the forum from processing the image right now:
		AutoRotation_Update($row['thumb_id']);

		// Let's process this thumbnail properly now, then update the database:
		$img = getAttachmentFilename($row['thumb_filename'], $row['thumb_id'], $row['attach_folder'], false, $row['thumb_hash']);
		AutoRotation_Process($img, $orientation);
		$size = @getimagesize($img);
		AutoRotation_Update($row['thumb_id'], $row['thumb_width'] = $size[0], $row['thumb_height'] = $size[1], $orientation);
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
	function imagerotate($src_img, $angle, $bgd_color = 0, $ignore_transparent = 0)
	{
		$src_x = imagesx($src_img);
		$src_y = imagesy($src_img);
		if ($angle == 180)
		{
			$dest_x = $src_x;
			$dest_y = $src_y;
		}
		elseif ($src_x <= $src_y)
		{
			$dest_x = $src_y;
			$dest_y = $src_x;
		}
		elseif ($src_x >= $src_y)
		{
			$dest_x = $src_y;
			$dest_y = $src_x;
		}

		$rotate = imagecreatetruecolor($dest_x, $dest_y);
		imagealphablending($rotate, false);

		switch ($angle)
		{
			case 270:
				for ($y = 0; $y < $src_y; $y++)
				{
					for ($x = 0; $x < $src_x; $x++)
					{
						$color = imagecolorat($src_img, $x, $y);
						imagesetpixel($rotate, $dest_x - $y - 1, $x, $color);
					}
				}
				break;
			case 90:
				for ($y = 0; $y < $src_y; $y++)
				{
					for ($x = 0; $x < $src_x; $x++)
					{
						$color = imagecolorat($src_img, $x, $y);
						imagesetpixel($rotate, $y, $dest_y - $x - 1, $color);
					}
				}
				break;
			case 180:
				for ($y = 0; $y < $src_y; $y++)
				{
					for ($x = 0; $x < $src_x; $x++)
					{
						$color = imagecolorat($src_img, $x, $y);
						imagesetpixel($rotate, $dest_x - $x - 1, $dest_y - $y - 1, $color);
					}
				}
				break;
			default: 
				$rotate = $src_img; 
		}
		return $rotate; 
	}
}

//==============================================================================
// Admin functions for dealing with image rotation
//==============================================================================
function AutoRotation_LoadHelp()
{
	loadLanguage('AutoRotation');
}

function AutoRotation_AdminHook(&$subActions)
{
	global $context;
	loadLanguage('AutoRotation');
	if (isset($subActions['remove']))
		$context['autorotation_hook'] = $subActions['remove'];
	$subActions['remove'] = 'AutoRotation_Rotate';
	$subActions['clearflags'] = 'AutoRotation_Clear';
}

function AutoRotation_Rotate()
{
	global $smcFunc, $context, $modSettings;

	// First, let's make sure that the session is valid!!!
	checkSession('post');

	// Start any user-requested image processing....
	if (!empty($_POST['orient']))
	{
		// Sanitize the inputs before we pass it to the database:
		$attach = array();
		foreach ($_POST['orient'] as $id => $orient)
			$attach[(int) $id] = (int) $orient;

		// This is a thumbnail!  Drats...  Gotta find the original and process it, too.... :(
		$request = $smcFunc['db_query']('', '
			SELECT
				a.id_attach, a.id_folder, a.file_hash, a.filename, a.jpeg_quality, t.id_attach AS thumb_id,
				t.id_folder AS thumb_folder, t.file_hash AS thumb_hash, t.filename AS thumb_name
			FROM {db_prefix}attachments AS a
				LEFT JOIN {db_prefix}attachments AS t ON (t.id_attach = a.id_thumb)
			WHERE a.id_attach IN ({array_int:attach_list})
				AND a.width > {int:zero}
				AND a.height > {int:zero}',
			array(
				'attach_list' => array_keys($attach),
				'zero' => 0,
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// Prevent other instances of the forum from processing the image right now:
			AutoRotation_Update($row['id_attach']);

			// Let's process this image properly now, then update the database:
			$img = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);
			$jpegQuality = min($row['jpeg_quality'], empty($modSettings['attachment_jpeg_quality']) ? 100 : $modSettings['attachment_jpeg_quality']);
			AutoRotation_Process($img, $orientation = $attach[$row['id_attach']], $jpegQuality);
			$size = @getimagesize($img);
			AutoRotation_Update($row['id_attach'], $size[0], $size[1], $orientation, $jpegQuality);

			// Also process the thumbnail (if available):
			if (!empty($row['thumb_id']))
			{
				// Prevent other instances of the forum from processing the image right now:
				AutoRotation_Update($row['thumb_id']);

				// Let's process this thumbnail properly now, then update the database:
				$img = getAttachmentFilename($row['thumb_name'], $row['thumb_id'], $row['thumb_folder'], false, $row['thumb_hash']);
				AutoRotation_Process($img, $orientation);
				$size = @getimagesize($img);
				AutoRotation_Update($row['thumb_id'], $size[0], $size[1], $orientation);
			}
		}
		$smcFunc['db_free_result']($request);
	}

	// Since we're chain-calling stuff, this MUST be called after processing the images!!!
	if (isset($context['autorotation_hook']) && is_callable($context['autorotation_hook']))
		$context['autorotation_hook']();
}

function AutoRotation_Clear()
{
	global $smcFunc, $context;

	// Make sure that the session is valid and the user can manage attachments:
	checkSession('get');
	isAllowedTo('manage_attachments');

	// Clear orientation flags, then go back to maintenance screen:
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}attachments
		SET proper_rotation = {int:rotated}',
		array(
			'rotated' => 0,
		)
	);
	redirectexit('action=admin;area=manageattachments;sa=maintenance;' . $context['session_var'] . '=' . $context['session_id']);
}

//==============================================================================
// Image Resizing functionality added by GL700Wing:
//==============================================================================
function AutoRotation_Inbound($index, $pm = false)
{
	global $modSettings, $sourcedir;

	// Does this index actually exist?  If not, return to caller:
	if (!isset($_FILES['attachment']['tmp_name'][$index]))
		return;

	// Figure some things out:
	$pre = $pm ? 'pm_' : '';
	$filename = $_FILES['attachment']['tmp_name'][$index];
	$sizeLimit = empty($modSettings[($pm ? 'pmA' : 'a') . 'ttachmentSizeLimit']) ? 0 : max(0, $modSettings[($pm ? 'pmA' : 'a') . 'ttachmentSizeLimit']) * 1024;
	$jpegQuality = min(100, empty($modSettings[$pre . 'attachment_jpeg_quality']) ? 100 : $modSettings[$pre . 'attachment_jpeg_quality']);
	$imageRotated = false;

	// Find out image mime.
	$imageSize = @getimagesize($filename);

	// If it's a JPEG image rotate it if necessary.
	if (!empty($modSettings['attachment_auto_rotate']) && $imageSize['mime'] == 'image/jpeg')
	{
		// Disable image reencoding to enable image rotation.
		$modSettings['attachment_image_reencode'] = false;

		@ini_set('gd.jpeg_ignore_warning', 1);
		$image = @imagecreatefromjpeg($filename);

		// Rotate JPEG image according to Exif orientation.
		$imageExif = @exif_read_data($filename);
		if (isset($imageExif['Orientation']) || array_key_exists('Orientation', $imageExif))
		{
			if ($_FILES['attachment']['size'][$index] <= $sizeLimit)
			{
				$imageOrientation = $imageExif['Orientation'];
				switch($imageOrientation)
				{
					case 3:
						$image = imagerotate($image, 180, 0);
						break;
					case 6:
						$image = imagerotate($image, -90, 0);
						break;
					case 8:
						$image = imagerotate($image, 90, 0);
						break;
				}

				$imageRotated = true;
			}
		}

		// Create the JPEG file with maximum quality of 100%.
		imagejpeg($image, $filename, $jpegQuality);
		imagedestroy($image);
	}

	// Resize/reformat image and rotate it if necessary.
	if (list ($width, $height, $type) = @getimagesize($filename))
	{
		if (AutoRotation_Aspect($width, $height, $pre) || empty($sizeLimit) || $jpegQuality != 100)
		{
			// If the image should be reformatted to JPEG.
			// Note: By default BMP images are reformatted to JPEG by resizeImageFile().
			if (!empty($modSettings[$pre . 'attachment_image_reformat']) || $type == 6 )
				$preferred_format = 2;
			else
				$preferred_format = $type;

			// Resize the image (and rotate it if necessary).
			require_once($sourcedir . '/Subs-Graphics.php');
			if (resizeImageFile($filename, $filename . '.temp', $width, $height, $preferred_format, $jpegQuality))
			{
				// Delete the old tmp file and rename the new one.
				unlink($filename);
				rename($filename . '.temp', $filename);

				// If it's a JPEG image rotate and/or compress the image.
				if (!empty($modSettings['attachment_auto_rotate'])  && ($imageSize['mime'] == 'image/jpeg' && !$imageRotated))
				{
					// Disable image reencoding to enable image rotation.
					$modSettings['attachment_image_reencode'] = false;

					@ini_set ('gd.jpeg_ignore_warning', 1);
					$image = @imagecreatefromjpeg($filename);

					// Rotate JPEG image according to Exif orientation.
					if (isset($imageExif['Orientation']) || array_key_exists('Orientation', $imageExif))
					{
						$imageOrientation = $imageExif['Orientation'];
						switch($imageOrientation)
						{
							case 3:
								$image = imagerotate($image, 180, 0);
								break;
							case 6:
								$image = imagerotate($image, -90, 0);
								break;
							case 8:
								$image = imagerotate($image, 90, 0);
								break;
						}
					}

					// Create the JPEG file with maximum quality of 100%.
					imagejpeg($image, $filename, $jpegQuality);
					imagedestroy($image);
				}

				// Recheck the file size
				$_FILES['attachment']['size'][$index] = filesize($filename);

				// Change the file suffix to 'jpg' if necessary.
				$filename = $_FILES['attachment']['name'][$index];
				if ($preferred_format == 2 && strrchr($filename, '.') != '.jpg')
					$_FILES['attachment']['name'][$index] = substr($filename, 0, -(strlen(strrchr($filename, '.')))) . '.jpg';
			}
		}
	}
}

function AutoRotation_Aspect(&$width, &$height, $pre = '')
{
	global $modSettings;

	// If image dimensions are equal to or below admin settings, don't continue:
	$max_width = !empty($modSettings[$pre . 'attachment_image_width']) ? $modSettings[$pre . 'attachment_image_width'] : $width;
	$max_height = !empty($modSettings[$pre . 'attachment_image_height']) ? $modSettings[$pre . 'attachment_image_height'] : $height;
	if ($width <= $max_width && $height <= $max_height)
		return false;

	// Adjust size of image, maintaining aspect ratio of image.  Note that the code rounds
	// the decimals up to the next number, to avoid creating images with a width/height of 0!
	$aspect_ratio = min($max_width / $width, $max_height / $height);
	$width  = (int) ceil($width * $aspect_ratio);
	$height = (int) ceil($height * $aspect_ratio);
	return true;
}

?>
