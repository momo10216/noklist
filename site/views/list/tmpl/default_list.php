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

// Get columns to be displayed
if (is_object($this->paramsMenuEntry)) {
	$config = $this->paramsMenuEntry->get('list_columns');
	$config = str_replace("\r","\n",$config);
	$config = str_replace("\n\n","\n",$config);
	if (empty($config)) {
		$listColumn = $this->colHeaders;
	} else {
		$listColumn = explode("\n",$config);
	}
}
$detailLinkcolumn = $this->paramsMenuEntry->get('list_detail_column');

// Display export
echo '<form action="'.$this->getLink('export').'" method="POST">';
echo '<select name="export_encoding" style="width: auto; margin: 0px; ">';
foreach($encodings as $display => $value) {
	echo '<option value="'.$value.'">'.$display.' ('.$value.')</option>';
}
echo '</select>';
echo '<input type="submit" value="'.JText::_('COM_NOKLIST_EXPORT_BUTTON').'"/>';
echo '</form>'.$EOL;

// Display import
if ($this->canChange()) {
	echo '<form action="'.$this->getLink('import').'" method="POST" enctype="multipart/form-data">';
	echo '<select name="import_encoding" style="width: auto; margin: 0px; ">';
	foreach($encodings as $display => $value) {
		echo '<option value="'.$value.'">'.$display.' ('.$value.')</option>';
	}
	echo '</select>';
	echo '<input class="input_box" id="import_file" name="import_file" type="file" size="57" />';
	echo '<input type="submit" value="'.JText::_('COM_NOKLIST_IMPORT_BUTTON').'" onClick="if(document.getElementById(\'import_file\').value == \'\') { alert(\''.JText::_('COM_NOKLIST_IMPORT_FILE_EMPTY').'\'); return false; }"/>';
	echo '</form>'.$EOL;
}

// Display header
$border='border-style:solid; border-width:1px';
$width='';
if ($this->paramsMenuEntry->get('width') != '0') {
	$width='width="'.$this->paramsMenuEntry->get('width').'" ';
}
switch ($this->paramsMenuEntry->get( "border_type")) {
	case "row":
		$borderStyle = " style=\"border-top-style:solid; border-width:1px\"";
		break;
	case "grid":
		$borderStyle = " style=\"".$border."\"";
		break;
	default:
		$borderStyle = "";
		break;
}
if ($this->paramsMenuEntry->get('table_center') == '1') { echo '<center>'.$EOL; }
if ($this->paramsMenuEntry->get('border_type') != '') {
	echo '<table '.$width.'border="0" cellspacing="0" cellpadding="'.$this->paramsMenuEntry->get('cellpadding').'" style="'.$border.'">'.$EOL;
} else {
	echo '<table '.$width.'border="0" cellspacing="0" cellpadding="'.$this->paramsMenuEntry->get('cellpadding').'" style="border-style:none; border-width:0px">'.$EOL;
}
echo '<tr>';
foreach ($listColumn as $col) {
	echo '<th align="left"'.$borderStyle.'>'.$col.'</th>';
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
	foreach($rows as $rkey => $row) {
		echo '<tr>';
		foreach ($listColumn as $col) {
			$pos =  array_search($col, $this->colHeaders);
			echo '<td'.$borderStyle.'>';
			if ($pos !== false) {
				if (isset($row[$pos])) {
					$field = $this->getDisplayValue($col, $row[$pos]);
					if ($detailLinkcolumn == $col) {
						$field = '<a href="'.$this->getLink('detail',"$rkey").'">'.$field.'</a>';
					}
					echo $field;
				}
			}
			echo '</td>';
		}
		echo '<td>';
		if ($this->canChange()) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('edit',"$rkey").'"><span class="icon-edit"></span></a>';
		}
		echo '</td>';
		echo '<td>';
		if ($this->canChange()) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('delete',"$rkey").'" onClick="return confirm(\''.$deleteConfirmMsg.'\');"><span class="icon-trash"></span></a>';
		}
		echo '</td>';
		echo '<td>';
		if ($this->canChange() && ($rkey > 0)) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('moveup',"$rkey").'"><span class="icon-arrow-up"></span></a>';
		}
		echo '</td>';
		echo '<td>';
		if ($this->canChange()  && ($rkey < ($rowcount-1))) {
			echo '<a style="text-decoration: none;" href="'.$this->getLink('movedown',"$rkey").'"><span class="icon-arrow-down"></span></a>';
		}
		echo '</td>';
		echo '</tr>'.$EOL;
	}
}

// Display footer
echo '</table>'.$EOL;
if ($this->paramsMenuEntry->get( "table_center") == "1") { echo '</center>'.$EOL; }
?>