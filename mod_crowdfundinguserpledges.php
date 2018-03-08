<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Crowdfunding.init');

JLoader::register('CrowdfundingUserPledgesModuleHelper', JPATH_ROOT . '/modules/mod_crowdfundinguserpledges/helper.php');

$moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

// Get user ID.
$userId   = Prism\Integration\Helper::getUserId();
if (!$userId) {
    return;
}

// Prepare results limit.
$limitResults = $params->get('results_limit', 5);
if ($limitResults <= 0) {
    $limitResults = 5;
}

// Prepare caching.
$projects = null;
$cache    = null;
if ($app->get('caching', 0)) {
    $cache = JFactory::getCache('com_crowdfunding', '');
    $cache->setLifeTime(Prism\Constants::TIME_SECONDS_24H);

    $projects = $cache->get(Crowdfunding\Constants::CACHE_USER_PLEDGED_PROJECTS.':'.(int)$userId);
    if ($projects === false) {
        $projects = null;
    }
}

// Get projects.
if ($projects === null) {
    $projects = CrowdfundingUserPledgesModuleHelper::getProjects($limitResults, $userId);

    // Store the records in the cache.
    if ($cache !== null and count($projects) > 0) {
        $cache->store($projects, Crowdfunding\Constants::CACHE_USER_PLEDGED_PROJECTS . ':' . (int)$userId);
    }
}

if (!$projects) {
    $userName     = Prism\Utilities\UserHelper::getName($userId);
    $linkDiscover = JRoute::_(CrowdfundingHelperRoute::getDiscoverRoute());
    require JModuleHelper::getLayoutPath('mod_crowdfundinguserpledges', 'system/nocontributions');
    return;
}

// Get component parameters
$componentParams = JComponentHelper::getParams('com_crowdfunding');
/** @var  $componentParams Joomla\Registry\Registry */

// Get options
$displayInfo        = $params->get('show_info', Prism\Constants::DISPLAY);
$displayDescription = $params->get('show_description', $componentParams->get('show_description', Prism\Constants::DISPLAY));
$displayCreator     = $params->get('show_author', $componentParams->get('show_author', Prism\Constants::DISPLAY));
$displayReadon      = $params->get('show_readon', Prism\Constants::DO_NOT_DISPLAY);
$displaySeeProjects = $params->get('show_see_projects', Prism\Constants::DO_NOT_DISPLAY);
$titleLength        = $params->get('title_length', $componentParams->get('title_length'));
$descriptionLength  = $params->get('description_length', $componentParams->get('description_length'));

$imagesDirectory    = $componentParams->get('images_directory', 'images/crowdfunding');
$dateFormat         = $componentParams->get('date_format_views', JText::_('DATE_FORMAT_LC3'));

if ($displayInfo) {
    $container  = Prism\Container::getContainer();
    /** @var  $container Joomla\DI\Container */

    $containerHelper = new Crowdfunding\Container\Helper();
    $money           = $containerHelper->fetchMoneyFormatter($container, $componentParams);
}

// Display user social profile ( integrate ).
if ($displayCreator) {
    $socialPlatform = $componentParams->get('integration_social_platform');

    if ($socialPlatform !== null and $socialPlatform !== '') {
        $usersId        = Prism\Utilities\ArrayHelper::getIds($projects, 'user_id');
        $socialProfiles = CrowdfundingHelper::prepareIntegration($socialPlatform, $usersId);
    }
}

if (count($projects) > 0) {
    require JModuleHelper::getLayoutPath('mod_crowdfundinguserpledges', $params->get('layout', 'default'));
}
