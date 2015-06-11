<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.2.1.1586
 * @date		2013-11-02
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
  <table class="adminlist">
  <tbody>
    <tr>
      <td width="80%">
        <textarea class="text_area" name="shAliasList" cols="80" rows="15"><?php echo $this->aliases;?></textarea>
      </td>  
      <td width="20%" style="vertical-align: top;">
        <span ><?php echo JHTML::_('tooltip', JText::_( 'COM_SH404SEF_TT_ALIAS_LIST')); ?></span>
      </td>
    </tr>
  </tbody>  
  </table>
