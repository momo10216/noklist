<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_noklist
 *
 * @copyright   Copyright (c) 2023 Norbert Kümin. All rights reserved.
 * @license     GNU General Public License version 2 or higher; see LICENSE
 */

namespace NKuemin\Component\NoKList\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Default Controller of NoKList component
 *
 * @package     Joomla.Site
 * @subpackage  com_noklist
 */
class DisplayController extends BaseController {
    /**
     * The default view for the display method.
     *
     * @var string
     */
    protected $default_view = 'list';
}