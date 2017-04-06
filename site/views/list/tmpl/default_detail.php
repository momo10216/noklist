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

// Display record
$id = JFactory::getURI()->getVar('id');
$rows = $this->getData();
if ($id == '') {
	$row = array_pop($rows);
} else {
	if (isset($rows[$id])) {
		$row = $rows[$id];
	}
}
if (isset($row)) {
	foreach($this->colHeaders as $key => $col) {
		$value = '';
		if (isset($row[$key])) { $value = $row[$key]; }
		echo '<tr><td>'.$col.'</td><td>'.$this->getDisplayValue($col,$value).'</td></tr>'.$EOL;
	}
}

// Display footer
echo '</table>'.$EOL;
// Navigation
?>	<p align="center">
		<button onClick="location.href='<?php echo $this->getLink('list'); ?>';">
			<?php echo JText::_('COM_NOKLIST_LIST_BUTTON') ?>
		</button>
<?php if ($this->canChange()): ?>
		<button onClick="location.href='<?php echo $this->getLink('edit',$id); ?>';">
			<?php echo JText::_('COM_NOKLIST_EDIT_BUTTON') ?>
		</button>
		<button onClick="if (confirm('<?php echo JText::_("COM_NOKLIST_ENTRY_CONFIRM_DELETE"); ?>')) { location.href='<?php echo $this->getLink('delete',$id); ?>'; }">
			<?php echo JText::_('COM_NOKLIST_DELETE_BUTTON') ?>
		</button>
<?php endif; ?>
	</p>
