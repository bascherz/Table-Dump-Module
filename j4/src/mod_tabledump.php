<?php
/**
 * Table Dump Module Entry Point
 * 
 * @package    Table Dump
 * @subpackage Modules
 * @license    GNU/GPL, see LICENSE.php
 * @link       http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * mod_sitemembers is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Module\TableDump\Site\Helper\TableDumpHelper;

$tablehtml = TableDumpHelper::getText();

if ($params->def('prepare_content', 1)) {
    PluginHelper::importPlugin('content');
    $module->content = HTMLHelper::_('content.prepare', $module->content, '', 'mod_custom.content');
}

require ModuleHelper::getLayoutPath('mod_tabledump', $params->get('layout', 'default'));
