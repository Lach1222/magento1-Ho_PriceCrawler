<?php

class Ho_PriceCrawler_Model_Scrapinghub_Jobs extends Ho_PriceCrawler_Model_Scrapinghub_Abstract
{
    /**
     * Schedule a job in Scrapinghub of the given spider identifier
     *
     * @param string $spiderIdentifier
     * @return bool
     */
    public function schedule($spiderIdentifier)
    {
        $result = $this->post('schedule', array('spider' => $spiderIdentifier, 'add_tag' => 'magento'));

        $result = json_decode($result);

        return $result->status == 'ok';
    }

    /**
     * Possible options:
     * - project
     * - job
     * - spider
     * - state (pending, running, finished)
     * - has_tag
     * - lacks_tag
     *
     * @param array $params
     * @return mixed
     */
    public function listJobs($params = array())
    {
        $result = $this->get('jobs/list', $params);

        $result = json_decode($result);

        if ($result->status === strtolower('error')) {
            Mage::getSingleton('adminhtml/session')->addError('Error: ' . $result->message);

            return false;
        }

        return $result->jobs;
    }

    /**
     * Delete job from Scrapinghub
     *
     * @param string $jobId <project>/<spider_id>/<job_id>
     * @return bool
     */
    public function deleteJob($jobId)
    {
        $result = $this->post('jobs/delete', array('job' => $jobId));

        $result = json_decode($result);

        return $result->status == 'ok';
    }

    /**
     * Cancel a running job at Scrapinghub
     *
     * @param string $jobId <project>/<spider_id>/<job_id>
     * @return bool
     */
    public function stopJob($jobId)
    {
        $result = $this->post('jobs/stop', array('job' => $jobId));

        $result = json_decode($result);

        return $result->status == 'ok';
    }
}