<?php

class Ho_PriceCrawler_Model_System_Config_Source_JobIds
{
    /**
     * @return array
     */
    public function toArray()
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

        $jobs = $connection->select()
            ->from($resource->getTableName('ho_pricecrawler/logs') . ' AS l')
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('job_id')
            ->group('job_id');

        $jobs->join($resource->getTableName('ho_pricecrawler/sites') . ' AS s', 'l.site_id = s.site_id', 'name');

        $jobs = $connection->fetchAll($jobs);

        $result = array();
        foreach ($jobs as $job) {
            $result[$job['job_id']] = $job['job_id'] . ' (' . $job['name'] . ')';
        }

        return $result;
    }
}
