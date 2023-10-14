<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	NoK-List
* @copyright	Copyright (c) 2017 Norbert K�min. All rights reserved.
* @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
* @author	Norbert Kuemin
* @authorEmail	momo_102@bluemail.ch
*/
defined('_JEXEC') or die; // no direct access

use Joomla\CMS\Language\Text;

$EOL = "\n";
$encodings = array(
	'Windows' => 'ISO-8859-1',
	'Mac' => 'MAC',
	'Linux' => 'UTF-8'
);

// Get columns to be displayed
if (is_object($this->paramsMenu)) {
	$config = $this->paramsMenu->get('list_columns');
	$config = str_replace("\r","\n",$config);
	$config = str_replace("\n\n","\n",$config);
	if (empty($config)) {
		$listColumn = $this->getModel()->colHeaders;
	} else {
		$listColumn = explode("\n",$config);
	}
}
if (is_object($this->paramsMenu)) {
    $detailLinkcolumn = $this->paramsMenu->get('list_detail_column');
}

// Get sort info
$sortField = '';
$sortDirection = 'ASC';
if (is_object($this->paramsMenu)) {
	$sortField = $this->paramsMenu->get('sort_column');
	$sortDirection = $this->paramsMenu->get('sort_direction');
}
$manualSortEnabled = $this->paramsMenu->get('sort_enable') == '1';
if ($manualSortEnabled) {
	$jinput = JFactory::getApplication()->input;
	$inputSortField = $jinput->get('sortfield','');
	$inputSortDirection = $jinput->get('sortdirection','');
	if (!empty($inputSortField)) { $sortField = $inputSortField; }
	if (!empty($inputSortDirection)) { $sortDirection = $inputSortDirection; }
}

// Pre text
echo $this->paramsMenu->get('pretext');

// Display export
if ($this->paramsMenu->get('allow_csv_export') == '1') {
	echo '<form action="'.$this->getLink('csv_export').'" method="POST">';
	echo '<select name="export_encoding" style="width: auto; margin: 0px; ">';
	foreach($encodings as $display => $value) {
		echo '<option value="'.$value.'">'.$display.' ('.$value.')</option>';
	}
	echo '</select>';
	echo '<input type="submit" value="'.JText::_('COM_NOKLIST_EXPORT_BUTTON').'"/>';
	echo '</form>'.$EOL;
}
if (($this->paramsMenu->get('allow_json_export') == '1') && ($this->paramsMenu->get('display_json_link') == '1')) {
	echo '<a href="'.$this->getLink('json_export').'">JSON</a>'.$EOL;
}
// Display import
if ($this->paramsMenu->get('allow_import') == '1') {
	if ($this->canChange()) {
		echo '<form action="'.$this->getLink('import').'" method="POST" enctype="multipart/form-data">';
		echo '<select name="import_encoding" style="width: auto; margin: 0px; ">';
		foreach($encodings as $display => $value) {
			echo '<option value="'.$value.'">'.$display.' ('.$value.')</option>';
		}
		echo '</select>';
		echo '<input class="input_box" id="import_file" name="import_file" type="file" size="57" />';
		echo '<input type="submit" value="'.Text::_('COM_NOKLIST_IMPORT_BUTTON').'" onClick="if(document.getElementById(\'import_file\').value == \'\') { alert(\''.Text::_('COM_NOKLIST_IMPORT_FILE_EMPTY').'\'); return false; }"/>';
		echo '</form>'.$EOL;
	}
}

