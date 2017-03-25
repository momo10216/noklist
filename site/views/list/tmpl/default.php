<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	NoK-PrjMgnt
* @copyright	Copyright (c) 2017 Norbert Kümin. All rights reserved.
* @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
* @author	Norbert Kuemin
* @authorEmail	momo_102@bluemail.ch
*/
defined('_JEXEC') or die; // no direct access
$details = false;
$uriEdit = new JURI(JURI::Root().'/index.php');
$uriEdit->setVar('layout','form');
$uriEdit->setVar('Itemid','');
$uriEdit->setVar('view','task');
$uriEdit->setVar('option','com_nokprjmgnt');
$uriDelete = new JURI(JURI::Root().'/index.php');
$uriDelete->setVar('layout','delete');
$uriDelete->setVar('Itemid','');
$uriDelete->setVar('view','task');
$uriDelete->setVar('option','com_nokprjmgnt');
if ($this->paramsMenuEntry->get('detail_enable') != '0') {
	$details = true;
	$uriDetail = new JURI(JURI::Root().'/index.php');
	$uriDetail->setVar('layout','detail');
	$uriDetail->setVar('Itemid','');
	$uriDetail->setVar('view','task');
	$uriDetail->setVar('option','com_nokprjmgnt');
}
// Get columns
$cols = array();
for ($i=1;$i<=20;$i++) {
	$field = 'column_'.$i;
	$cols[] = $this->paramsMenuEntry->get($field);
}
$colcount = count($cols);
// Display
$border='border-style:solid; border-width:1px';
$width='';
if ($this->paramsMenuEntry->get('width') != '0') {
	$width='width="'.$this->paramsMenuEntry->get('width').'" ';
}
if ($this->paramsMenuEntry->get('table_center') == '1') echo "<center>\n";
if ($this->paramsMenuEntry->get('border_type') != '') {
	echo '<table '.$width.'border="0" cellspacing="0" cellpadding="'.$this->paramsMenuEntry->get('cellpadding').'" style="'.$border.'">'."\n";
} else {
	echo '<table '.$width.'border="0" cellspacing="0" cellpadding="'.$this->paramsMenuEntry->get('cellpadding').'" style="border-style:none; border-width:0px">'."\n";
}
$header = $this->getModel()->getHeader($cols);
$aligns = $this->getModel()->getAlign($cols);
echo '<tr>';
foreach ($cols as $col) {
	if ($header[$col] != '') {
		echo '<th';
		if (isset($aligns[$col]) && !empty($aligns[$col])) {
			echo ' align="'.$aligns[$col].'"';
		}
		echo '>';
		if ($this->paramsMenuEntry->get('show_header', '1') == '1') {
			echo $header[$col];
		}
		echo '</th>';
	}
}
echo '<th align="left">';
if ($this->componentCanDo->get('core.create')) {
	echo '<a style="text-decoration: none;" href="'.$uriEdit->toString().'"><span class="icon-new"></span></a>';
}
echo '</th>';
echo '</tr>'."\n";
$detailColumn = $this->paramsMenuEntry->get('detail_column_link');
//echo "<pre>".$detailColumn."</pre>";
if ($this->items) {
	$deleteConfirmMsg = JText::_("COM_NOKPRJMGNT_PROJECT_CONFIRM_DELETE");
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
	foreach($this->items as $item) {
		$itemCanDo = JHelperContent::getActions('com_nokprjmgnt','project',$item->id);
		$row = (array) $item;
		echo "<tr>\n";
		if ($details) {
			$uriDetail->setVar('id',$item->id);
		}
		for($j=0;$j<$colcount;$j++) {
			$field = $cols[$j];
			if (!empty($field)) {
				$data = $row[$field];
				$data = str_replace('0000-00-00 00:00:00','',$data);
				$data = str_replace(' 00:00:00','',$data);
				if (($field == 'responsible_user_id') || ($field == 'assign_user_ids')) {
					$data = $this->getModel()->getConvertUserIdsToNames($data);
				}
				echo '<td';
				if (isset($aligns[$field]) && !empty($aligns[$field])) {
					echo ' align="'.$aligns[$field].'"';
				}
				echo $borderStyle.'>';
				if ($details && (($detailColumn == "") || ($detailColumn == $field))) {
					echo "<a href=\"".$uriDetail->toString()."\">".$data."</a>";
				} else {
					echo $data;
				}
				echo "</td>";
			}
		}
		echo "<td".$borderStyle.">";
		if ($itemCanDo->get('core.edit')) {
			$uriEdit->setVar('id',$item->id);
			echo '<a style="text-decoration: none;" href="'.$uriEdit->toString().'"><span class="icon-edit"></span></a>';
		}
		echo '</td>';
		echo "<td".$borderStyle.">";
		if ($itemCanDo->get('core.delete')) {
			$uriDelete->setVar('id',$item->id);
			echo '<a style="text-decoration: none;" href="'.$uriDelete->toString().'" onClick="return confirm(\''.$deleteConfirmMsg.'\');"><span class="icon-trash"></span></a>';
		}
		echo '</td>';
		echo "</tr>\n";
	}
}
echo "</table>\n";
if ($this->paramsMenuEntry->get( "table_center") == "1") echo "</center>\n";
?>