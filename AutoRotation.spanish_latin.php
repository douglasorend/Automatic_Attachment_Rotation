<?php
/**********************************************************************************
* AutoRotation.spanish_latin.php                                                  *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:   *
*	http://opensource.org/licenses/BSD-2-Clause                                   *
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but	  *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY	  *
* or FITNESS FOR A PARTICULAR PURPOSE.											  *
***********************************************************************************
* Spanish translation by Rock Lee (https://www.bombercode.org) Copyright 2014-2018*
***********************************************************************************/
$txt['img_rotate_confirm'] = '&iquest;Seguro que desea aplicar los cambios en la imagen Girar / Voltear?';
$txt['img_orientation'] = 'Girar / Voltear';
$txt['img_orientation1'] = 'Ning&uacute;n cambio';
$txt['img_orientation2'] = 'Flip horizontal';
$txt['img_orientation3'] = 'Girar 180&deg;';
$txt['img_orientation4'] = 'Vertical Flip';
$txt['img_orientation5'] = 'Vertical Flip, Girar 90&deg; Derecha';
$txt['img_orientation6'] = 'Girar 90&deg; Derecha';
$txt['img_orientation7'] = 'Flip horizontal, Girar 90&deg; Derecha';
$txt['img_orientation8'] = 'Girar 90&deg; Izquierda';
$txt['img_rotate'] = 'Girar / Voltear im&aacute;genes';
$txt['attachment_clear_rotation_title'] = 'Restablecer bandera de orientaci&oacute;n';
$txt['attachment_clear_rotation_desc'] = 'Esta funci&oacute;n borrar&aacute; los indicadores de orientaci&oacute;n en la base de datos para todos los archivos adjuntos de imagen. Tenga en cuenta que esta funci&oacute;n no puede reparar im&aacute;genes que no contienen informaci&oacute;n EXIF, como im&aacute;genes que SMF ha recodificado. Esta operaci&oacute;n <strong>NO</strong> cambia las im&aacute;genes en este momento, pero indica a las im&aacute;genes que se reprocesen en el momento de la visualizaci&oacute;n posterior.';
$txt['attachment_clear_rotation_button'] = 'Banderas de orientaci&oacute;n claras';
$txt['AutoRotation_log_error'] = '&iquest;Error de memoria de registro al girar la imagen?';
$txt['AutoRotation_memory_issue'] = 'No se puede asignar %1$d de memoria para la rotaci&oacute;n de la imagen.';

// Added for Automatic Attachment Rotation (and Resize).
$txt['attachment_auto_rotate'] = 'Rotar las im&aacute;genes autom&aacute;ticamente<div class="smalltext">(Solo es posible para im&aacute;genes JPEG que contienen datos de orientaci&oacute;n EXIF)</div>';
$txt['attachment_image_reformat'] = 'Cambiar el formato de im&aacute;genes que no son JPEG a JPEG';
$txt['attachment_resize_existing'] = 'Cambiar el tama&ntilde;o de las im&aacute;genes existentes';
$txt['attachment_resize_backup'] = 'Mantener copia de seguridad de la imagen original al cambiar el tama&ntilde;o';
$txt['attachment_jpeg_quality'] = 'Factor de calidad JPEG<div class="smalltext">(M&aacute;ximo 100, valor predeterminado 100)</div>';
$txt['attachment_image_width'] = 'Ancho m&aacute;ximo de im&aacute;genes adjuntas<div class="smalltext">(0 para ningun ancho m&aacute;ximo)</div>';
$txt['attachment_image_height'] = 'Altura m&aacute;xima de las im&aacute;genes adjuntas<div class="smalltext">(0 para ninguna altura m&aacute;xima)</div>';
$txt['attachment_image_reencode'] = 'Vuelva a codificar los archivos adjuntos de imagen potencialmente peligrosos.<div class="smalltext">Nota: La rotaci�n autom�tica de im�genes NO funcionar� si esta opci�n est� marcada.</div>';

$txt['pm_attachment_image_reformat'] = 'Cambiar el formato de im&aacute;genes que no son JPEG a JPEG.';
$txt['pm_attachment_jpeg_quality'] = 'Factor de calidad JPEG<div class="smalltext">(M&aacute;ximo 100, valor predeterminado 100)</div>';
$txt['pm_attachment_image_width'] = 'Ancho m&aacute;ximo de im&aacute;genes adjuntas<div class="smalltext">(0 para ningun ancho m&aacute;ximo)</div>';
$txt['pm_attachment_image_height'] = 'Altura m&aacute;xima de las im&aacute;genes adjuntas<div class="smalltext">(0 para ninguna altura m&aacute;xima)</div>';

$helptxt['attachment_image_reformat'] = 'Al seleccionar esta opci&oacute;n, se reformatear&aacute;n las im&aacute;genes que no sean JPEG como JPEG';
$helptxt['attachment_resize_existing'] = 'Al seleccionar esta opci&oacute;n, se redimensionar&aacute; cualquier imagen existente que sea m&aacute;s grande que las dimensiones establecidas para las im&aacute;genes adjuntas.';
$helptxt['attachment_resize_backup'] = 'Si esta opci&oacute;n est&aacute; habilitada (y la opci&oacute;n <i>&quot;Cambiar el tama&ntilde;o de las im&aacute;genes existentes&quot;</i> tambi&eacute;n est&aacute; habilitado) el archivo de imagen original se guarda en el directorio <i>archivos adjuntos PreREI</i> en el directorio predeterminado del foro.';
$helptxt['attachment_jpeg_quality'] = 'Esto establece el factor de calidad JPEG. Un n&uacute;mero m&aacute;s alto aumenta la calidad de la imagen pero tambi&eacute;n aumenta el tama&ntilde;o del archivo adjunto.';
$helptxt['pm_attachment_image_reformat'] = 'Al seleccionar esta opci&oacute;n, se reformatear&aacute;n las im&aacute;genes que no sean JPEG como JPEG';
$helptxt['pm_attachment_jpeg_quality'] = 'Esto establece el factor de calidad JPEG. Un n&uacute;mero m&aacute;s alto aumenta la calidad de la imagen pero tambi&eacute;n aumenta el tama&ntilde;o del archivo adjunto.';
?>
