<?php
function template_attachment_resize()
{
	global $context, $txt, $settings;

	if ($context['completed'])
	{
		echo '
			<div class="cat_bar" id="manage_attachments">
				<h3 class="catbg">', $txt['resize_images_complete'], '</h3>
			</div>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<p>', $txt['resize_images_complete_desc'], '</p>
					<br/>
					<p>', $txt['resize_start_time'], '&nbsp;&nbsp;',$_SESSION['start_time'], '</p>
					<p>', $txt['resize_finish_time'], '&nbsp;&nbsp;',$_SESSION['finish_time'], '</p>
					<p>', $txt['resize_elapsed_time'], '&nbsp;&nbsp;',$_SESSION['elapsed_time'], '</p>
					<p>', $txt['resize_total_resized'], '&nbsp;&nbsp;', $_SESSION['num_files'], '</p>
					<br/>
					<p>', $txt['resize_space_desc'], '</p>
					<p>', $txt['resize_space_before'], '&nbsp;&nbsp;', $_SESSION['attachmentSpaceBefore'], $txt['megabyte'], '</p>
					<p>', $txt['resize_space_after'], '&nbsp;&nbsp;', $_SESSION['attachmentSpaceAfter'], $txt['megabyte'], '</p>
					<p>', $txt['resize_space_saved'], '&nbsp;&nbsp;', $_SESSION['attachmentSpaceSaved'], $txt['megabyte'], '</p>
				</div>
				<span class="botslice"><span></span></span>
			</div>';
	}
	elseif ($context['nofiles'])
	{
		echo '
			<div class="cat_bar" id="manage_attachments">
				<h3 class="catbg">', $txt['resize_images_complete'], '</h3>
			</div>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<p>', $txt['resize_images_empty_desc'], '</p>
				</div>
				<span class="botslice"><span></span></span>
			</div>';
	}
}

?>
