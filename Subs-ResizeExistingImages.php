<?php

ini_set("display_errors",1);

if (!defined('SMF'))
	die('Hacking attempt ...');

// Show a list of images available for resizing.
// - called by ?action=admin;area=manageattachments;sa=resizebrowse;resize.
// - uses the 'browse' sub template.
// - allows sorting by name, size, dimensions, JPEG quality, message and date.
// - paginates results.
function ResizeBrowse()
{
	global $context, $txt, $scripturl, $modSettings, $sourcedir, $forum_version;

	loadLanguage('ResizeExistingImages');

	$context['sub_template'] = 'browse';
	$context['browse_type'] = 'resize';

	// Are we running SMF 2.0.x or SMF 2.1?
	$smf20 = (substr($forum_version, 0, 7) == 'SMF 2.0');
	if ($smf20)
		$sa = "resizebrowse";
	else
		$sa = "browse;resize";

	// Set the options for the list.
	$listOptions = array(
		'id' => 'file_list',
		'title' => (empty($modSettings['attachment_image_reformat']) ? $txt['resize_images'] : $txt['resize_reformat_images']),
		'items_per_page' => $modSettings['defaultMaxMessages'],
		'base_href' => $scripturl . '?action=admin;area=manageattachments;sa=' . $sa,
		'default_sort_col' => 'filesize',
		'no_items_label' => $txt['resize_images_empty_desc'],
		'get_items' => array(
			'function' => 'resize_getFiles',
		),
		'get_count' => array(
			'function' => 'resize_getNumFiles',
		),
		'columns' => array(
			'name' => array(
				'header' => array(
					'value' => $txt['attachment_name'],
				),
				'data' => array(
					'function' => create_function('$rowData', '
						global $scripturl;
						$link = \'<a href="\';
						$time = filemtime(getAttachmentFilename($rowData[\'filename\'], $rowData[\'id_attach\'], $rowData[\'id_folder\'], false, $rowData[\'file_hash\']));
						$link .= sprintf(\'%1$s?action=dlattach;topic=%2$d.0;attach=%3$d;ts=%4$d\', $scripturl, $rowData[\'id_topic\'], $rowData[\'id_attach\'], $time);
						$link .= \'"\';
						$link .= sprintf(\' onclick="return reqWin(this.href\' . ($rowData[\'attachment_type\'] == 1 ? \'\' : \' + \\\';image\\\'\') . \', %1$d, %2$d, true);"\', $rowData[\'width\'] + 20, $rowData[\'height\'] + 20);
						$link .= sprintf(\'>%1$s</a>\', preg_replace(\'~&amp;#(\\\\d{1,7}|x[0-9a-fA-F]{1,6});~\', \'&#\\\\1;\', htmlspecialchars($rowData[\'filename\'])));
						return $link;
					'),
				),
				'sort' => array(
					'default' => 'a.filename',
					'reverse' => 'a.filename DESC',
				),
			),
			'filesize' => array(
				'header' => array(
					'value' => $txt['attachment_file_size'],
				),
				'data' => array(
					'function' => create_function('$rowData', '
						global $txt;
						return sprintf(\'%1$s%2$s\', round($rowData[\'size\'] / 1024, 2), $txt[\'kilobyte\']);
					'),
				),
				'sort' => array(
					'default' => 'a.size DESC',
					'reverse' => 'a.size',
				),
			),
			'width' => array(
				'header' => array(
					'value' => $txt['resize_image_width'],
				),
				'data' => array(
					'function' => create_function('$rowData', '
						return $rowData[\'width\'];
					'),
				),
				'sort' => array(
					'default' => 'a.width DESC',
					'reverse' => 'a.width',
				),
			),
			'height' => array(
				'header' => array(
					'value' => $txt['resize_image_height'],
				),
				'data' => array(
					'function' => create_function('$rowData', '
						return $rowData[\'height\'];
					'),
				),
				'sort' => array(
					'default' => 'a.height DESC',
					'reverse' => 'a.height',
				),
			),
			'jpeg_quality' => array(
				'header' => array(
					'value' => $txt['resize_image_jpeg_quality'],
				),
				'data' => array(
					'function' => create_function('$rowData', '
						return $rowData[\'jpeg_quality\'];
					'),
				),
				'sort' => array(
					'default' => 'a.jpeg_quality DESC',
					'reverse' => 'a.jpeg_quality',
				),
			),
			'post' => array(
				'header' => array(
					'value' => $txt['subject'],
				),
				'data' => array(
					'function' => create_function('$rowData', '
						global $scripturl;
						$post = sprintf(\'<a href="%1$s?topic=%2$d.msg%3$d#msg%3$d">%4$s</a>\', $scripturl, $rowData[\'id_topic\'], $rowData[\'id_msg\'], $rowData[\'subject\']);
						return $post;
					'),
				),
				'sort' => array(
					'default' => 'm.subject',
					'reverse' => 'm.subject DESC',
				),
			),
			'date' => array(
				'header' => array(
					'value' => $txt['date'],
				),
				'data' => array(
					'function' => create_function('$rowData', '
						global $txt;
						$date = empty($rowData[\'poster_time\']) ? $txt[\'never\'] : timeformat($rowData[\'poster_time\']);
						return $date;
					'),
				),
				'sort' => array(
					'default' => 'm.poster_time',
					'reverse' => 'm.poster_time DESC',
				),
			),
			'check' => array(
				'header' => array(
					'value' => '<input type="checkbox" onclick="invertAll(this, this.form);" class="input_check" />',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<input type="checkbox" name="resize[%1$d]" class="input_check" />',
						'params' => array(
							'id_attach' => false,
						),
					),
				),
			),
		),
		'form' => array(
			'href' => $scripturl . '?action=admin;area=manageattachments;sa=resizeselect',
			'include_sort' => true,
			'include_start' => true,
		),
		'additional_rows' => array(
			array(
				'position' => 'below_table_data',
				'value' => '<input type="submit" name="resize_submit" class="button_submit" value="' . (empty($modSettings['attachment_image_reformat']) ? $txt['resize_images_now'] : $txt['resize_reformat_images_now']) . '" />',
				'style' => 'text-align: right;',
			),
			array(
				'position' => 'after_title',
				'value' => $txt['resize_images_desc'],
			),
		),

	);

	// Create the list.
	require_once($sourcedir . '/Subs-List.php');
	createList($listOptions);
}

// Called from the browse selection list.
function ResizeSelect()
{
	global $context, $settings, $txt, $smcFunc, $forum_version;

	// Check the session.
	checkSession('post');

	// Are we running SMF 2.0.x or SMF 2.1?
	$smf20 = (substr($forum_version, 0, 7) == 'SMF 2.0');

	if (!empty($_POST['resize']))
	{
		$attachments = array();
		loadLanguage('ResizeExistingImages');

		// All the images that have been selected for resizing.
		foreach ($_POST['resize'] as $resizeID => $dummy)
			$attachments[] = (int) $resizeID;

		// While we have images to work on.
		if (!empty($attachments))
		{
			// Make the query.
			$request = $smcFunc['db_query']('', '
				SELECT
					a.id_folder, a.filename, a.file_hash, a.attachment_type, a.id_attach, a.id_member, a.width, a.height,
					a.fileext, a.size, a.downloads, a.jpeg_quality
					, m.id_msg, m.id_topic, m.subject, m.id_msg, m.poster_time
				FROM {db_prefix}attachments AS a
					INNER JOIN {db_prefix}messages AS m ON (m.id_msg = a.id_msg)
				WHERE a.id_attach IN ({array_int:attachments})',
				array(
					'attachments' => $attachments,
				)
			);

			// Put the results in an array.
			$files = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$files[] = $row;
			$smcFunc['db_free_result']($request);

			// Call the main resizing function.
			foreach ($files as $row)
			{
				ResizeMain($row);

				// Try and get more time.
				@set_time_limit(60);
				if (function_exists('apache_reset_timeout'))
					@apache_reset_timeout();
			}
		}
	}

	// Done, back to the browse list we go.
	$_REQUEST['sort'] = isset($_REQUEST['sort']) ? (string) $_REQUEST['sort'] : 'date';
	if (isset($_REQUEST["desc"]))
		$_REQUEST['sort'] .= ';desc';

	if ($smf20)
		$sa = "resizebrowse";
	else
		$sa = "browse;resize";

	redirectexit('action=admin;area=manageattachments;sa=' . $sa . ';sort=' . $_REQUEST['sort'] . ';start=' . $_REQUEST['start'] . $context['session_var'] . '=' . $context['session_id']);
}

// Batch processing of images from the attachment file maintenance section.
// - runs as a paused loop to prevent server overload.
function ResizeBatch()
{
	global $db_prefix, $smcFunc, $txt, $context, $modSettings;

	// Going to need these.
	loadLanguage('ResizeExistingImages');
	loadTemplate('ResizeExistingImages');

	// Make sure the session is valid.
	checkSession('get');

	// Need to be an admin to do this.
	isAllowedTo('admin_forum');

	// On first entry we need to set some parameters.
	if (empty($_GET['step']))
	{
		// Make a backup copy of the attachments table.
		$table = $db_prefix . 'attachments';
		$backup_table = $db_prefix . 'attachmentsPreREI';

		// Create the backup table if it doesn't already exist.
		$result = $smcFunc['db_query']('', '
			CREATE TABLE IF NOT EXISTS {raw:backup_table} LIKE {raw:table}',
			array(
				'backup_table' => $backup_table,
				'table' => $table
			)
		);

		// And copy the data from the primary table to the backup table.
		$smcFunc['db_query']('', '
			INSERT IGNORE INTO {raw:backup_table}
			SELECT *
			FROM {raw:table}',
			array(
				'backup_table' => $backup_table,
				'table' => $table
			)
		);

		// Get some information before any processing is done.
		$_SESSION['num_files'] = resize_getNumFiles();
		$_SESSION['start_time'] = timeformat(time(), false);
		$_SESSION['attachmentSpaceBefore'] = getAttachmentDirSize();
		$_SESSION['resize_start'] = date_create();

		$context['nofiles'] = false;
		$context['completed'] = false;
		$context['resize_results'] = array();
		$_GET['step'] = 0;

		// Find out how many images we are going to process.
		$images = resize_getNumFiles();
		$_SESSION['resize_images'] = $images;
	}

	// Batch size -- how many images to process per loop.
	$chunk_size = $modSettings['defaultMaxMessages'];
	$_SESSION['chunk_size'] = $chunk_size;

	// Set up this for the pass through loop.
	$images = (isset($_SESSION['resize_images'])) ? $_SESSION['resize_images'] : 0;
	if (isset($_SESSION['resize_results']))
		$context['resize_results'] = $_SESSION['resize_results'];

	// Get the next group of images that meet our criteria.
	$files = resize_getFiles(0, $chunk_size);

	// While we have images that have not been resized.
	foreach ($files as $row)
	{
		$filename = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);
		if (file_exists($filename))
			ResizeMain($row);
	}

	// Update the counter and see if we have more to do.
	$_GET['step'] += $chunk_size;
	if ($_GET['step'] < $images)
		pauseAttachmentResize($images);

	// Got here so we must have done something - let's clean up.
	unset($_GET['step'], $_SESSION['resize_results'], $_SESSION['resize_images'], $_SESSION['resizesize']);

	$_SESSION['attachmentSpaceAfter'] = getAttachmentDirSize();
	$_SESSION['attachmentSpaceSaved'] = round(($_SESSION['attachmentSpaceBefore'] - $_SESSION['attachmentSpaceAfter']), 2);
	$_SESSION['finish_time'] = timeformat(time(), false);
	$_SESSION['resize_finish'] = date_create();
	$resize_diff = date_diff($_SESSION['resize_start'], $_SESSION['resize_finish']);
	$_SESSION['elapsed_time'] = $resize_diff->h . ' hours, ' . $resize_diff->i . ' minutes';

	// Do a final exit to the sub template to show what we did.
	$context['page_title'] = $txt['resize_images'];
	$context[$context['admin_menu_name']]['current_subsection'] = 'maintenance';
	$context['sub_template'] = 'attachment_resize';
	if ($_SESSION['num_files'] == 0)
		$context['nofiles'] = true;
	else
	{
		// Output the results to the error log just in case the results screen is missed.
		error_log(print_r("", true));
		error_log(print_r($txt['resize_images_complete'], true));
		error_log(print_r($txt['resize_images_complete_desc'], true));
		error_log(print_r($txt['resize_start_time'] . ' ' . $_SESSION['start_time'], true));
		error_log(print_r($txt['resize_finish_time'] . ' ' . $_SESSION['finish_time'], true));
		error_log(print_r($txt['resize_elapsed_time'] . ' ' . $_SESSION['elapsed_time'], true));
		error_log(print_r($txt['resize_total_resized'] . ' ' . $_SESSION['num_files'], true));
		error_log(print_r("", true));
		error_log(print_r($txt['resize_space_desc'], true));
		error_log(print_r($txt['resize_space_before'] . ' ' . $_SESSION['attachmentSpaceBefore'] . $txt['megabyte'], true));
		error_log(print_r($txt['resize_space_after'] . ' ' . $_SESSION['attachmentSpaceAfter'] . $txt['megabyte'], true));
		error_log(print_r($txt['resize_space_saved'] . ' ' . $_SESSION['attachmentSpaceSaved'] . $txt['megabyte'], true));
		error_log(print_r("", true));

		$context['completed'] = true;
	}
}

// Main resize controller.
// - Runs resizing on the supplied images.
// - Uses values set in via Admin -> Forum -> Attachments and Avatars -> Attachment Settings for size, dimensions, JPEG Quality and reformat options.
// - updates database with any changes.
function ResizeMain($file)
{
	global $boarddir, $context, $txt, $smcFunc, $sourcedir, $modSettings;

	$attsPreREI = $boarddir . '/attachmentsPreREI';
	if (!file_exists($attsPreREI))
		mkdir($attsPreREI, 0755, true);

	// Get the file path.
	$filename = getAttachmentFilename($file['filename'], $file['id_attach'], $file['id_folder']);

	// Get the image dimensions and type.
	list ($width, $height, $type) = @getimagesize($filename);

	// Determine the new width/height and make sure the aspect ratio is retained.
	require_once($sourcedir . '/Subs-AutoRotation.php');
	AutoRotation_Aspect($width, $height);

	// Set the preferred format to JPEG if reformatting or image is BMP.
	if (!empty($modSettings['attachment_image_reformat']) || $type == 6)
		$preferred_format = 2;
	else
		$preferred_format = $type;

	// Work out the JPEG quality.
	$jpegQuality = min(100, empty($modSettings['attachment_jpeg_quality']) ? 100 : $modSettings['attachment_jpeg_quality']);

	// Get the timestamp of the original image file.
	$timestamp = filemtime($filename);

	// Make a backup copy of the original image file.
	$reiFilename = $attsPreREI . '/' . pathinfo($filename, PATHINFO_FILENAME);
	copy($filename, $reiFilename);
	touch($reiFilename, $timestamp);

	// Got all the info we need - now resize it.
	require_once($sourcedir . '/Subs-Graphics.php');
	if (resizeImageFile($filename, $filename . '.temp', $width, $height, $preferred_format, $jpegQuality))
	{
		// Rename the temporary image file to the same name as the original image file.
		rename($filename . '.temp', $filename);

		// Make sure the resized file is not empty.
		if (filesize($filename) == 0)
		{
			// Oops - something went wrong! Log an error message, restore the original image file and reset the preferred format.
			error_log(print_r("resizeImageFile FAILED for" . $filename . " - the original image file has been restored.", true));
			@unlink($filename);
			@unlink($filename . '.temp');
			rename($reiFilename, $filename);
			$preferred_format = $type;
		}

		// Get the file size.
		$file['filesize'] = filesize($filename);

		// Get the image dimensions.
		list ($file['width'], $file['height']) = @getimagesize($filename);

		// Update the database with the new details.
		if ($preferred_format == 2)
		{
			// If the original image type is not JPEG change the file extension to 'jpg'.
			if ($type != 2)
				$file['filename'] = substr($file['filename'], 0, -(strlen(strrchr($file['filename'], '.')))) . '.jpg';

			$smcFunc['db_query']('', '
				UPDATE {db_prefix}attachments
				SET width = {int:width},
					height = {int:height},
					size = {int:size},
					filename = {string:filename},
					fileext = {string:ext},
					mime_type = {string:mime},
					jpeg_quality = {int:jpeg_quality}
				WHERE id_attach = {int:id_attach}
				LIMIT 1',
				array(
					'width' => $file['width'],
					'height' => $file['height'],
					'size' => $file['filesize'],
					'filename' => $file['filename'],
					'ext' => 'jpg',
					'mime' => 'image/jpeg',
					'jpeg_quality' => $jpegQuality,
					'id_attach' => $file['id_attach'],
				)
			);
		}
		else
		{
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}attachments
				SET width = {int:width},
					height = {int:height},
					size = {int:size}
				WHERE id_attach = {int:id_attach}
				LIMIT 1',
				array(
					'width' => $file['width'],
					'height' => $file['height'],
					'size' => $file['filesize'],
					'id_attach' => $file['id_attach'],
				)
			);
		}
	}
	else
	{
		// Oops - something went wrong! Log an error message and restore the original image file.
		error_log(print_r("resizeImageFile FAILED for" . $filename . " - the original image file has been restored.", true));
		@unlink($filename);
		@unlink($filename . '.temp');
		rename($reiFilename, $filename);
	}

	// Give the current image file the same timestamp as the original image file.
	touch($filename, $timestamp);
}

// Retrieves the information for the selected images.
// @param int $start.
// @param int $chunk_size.
// @param string $sort.
// @return array $files.
function resize_getFiles($start, $chunk_size, $sort = '')
{
	global $modSettings, $smcFunc;

	$files = array();

	// Init.
	if ($sort == '')
		$sort = 'a.id_attach DESC';

	// Get the configured options.
	$max_width = empty($modSettings['attachment_image_width']) ? 0 : $modSettings['attachment_image_width'];
	$max_height = empty($modSettings['attachment_image_height']) ? 0 : $modSettings['attachment_image_height'];
	$jpegQuality = min(100, empty($modSettings['attachment_jpeg_quality']) ? 100 : $modSettings['attachment_jpeg_quality']);

	// Nothing to do.
	if ($max_width == 0 && $max_height == 0 && $jpegQuality == 100)
		return $files;

	// Set max_size to 0 if the image cannot be reformatted and JPEG Quality is 100, or if the Attachment Size Limit is empty.
	$max_size = ((empty($modSettings['attachment_image_reformat']) && $jpegQuality == 100) || empty($modSettings['attachmentSizeLimit'])) ? 0 : $modSettings['attachmentSizeLimit'] * 1024;

	// Find out which images should be resized/reformatted.
	// - Non-JPEG images that are larger than the max size; or.
	// - Images that are wider/higher than the maximum specified; or.
	// - JPEG images that have a JPEQ quality greaer than the maximum soecified.
	$request = $smcFunc['db_query']('', '
		SELECT
			a.id_folder, a.filename, a.file_hash, a.attachment_type, a.id_attach,
			a.id_member, a.width, a.height, a.jpeg_quality, a.fileext, a.size, a.downloads, a.mime_type,
			m.id_msg, m.id_topic, m.subject, m.id_msg, m.poster_time
		FROM {db_prefix}attachments AS a
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = a.id_msg)
		WHERE (a.attachment_type = 0 AND width > 0 AND height > 0)
			AND (({int:max_size} > 0 AND a.size > {int:max_size} AND a.mime_type != \'image/jpeg\')
				OR ({int:max_width} > 0 AND a.width > {int:max_width}) OR ({int:max_height} > 0 AND a.height > {int:max_height})
				OR (a.fileext = \'jpg\' AND a.jpeg_quality > {int:jpegQuality}))
		ORDER BY {raw:sort}
		' . ((!empty($chunk_size)) ? 'LIMIT {int:offset}, {int:limit} ' : ''),
		array(
			'max_size' => $max_size,
			'max_width' => $max_width,
			'max_height' => $max_height,
			'jpegQuality' => $jpegQuality,
			'sort' => $sort,
			'offset' => $start,
			'limit' => $chunk_size,
		)
	);

	// Put the results in an array.
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$files[] = $row;
	$smcFunc['db_free_result']($request);

	return $files;
}

// Determines how many images meet our resizing/reformatting criteria.
function resize_getNumFiles()
{
	global $modSettings, $smcFunc;

	$max_width = empty($modSettings['attachment_image_width']) ? 0 : $modSettings['attachment_image_width'];
	$max_height = empty($modSettings['attachment_image_height']) ? 0 : $modSettings['attachment_image_height'];
	$jpegQuality = min(100, empty($modSettings['attachment_jpeg_quality']) ? 100 : $modSettings['attachment_jpeg_quality']);

	// Set max_size to 0 the image cannot be reformatted and JPEG Quality is 100, or if the Attachment Size Limit is empty.
	$max_size = ((empty($modSettings['attachment_image_reformat']) && $jpegQuality == 100) || empty($modSettings['attachmentSizeLimit'])) ? 0 : $modSettings['attachmentSizeLimit'] * 1024;

	// Find out how many images meet the following criteria:.
	// - Non-JPEG images that are larger than the max size; or.
	// - Images that are wider/higher than the maximum specified; or.
	// - JPEG images that have a JPEQ quality greaer than the maximum soecified.
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id_attach)
		FROM {db_prefix}attachments
		WHERE (attachment_type = 0 AND width > 0 AND height > 0)
			AND (({int:max_size} > 0 AND size > {int:max_size} AND mime_type != \'image/jpeg\')
				OR ({int:max_width} > 0 AND width > {int:max_width}) OR ({int:max_height} > 0 AND height > {int:max_height})
				OR (fileext = \'jpg\' AND jpeg_quality > {int:jpegQuality}))',
		array(
			'max_size' => $max_size,
			'max_width' => $max_width,
			'max_height' => $max_height,
			'jpegQuality' => $jpegQuality,
		)
	);

	// The number of images that need to be resized.
	list ($num_files) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	return $num_files;
}

// Get the attachment directory size.
function getAttachmentDirSize()
{
	global $context, $modSettings, $forum_version;

	// Are we running SMF 2.0.x or SMF 2.1?.
	$smf20 = (substr($forum_version, 0, 7) == 'SMF 2.0');

	if ($smf20)
	{
		if (!empty($modSettings['currentAttachmentUploadDir']))
			$attach_dirs = safe_unserialize($modSettings['attachmentUploadDir']);
		else
			$attach_dirs = array($modSettings['attachmentUploadDir']);
	}
	else
	{
		if (!is_array($modSettings['attachmentUploadDir']))
			$modSettings['attachmentUploadDir'] = smf_json_decode($modSettings['attachmentUploadDir'], true);

		// Just use the current path for temp files.
		$attach_dirs = $modSettings['attachmentUploadDir'];
	}

	// Find out how big the directory is. We have to loop through all our attachment paths in case there's an old temp file in one of them.
	$attachmentDirSize = 0;
	foreach ($attach_dirs as $id => $attach_dir)
	{
		$dir = @opendir($attach_dir) or fatal_lang_error('cant_access_upload_path', 'critical');
		while ($file = readdir($dir))
		{
			if ($file == '.' || $file == '..')
				continue;

			if (preg_match('~^post_tmp_\d+_\d+$~', $file) != 0)
			{
				// Temp file is more than 5 hours old!.
				if (filemtime($attach_dir . '/' . $file) < time() - 18000)
					@unlink($attach_dir . '/' . $file);
				continue;
			}

			// We're only counting the size of the current attachment directory.
			if (empty($modSettings['currentAttachmentUploadDir']) || $modSettings['currentAttachmentUploadDir'] == $id)
				$attachmentDirSize += filesize($attach_dir . '/' . $file);
		}
		closedir($dir);
	}

	// Calculate the result in megabytes.
	$attachmentDirSize /= 1024000;
	$attachmentDirSize = round($attachmentDirSize, 2);

	return $attachmentDirSize;
}

// Sets up for the next loop.
// @param int $max_steps.
function pauseAttachmentResize($max_steps = 0)
{
	global $context, $txt, $time_start;

	loadLanguage('ResizeExistingImages');

	// Try to get more time.
	@set_time_limit(600);
	if (function_exists('apache_reset_timeout'))
		@apache_reset_timeout();

	// Have we already used our maximum time - we don't want to just run forever.
	if (array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start)) > 30)
	{
		$context['resize_results'][9999999] = '|' . $txt['resize_images_timeout'] . ' ' . array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start));
		return;
	}

	$files_remaining = $_SESSION['num_files'] - $_GET['step'];

	// Set the context vars for display via the admin template 'not_done'.
	$context['continue_get_data'] = '?action=admin;area=manageattachments;sa=resizebatch;step=' . $_GET['step'] . ';' . $context['session_var'] . '=' . $context['session_id'];
	$context['page_title'] = $txt['not_done_title'];
	$context['continue_post_data'] = $txt['resize_start_time'] . $_SESSION['start_time'] . '<br/>' .
									$txt['resize_number_images'] . $_SESSION['num_files'] . '<br/>' .
									$txt['resize_batch_size'] . $_SESSION['chunk_size'] . '<br/>' .
									$txt['resize_images_to_resize'] . $files_remaining;
	$context['continue_countdown'] = 3;
	$context['sub_template'] = 'not_done';
	$context[$context['admin_menu_name']]['current_subsection'] = 'maintenance';
	$context['continue_percent'] = round(((int) $_GET['step'] / $max_steps) * 100);
	$context['continue_percent'] = min($context['continue_percent'], 100);

	// Save for the next loop.
	$_SESSION['resize_results'] = $context['resize_results'];

	obExit();
}

?>
