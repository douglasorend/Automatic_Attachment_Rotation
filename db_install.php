<?php
$SSI_INSTALL = false;
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$SSI_INSTALL = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');
require($sourcedir.'/Subs-Admin.php');
db_extend('packages');

//==============================================================================
// Insert one column into the necessary tables:
//==============================================================================
// {prefix}boards table gets a new column to hold the number of anonymous posts:
$smcFunc['db_add_column'](
	'{db_prefix}attachments', 
	array(
		'name' => 'proper_rotation', 
		'size' => 3, 
		'type' => 'tinyint', 
		'null' => false, 
		'default' => 0
	)
);

//==============================================================================
// Add resize options to the settings table
//==============================================================================
$smcFunc['db_insert']('ignore',
	'{db_prefix}settings',
	array('variable' => 'string', 'value' => 'int'),
	array(
		array('attachment_image_reformat', '0'),
		array('attachment_resize_existing', '0'),
		array('attachment_jpeg_quality', '100'),
		array('attachment_image_width', '0'),
		array('attachment_image_height', '0'),
	),
	array('variable')
);

$smcFunc['db_insert']('ignore',
	'{db_prefix}settings',
	array('variable' => 'string', 'value' => 'int'),
	array(
		array('pm_attachment_image_reformat', '0'),
		array('pm_attachment_jpeg_quality', '100'),
		array('pm_attachment_image_width', '0'),
		array('pm_attachment_image_height', '0'),
	),
	array('variable')
);

//==============================================================================
// Set the default value for the PAM mode if not already set:
//==============================================================================
if (!isset($modSettings['PAM_mode']))
	updateSettings(	array( 'PAM_mode' => 3 ) );

// Echo that we are done if necessary:
if ($SSI_INSTALL)
	echo 'DB Changes should be made now...';
?>
