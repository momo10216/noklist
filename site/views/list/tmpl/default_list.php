<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	NoK-List
* @copyright	Copyright (c) 2017 Norbert Kümin. All rights reserved.
* @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
* @author	Norbert Kuemin
* @authorEmail	momo_102@bluemail.ch
*/
defined('_JEXEC') or die; // no direct access
$EOL = "\n";
$encodings = array(
	'Windows' => 'ISO-8859-1',
	'Mac' => 'MAC',
	'Linux' => 'UTF-8'
);

// Display export
echo '<form action="'.$this->getLink('export').'" method="POST">';
echo '<select name="export_encoding" style="width: auto; margin: 0px; ">';
foreach($encodings as $display => $value) {
	echo '<option value="'.$value.'">'.$display.'</option>';
}
echo '</select>';
echo '<input type="submit" value="'.JText::_('COM_NOKLIST_EXPORT_BUTTON').'"/>';
echo '</form>'.$EOL;

// Display header
echo '<table>'.$EOL;
echo '<tr>';
foreach ($this->colHeaders as $col) {
	echo '<th align="left">'.$col.'</th>';
}
echo '<th align="left">';
if ($this->canChange()) {
	echo '<a style="text-decoration: none;" href="'.$this->getLink('new').'"><span class="icon-new"></span></a>';
}
echo '</th>';
echo '</tr>'.$EOL;

// Display list
$rows = $this->getData();
$rowcount = count($rows);
if ($rowcount > 0) {
	$deleteConfirmMsg = JText::_("COM_NOKLIST_ENTRY_CONFIRM_DELETE");
	foreach($rows as $key => $row) {
		echo '<tr>';
		foreach($row as $field) {
			echo '<td>'.$field.'</td>';
		}
		if (count($row) < count($this->colHeaders)) {
			for($i=count($row) ; $i < count($this->colHeaders) ; $i++) { echo '<td></td>'; }
		}
		echo '<td>';
		if ($this->canChange()) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('edit',"$key").'"><span class="icon-edit"></span></a>';
		}
		echo '</td>';
		echo '<td>';
		if ($this->canChange()) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('delete',"$key").'" onClick="return confirm(\''.$deleteConfirmMsg.'\');"><span class="icon-trash"></span></a>';
		}
		echo '</td>';
		echo '<td>';
		if ($this->canChange() && ($key > 0)) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('moveup',"$key").'"><span class="icon-arrow-up"></span></a>';
		}
		echo '</td>';
		echo '<td>';
		if ($this->canChange()  && ($key < ($rowcount-1))) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('movedown',"$key").'"><span class="icon-arrow-down"></span></a>';
		}
		echo '</td>';
		echo '</tr>'.$EOL;
	}
}
echo "</table>\n";
?>