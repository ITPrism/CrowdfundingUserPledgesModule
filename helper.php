<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class CrowdfundingUserPledgesModuleHelper
{
    public static function getProjects($limit, $userId)
    {
        if (!$userId) {
            return array();
        }

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(
                'a.title, a.short_desc, a.image, a.image_small, a.image_square, ' .
                'a.goal, a.funded, a.funding_start, a.funding_end, a.funding_days, ' .
                'a.user_id, a.funding_type, ' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug, ' .
                $query->concatenate(array('b.id', 'b.alias'), ':') . ' AS catslug, ' .
                'c.name AS user_name'
            )
            ->from($db->quoteName('#__crowdf_projects', 'a'))
            ->leftJoin($db->quoteName('#__categories', 'b') . ' ON a.catid = b.id')
            ->leftJoin($db->quoteName('#__users', 'c') . ' ON a.user_id = c.id')
            ->rightJoin($db->quoteName('#__crowdf_transactions', 'd') . ' ON d.project_id = a.id')
            ->where('d.investor_id = '. (int)$userId)
            ->order('d.txn_date DESC')
            ->group('a.id');

        if (!$limit) {
            $limit = 5;
        }

        $db->setQuery($query, 0, (int)$limit);

        return (array)$db->loadObjectList();
    }
}
