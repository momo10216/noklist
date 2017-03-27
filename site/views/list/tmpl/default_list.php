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
// Display header
echo '<table>'.$EOL;
echo '<tr>';
foreach ($this->colHeader as $col) {
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
if (count($rows)>0) {
	$deleteConfirmMsg = JText::_("COM_NOKLIST_ENTRY_CONFIRM_DELETE");
	foreach($rows as $key => $row) {
		echo '<tr>';
		foreach($row as $field) {
			echo '<td>'.$field.'</td>';
		}
/*
		echo "<td".$borderStyle.">";
		if ($itemCanDo->get('core.edit')) {
			$uriEdit->setVar('id',$item->id);
			echo '<a style="text-decoration: none;" href="'.$this->getLink('edit',$key).'"><span class="icon-edit"></span></a>';
		}
		echo '</td>';
		echo "<td".$borderStyle.">";
		if ($itemCanDo->get('core.delete')) {
			$uriDelete->setVar('id',$item->id);
			echo '<a style="text-decoration: none;" href="'.$this->getLink('delete',$key).'" onClick="return confirm(\''.$deleteConfirmMsg.'\');"><span class="icon-trash"></span></a>';
		}
		echo '</td>';
*/
		echo '</tr>'.$EOL;
	}
}
echo "</table>\n";
?>