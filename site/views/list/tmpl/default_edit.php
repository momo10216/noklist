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
$id = JRequest::getVar('id');
$record = array();
if (!empty($id)) {
	$rows = $this->getData();
	if (isset($rows[$id])) { $record = $rows[$id]; }
	$title = JText::_('COM_NOKLIST_PAGE_EDIT_TITLE');
} else {
	$title = JText::_('COM_NOKLIST_PAGE_ADD_TITLE');
}
?>
<h1><?php echo $title; ?></h1>
<form action="<?php echo $this->getLink('save',$id); ?>" method="post" name="adminForm" id="adminForm">

<?php
foreach ($this->colHeader as $key => $col) {
	$value = '';
	if (isset($record[$key])) { $value = $record[$key]; }
	echo '	<div class="control-label"><label for="jform_'.$key.'" title="" data-original-title="'.$col.'">'.$col.'</label></div>'.$EOL;
	echo '	<div class="controls"><input id="jform_'.$key.'" type="text" name="col_'.$key.'" value="'.$value.'"/></div>'.$EOL;
}
?>
	<p align="center">
		<button type="submit">
			<?php echo JText::_('JSAVE') ?>
		</button>
		<button type="submit" onClick="document.adminForm.task.value='cancel';">
			<?php echo JText::_('JCANCEL') ?>
		</button>
	</p>
</form>