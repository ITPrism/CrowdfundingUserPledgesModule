<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
/**
 * @var string $userName
 * @var string $linkDiscover
 */
?>
<div class="cf-mod-userpledges<?php echo $moduleclassSfx; ?>">
    <div><?php echo JText::sprintf('MOD_CROWDFUNDINGUSERPLEDGES_HAS_NOT_CONTRIBUTE', $userName); ?></div>
    <div><?php echo JText::sprintf('MOD_CROWDFUNDINGUSERPLEDGES_FIND_CAMPAIGN', $linkDiscover); ?></div>
</div>