// Display header
$border='border-style:solid; border-width:1px';
$width='';
if ($this->paramsMenu->get('width') != '0') {
	$width=' width="'.$this->paramsMenu->get('width').'"';
}
switch ($this->paramsMenu->get( "border_type")) {
	case "row":
		$borderStyle = " style=\"border-top-style:solid; border-width:1px\"";
		break;
	case "column":
		$borderStyle = " style=\"border-left-style:solid; border-width:1px\"";
		break;
	case "grid":
		$borderStyle = " style=\"".$border."\"";
		break;
	default:
		$borderStyle = "";
		break;
}
if ($this->paramsMenu->get('table_center') == '1') { echo '<center>'.$EOL; }
if ($this->paramsMenu->get('border_type') != '') {
	echo '<table'.$width.' cellspacing="0" cellpadding="'.$this->paramsMenu->get('cellpadding').'" style="'.$border.'">'.$EOL;
} else {
	echo '<table'.$width.' border="0" cellspacing="0" cellpadding="'.$this->paramsMenu->get('cellpadding').'" style="border-style:none; border-width:0px">'.$EOL;
}
echo '<tr>';
foreach ($listColumn as $key => $col) {
	echo '<th align="left"';
	if ($this->paramsMenu->get('border_type') != 'row') {
		if (($this->paramsMenu->get('border_type') != 'column') || ($key != '0')) {
			echo $borderStyle;
		}
	}
	echo '>';
	if ($manualSortEnabled) {
		$newSortDirection = 'ASC';
		$sortExtText = '';
		if ($col == $sortField) {
			$sortExtText = '&#x25BC;';
			if ($sortDirection == 'ASC') {
				$newSortDirection = 'DESC';
				$sortExtText = ' &#x25B2;';
			}
		}
		echo '<a style="text-decoration: none;" href="'.$this->getSortLink($col,$newSortDirection).'">';
	}
	echo $col;
	if ($manualSortEnabled) {
		echo '</a>'.$sortExtText;
	}
	echo '</th>';
}
if ($this->canChange()) {
	if (empty($sortField) && ($manualSortEnabled === false)) {
		echo '<th colspan="4" align="left"';
	} else {
		echo '<th colspan="2" align="left"';
	}
	if ($this->paramsMenu->get('border_type') != 'row') {
		echo $borderStyle;
	}
	echo '>';
	echo '<a style="text-decoration: none;" href="'.$this->getLink('new').'"><span class="icon-new"></span></a>';
	echo '</th>';
}
echo '</tr>'.$EOL;

// Display list
$rows = $this->getModel()->getItems();
$idxlist = $this->getModel()->getIndex($rows,$sortField,$sortDirection);
$rowcount = count($rows);
if ($rowcount > 0) {
	$deleteConfirmMsg = Text::_('COM_NOKLIST_ENTRY_CONFIRM_DELETE');
	foreach($idxlist as $rkey => $rvalue) {
		if (isset($rows[$rkey])) {
			$row = $rows[$rkey];
			echo '<tr>';
			foreach ($listColumn as $fkey => $col) {
				$pos =  array_search($col, $this->getModel()->colHeaders);
				$align = '';
				if (strtolower($this->getModel()->colTypes[$col]) == 'number') { $align = ' align="right"'; }
				echo '<td'.$align;
				if (($this->paramsMenu->get('border_type') != 'column') || ($fkey != '0')) {
					echo $borderStyle;
				}
				echo '>';
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
			if ($this->canChange()) {
				$cellstart = '<td>';
				if ($this->paramsMenu->get('border_type') != 'column') {
					$cellstart = '<td'.$borderStyle.'>';
				}
				echo $cellstart;
				echo '<a style="text-decoration: none;" href="'.$this->getLink('edit',"$rkey").'"><span class="icon-edit"></span></a>';
				echo '</td>';
				echo $cellstart;
				echo '<a style="text-decoration: none;" href="'.$this->getLink('delete',"$rkey").'" onClick="return confirm(\''.$deleteConfirmMsg.'\');"><span class="icon-trash"></span></a>';
				echo '</td>';
				if (empty($sortField) && ($manualSortEnabled === false)) {
					echo $cellstart;
					if ($rkey > 0) {
						echo '<a style="text-decoration: none;" href="'.$this->getLink('moveup',"$rkey").'"><span class="icon-arrow-up"></span></a>';
					}
					echo '</td>';
					echo $cellstart;
					if ($rkey < ($rowcount-1)) {
						echo '<a style="text-decoration: none;" href="'.$this->getLink('movedown',"$rkey").'"><span class="icon-arrow-down"></span></a>';
					}
					echo '</td>';
				}
			}
			echo '</tr>'.$EOL;
		}
	}
}

// Display footer
echo '</table>'.$EOL;
if ($this->paramsMenu->get( "table_center") == "1") { echo '</center>'.$EOL; }

// Post text
echo $this->paramsMenu->get('posttext');
?>
