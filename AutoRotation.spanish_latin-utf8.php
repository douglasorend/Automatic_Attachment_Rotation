<?php
/**********************************************************************************
* AutoRotation.spanish_latin-utf8.php                                             *
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
$txt['img_rotate_confirm'] = '¿Seguro que desea aplicar los cambios en la imagen Girar / Voltear?';
$txt['img_orientation'] = 'Girar / Voltear';
$txt['img_orientation1'] = 'Ning&uacute;n cambio';
$txt['img_orientation2'] = 'Flip horizontal';
$txt['img_orientation3'] = 'Girar 180&deg;';
$txt['img_orientation4'] = 'Vertical Flip';
$txt['img_orientation5'] = 'Vertical Flip, Girar 90&deg; Derecha';
$txt['img_orientation6'] = 'Girar 90&deg; Derecha';
$txt['img_orientation7'] = 'Flip horizontal, Girar 90&deg; Derecha';
$txt['img_orientation8'] = 'Girar 90&deg; Izquierda';
$txt['img_rotate'] = 'Girar / Voltear imágenes';
$txt['attachment_clear_rotation_title'] = 'Restablecer bandera de orientación';
$txt['attachment_clear_rotation_desc'] = 'Esta función borrará los indicadores de orientación en la base de datos para todos los archivos adjuntos de imagen. Tenga en cuenta que esta función no puede reparar imágenes que no contienen información EXIF, como imágenes que SMF ha recodificado. Esta operación <strong>NO</strong> cambia las imágenes en este momento, pero indica a las imágenes que se reprocesen en el momento de la visualización posterior.';
$txt['attachment_clear_rotation_button'] = 'Banderas de orientación claras';
$txt['AutoRotation_log_error'] = '¿Error de memoria de registro al girar la imagen?';
$txt['AutoRotation_memory_issue'] = 'No se puede asignar %1$d de memoria para la rotación de la imagen.';

// Added for Automatic Attachment Rotation (and Resize).
$txt['attachment_auto_rotate'] = 'Rotar las imágenes automáticamente<div class="smalltext">(Solo es posible para imágenes JPEG que contienen datos de orientación EXIF)</div>';
$txt['attachment_image_reformat'] = 'Cambiar el formato de imágenes que no son JPEG a JPEG';
$txt['attachment_resize_existing'] = 'Cambiar el tamaño de las imágenes existentes';
$txt['attachment_resize_backup'] = 'Mantener copia de seguridad de la imagen original al cambiar el tamaño';
$txt['attachment_jpeg_quality'] = 'Factor de calidad JPEG<div class="smalltext">(Máximo 100, valor predeterminado 100)</div>';
$txt['attachment_image_width'] = 'Ancho máximo de imágenes adjuntas<div class="smalltext">(0 para ningun ancho máximo)</div>';
$txt['attachment_image_height'] = 'Altura máxima de las imágenes adjuntas<div class="smalltext">(0 para ninguna altura máxima)</div>';
$txt['attachment_image_reencode'] = 'Vuelva a codificar los archivos adjuntos de imagen potencialmente peligrosos.<div class="smalltext">Nota: La rotación automática de imágenes NO funcionará si esta opción está marcada.</div>';

$txt['pm_attachment_image_reformat'] = 'Cambiar el formato de imágenes que no son JPEG a JPEG.';
$txt['pm_attachment_jpeg_quality'] = 'Factor de calidad JPEG<div class="smalltext">(Máximo 100, valor predeterminado 100)</div>';
$txt['pm_attachment_image_width'] = 'Ancho máximo de imágenes adjuntas<div class="smalltext">(0 para ningun ancho máximo)</div>';
$txt['pm_attachment_image_height'] = 'Altura máxima de las imágenes adjuntas<div class="smalltext">(0 para ninguna altura máxima)</div>';

$helptxt['attachment_image_reformat'] = 'Al seleccionar esta opción, se reformatearán las imágenes que no sean JPEG como JPEG';
$helptxt['attachment_resize_existing'] = 'Al seleccionar esta opción, se redimensionará cualquier imagen existente que sea más grande que las dimensiones establecidas para las imágenes adjuntas.';
$helptxt['attachment_resize_backup'] = 'Si esta opción está habilitada (y la opción <i>&quot;Cambiar el tamaño de las imágenes existentes&quot;</i> también está habilitado) el archivo de imagen original se guarda en el directorio <i>archivos adjuntos PreREI</i> en el directorio predeterminado del foro.';
$helptxt['attachment_jpeg_quality'] = 'Esto establece el factor de calidad JPEG. Un número más alto aumenta la calidad de la imagen pero también aumenta el tamaño del archivo adjunto.';
$helptxt['pm_attachment_image_reformat'] = 'Al seleccionar esta opción, se reformatearán las imágenes que no sean JPEG como JPEG';
$helptxt['pm_attachment_jpeg_quality'] = 'Esto establece el factor de calidad JPEG. Un número más alto aumenta la calidad de la imagen pero también aumenta el tamaño del archivo adjunto.';
?>
