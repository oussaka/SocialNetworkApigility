<?php
namespace Feeds\V1\Rest\Feeds;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;
use Zend\ServiceManager\ServiceManager;
use Zend\Feed\Reader\Reader;
use Zend\Http\Client;
use Zend\Dom\Query;
use Zend\Validator\Db\NoRecordExists;

class FeedsResource extends AbstractResourceListener
{
    /**
     * Instance of serviceManager
     */
    protected $serviceManager;

    /**
     * Hold the table instance
     *
     * @var UserFeedsTable
     */
    protected $userFeedsTable;

    /**
     * Hold the table instance
     *
     * @var UserFeedArticlesTable
     */
    protected $userFeedArticlesTable;

    /**
     * Hold the table instance
     *
     * @var UsersTable
     */
    protected $usersTable;

    /**
     * Add a new subscription
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        // cast data to array
        $data = (array) $data;

        // $username = $this->params()->fromRoute('username');
        $username = $this->getEvent()->getRouteMatch()->getParam('username');

        $usersTable = $this->getTable('UsersTable');
        $user = $usersTable->getByUsername($username);

        $userFeedsTable = $this->getTable('UserFeedsTable');
        $rssLinkXpath = '//link[@type="application/rss+xml"]';
        $faviconXpath = '//link[@rel="shortcut icon"]';

        $client = new Client($data['url']);
        $client->setEncType(Client::ENC_URLENCODED);
        $client->setMethod(\Zend\Http\Request::METHOD_GET);

        $response = $client->send();

        if ($response->isSuccess()) {
            $html = $response->getBody();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

            $dom = new Query($html);
            $rssUrl = $dom->execute($rssLinkXpath);

            if (!count($rssUrl)) {
                return new JsonModel(array(
                    'result' => false,
                    'message' => 'Rss link not found in the url provided'
                ));
            }
            $rssUrl = $rssUrl->current()->getAttribute('href');

            $faviconUrl = $dom->execute($faviconXpath);
            if (count($faviconUrl)) {
                $faviconUrl = $faviconUrl->current()->getAttribute('href');
            } else {
                $faviconUrl = null;
            }
        } else {
            return new JsonModel(array(
                'result' => false,
                'message' => 'Website not found'
            ));
        }

        $validator = new NoRecordExists(
            array(
                'table'   => 'user_feeds',
                'field'   => 'url',
                'adapter' => $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')
            )
        );

        if (!$validator->isValid($rssUrl)) {

            /* return new \ZF\ApiProblem\ApiProblemResponse(
                new ApiProblem(500, 'You already have a subscription to this url', null, "Internal Server Error", array("status" => false))
            ); */
            return new ApiProblem(500, 'You already have a subscription to this url', null, "Internal Server Error", array("status" => false));
            /* return array(
                'result' => false,
                'message' => 'You already have a subscription to this url'
            ); */
        }

        $rss = Reader::import($rssUrl);

        /* @var $userFeedsTable \Zend\Db\TableGateway\AbstractTableGateway */
        $result = $userFeedsTable->create($user->id, $rssUrl, $rss->getTitle(), $faviconUrl);
        return array(
            'result' => $result,
            'id'     => $userFeedsTable->getLastInsertValue()
        );
    }

    /**
     * Delete a subscription
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        $username = $this->params()->fromRoute('username');
        $usersTable = $this->getTable('UsersTable');
        $user = $usersTable->getByUsername($username);

        $userFeedsTable = $this->getTable('UserFeedsTable');
        $userFeedArticlesTable = $this->getTable('UserFeedArticlesTable');

        $userFeedArticlesTable->delete(array('feed_id' => $id));
        return new JsonModel(array(
            'result' => $userFeedsTable->delete(array('id' => $id, 'user_id' => $user->id))
        ));
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Return a list of feed subscription for a specific user
     *
     * @param  string $username
     * @return ApiProblem|mixed
     */
    public function fetch($username)
    {
        // $username = $this->params()->fromRoute('username');
        $usersTable = $this->getTable('UsersTable');
        $user = $usersTable->getByUsername($username);
        $userFeedsTable = $this->getTable('UserFeedsTable');
        $userFeedArticlesTable = $this->getTable('UserFeedArticlesTable');

        $feedsFromDb = $userFeedsTable->getByUserId($user->id);
        $feeds = array();
        foreach ($feedsFromDb as $f) {
            $feeds[$f->id] = $f;
            $feeds[$f->id]['articles'] = $userFeedArticlesTable->getByFeedId($f->id)->toArray();
        }

        return $feeds;
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        return new ApiProblem(405, 'The GET method has not been defined for collections');
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }

    protected function getTable($table)
    {
        $sm = $this->getServiceManager();

        switch ($table) {
            case 'UserFeedsTable':
                if (!$this->userFeedsTable) {
                    $this->userFeedsTable = $sm->get('Feeds\Model\UserFeedsTable');
                }

                return $this->userFeedsTable;
            case 'UserFeedArticlesTable':
                if (!$this->userFeedArticlesTable) {
                    $this->userFeedArticlesTable = $sm->get('Feeds\Model\UserFeedArticlesTable');
                }

                return $this->userFeedArticlesTable;
            case 'UsersTable':
                if (!$this->usersTable) {
                    $this->usersTable = $sm->get('Users\Model\UsersTable');
                }

                return $this->usersTable;
        }
    }

    /**
     * Gets the value of serviceManager.
     *
     * @return mixed
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Sets the value of serviceManager.
     *
     * @param mixed $serviceManager the service manager
     *
     * @return self
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

}
