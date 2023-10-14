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

defined('_JEXEC') or die;

/**
 * CVS helper.
 *
 * @package     Joomla
 * @subpackage  com_noklist
 * @since       3.0
 */
class JsonHelper {
	public static function save($data, $encoding, $filename) {
		$content = json_encode($data);;
		if ($encoding != "utf-8") {
			$content = iconv("UTF-8", strtoupper($encoding)."//TRANSLIT", $content); 
		}
		header('Content-Type: application/json; charset='.$encoding);
		header('Content-Length: '.strlen($content));
		if (!empty($filename)) {
			header('Content-Disposition: attachment; filename="'.$filename.'"');
		}
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Pragma: no-cache');
		echo $content;
		// Close the application.
		$app = JFactory::getApplication();
		$app->close();
	}
}
?>
