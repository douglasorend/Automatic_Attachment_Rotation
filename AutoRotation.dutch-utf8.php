<?php
/**********************************************************************************
* AutoRotation.dutch-utf8.php                                                          *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:   *
*	http://opensource.org/licenses/BSD-2-Clause                                   *
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but	  *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY	  *
* or FITNESS FOR A PARTICULAR PURPOSE.											  *
**********************************************************************************/
global $helptxt;

$txt['img_rotate_confirm'] = 'Weet je zeker dat de de wijzigingen voor draaien en spiegelen wilt uitvoeren?';
$txt['img_orientation'] = 'Draai/Spiegel';
$txt['img_orientation1'] = 'Geen wijziging';
$txt['img_orientation2'] = 'Spiegel horizontaal';
$txt['img_orientation3'] = 'Draai 180&deg;';
$txt['img_orientation4'] = 'Spiegel verticaal';
$txt['img_orientation5'] = 'Spiegel verticaal, draai 90&deg; rechtsom';
$txt['img_orientation6'] = 'Draai 90&deg; rechtsom';
$txt['img_orientation7'] = 'Spiegel horizontaal, draai 90&deg; rechtsom';
$txt['img_orientation8'] = 'Draai 90&deg; linksom';
$txt['img_rotate'] = 'Draai / Spiegel';
$txt['attachment_clear_rotation_title'] = 'Herstel orientatie';
$txt['attachment_clear_rotation_desc'] = 'Deze functie wist alle orientatie instellingen uit de database voor alle afbeeldingen bijlages.  Let op, dat deze functie geen invloed heeft op afbeeldingen welke geen EXIF informatie bevatten, zoals afbeeldingen die SMF opnieuw gecodeerd heeft. Deze actie wijzigt de afbeeldingen <strong>NIET</strong> meteen, maar zorgt dat de afbeeldingen opnieuw worden verwerkt bij de volgende weergave.';
$txt['attachment_clear_rotation_button'] = 'Verwijder orientatie instellingen';
$txt['AutoRotation_log_error'] = 'Schrijf geheugenfouten bij het draaien van de afbeelding?';
$txt['AutoRotation_memory_issue'] = 'Het is niet mogelijk om %1$d geheugen toe te wijzen voor het draaien van de afbeelding.';

// Added for Automatic Attachment Rotation (and Resize).
$txt['attachment_auto_rotate'] = 'Afbeeldingen automatisch draaien<div class="smalltext">(Alleen mogelijk als de JPEG bestanden EXIF orientatie data bevatten)</div>';
$txt['attachment_image_reformat'] = 'Niet-JPEG afbeeldingen omzetten naar JPEG';
$txt['attachment_resize_existing'] = 'Bestaande bijlagen aanpassen';
$txt['attachment_resize_backup'] = 'Keep backup of original image file when resizing';
$txt['attachment_jpeg_quality'] = 'JPEG kwaliteits factor<div class="smalltext">(Maximaal 100, standaard 100)</div>';
$txt['attachment_image_width'] = 'Maximale breedte van afbeeldingen in bijlage<div class="smalltext">(0 voor geen maximum)</div>';
$txt['attachment_image_height'] = 'Maximale hoogte van afbeeldingen in bijlage<div class="smalltext">(0 voor geen maximum)</div>';

$txt['pm_attachment_image_reformat'] = 'Niet-JPEG afbeeldingen omzetten naar JPEG';
$txt['pm_attachment_jpeg_quality'] = 'JPEG kwaliteits factor<div class="smalltext">(Maximaal 100, standaard 100)</div>';
$txt['pm_attachment_image_width'] = 'Maximale breedte van afbeeldingen in bijlage<div class="smalltext">(0 voor geen maximum)</div>';
$txt['pm_attachment_image_height'] = 'Maximale hoogte van afbeeldingen in bijlage<div class="smalltext">(0 voor geen maximum)</div>';

$helptxt['attachment_image_reformat'] = 'Als je deze optie selecteert zullen niet-JPEG afbeeldingen worden omgezet naar het JPEG formaat';
$helptxt['attachment_resize_existing'] = 'Als je deze optie selecteert zullen bestaande afbeeldingen die groter zijn dan de ingestelde afmetingen aangepast worden.';
$helptxt['attachment_resize_backup'] = 'If this option is enabled (and the option <i>\'Resize existing images\'</i> is also enabled) the original image file will be kept in the <i>attachmentsPreREI<i> directory.';
$helptxt['attachment_jpeg_quality'] = 'Hiermee bepaal je de JPEG kwaliteits factor. Hoe hoger het getal hoe beter de kwaliteit maar dit geeft ook een groter bestand.';
$helptxt['pm_attachment_image_reformat'] = 'Als je deze optie selecteert zullen niet-JPEG afbeeldingen worden omgezet naar het JPEG formaat';
$helptxt['pm_attachment_jpeg_quality'] = 'Hiermee bepaal je de JPEG kwaliteits factor. Hoe hoger het getal hoe beter de kwaliteit maar dit geeft ook een groter bestand.';

?>
