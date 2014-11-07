<?php

class Ho_PriceCrawler_Model_Scrapinghub_Abstract extends Mage_Core_Model_Abstract
{
    const XML_PATH_SCRAPINGHUB_API_URL = 'ho_pricecrawler/scrapinghub/api_url';
    const XML_PATH_SCRAPINGHUB_API_KEY = 'ho_pricecrawler/scrapinghub/api_key';
    const XML_PATH_SCRAPINGHUB_PROJECT_ID = 'ho_pricecrawler/scrapinghub/project_id';

    /**
     * POST action to the Scrapinghub API
     *
     * Possible POST actions:
     * - jobs/update
     * - jobs/delete
     * - jobs/stop
     * - schedule
     *
     * @see http://doc.scrapinghub.com/api.html
     * @param string $action POST action
     * @param array $options cURL options
     * @return mixed
     */
    public function post($action, $options)
    {
        return $this->_execute($action, $options, 'json', 'post');
    }

    /**
     * GET action to the Scrapinghub API
     *
     * Possible GET actions:
     * - jobs/list (json)
     * - jobs/list (jl)
     * - spiders/list (json)
     * - items (json)
     * - items (jl)
     * - items (csv)
     * - as/spider-properties (json)
     * Note: Remember to pass the right file extension
     *
     * @see http://doc.scrapinghub.com/api.html
     * @param string $action GET action name
     * @param array $options cURL options
     * @param string $extension json, jl or csv
     * @return mixed
     */
    public function get($action, $options = array(), $extension = 'json')
    {
        return $this->_execute($action, $options, $extension, 'get');
    }

    /**
     * Execute cURL API call to Scrapinghub
     *
     * @see http://doc.scrapinghub.com/api.html
     * @param string $action Action name
     * @param array $options cURL options
     * @param string $extension json, jl or csv
     * @param string $type get or post
     * @return mixed
     */
    protected function _execute($action, $options = array(), $extension = 'json', $type = 'get')
    {
        $curl = curl_init();

        $options = array_merge(array('project' => $this->_getProjectId()), $options);

        $apiUrl = $this->_createApiUrl($action, $extension);
        if ($type == 'get') {
            $apiUrl .= '?' . http_build_query($options);
        }

        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        curl_setopt($curl, CURLOPT_USERPWD, $this->_getApiKey());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if ($type == 'post') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($options));
        }

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    /**
     * Create Scrapinghub API URL
     *
     * @param string $action Action name
     * @param string $extension File extension
     * @return string API URL
     */
    protected function _createApiUrl($action, $extension)
    {
        return $this->_getApiUrl() . $action . '.' . $extension;
    }

    /**
     * Get Scrapinghub API URL
     * @return string
     */
    protected function _getApiUrl()
    {
        return Mage::getStoreConfig(self::XML_PATH_SCRAPINGHUB_API_URL);
    }

    /**
     * Get Scrapinghub API key
     * @return string
     */
    protected function _getApiKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_SCRAPINGHUB_API_KEY);
    }

    /**
     * Get Scrapinghub project ID
     * @return string
     */
    protected function _getProjectId()
    {
        return Mage::getStoreConfig(self::XML_PATH_SCRAPINGHUB_PROJECT_ID);
    }
}