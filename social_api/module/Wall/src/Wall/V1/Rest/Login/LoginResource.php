<?php
namespace Wall\V1\Rest\Login;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;
use Zend\ServiceManager\ServiceManager;
use Zend\Crypt\Password\Bcrypt;
use OAuth2\Storage\Pdo;
use OAuth2\Server;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\Request;
use OAuth2\Response;

class LoginResource extends AbstractResourceListener
{

    protected $serviceManager;

    /**
     * Holds the table object
     *
     * @var UsersTable
     */
    protected $usersTable;

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $usersTable = $this->getUsersTable();
        $user = $usersTable->getByUsername($data->username);

        $bcrypt = new Bcrypt();
        if (!empty($user) && $bcrypt->verify($data->password, $user->password)) {

            /*
            $storage = new Pdo($usersTable->adapter->getDriver()->getConnection()->getConnectionParameters());
            // $storage->setClientDetails("testclient", 'testpass', "http://example.com");
            $server = new Server($storage);
            $server->addGrantType(new ClientCredentials($storage));
            // $request = \OAuth2\Request::createFromGlobals();
            // $token = $server->grantAccessToken($request);
            // echo json_encode($token);
            $response = $server->handleTokenRequest(Request::createFromGlobals());

            if (!$response->isSuccessful()) {
                $result = new JsonModel(array(
                    'result' => false,
                    'errors' => 'Invalid oauth'
                ));
            }

            return new JsonModel($response->getParameters());
            */

            $result = array(
                'id' => $user->id,
                'result' => true,
                'errors' => 'connected with success.'
            );
        } else {
            $result = new ApiProblem(422, 'Invalid Username or password');
            /* $result = array(
                'result' => false,
                'errors' => 'Invalid Username or password'
            ); */
        }

        return $result;

    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
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
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
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

    /**
     * This is a convenience method to load the usersTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UsersTable
     */
    protected function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceManager();
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
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
