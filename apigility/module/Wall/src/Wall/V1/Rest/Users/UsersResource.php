<?php
namespace Wall\V1\Rest\Users;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;
use Zend\ServiceManager\ServiceManager;
use Zend\Crypt\Password\Bcrypt;
use Common\Mailer;

class UsersResource extends AbstractResourceListener
{

    /**
     *
     */
    protected $serviceManager;

    /**
     * Holds the table object
     *
     * @var UsersTable
     */
    protected $usersTable;

    /**
     * Holds the table object
     *
     * @var UserImagesTable
     */
    protected $userImagesTable;

    /**
     * Create a resource
     *
     * @param  mixed $unfilteredData
     * @return ApiProblem|mixed
     */
    public function create($unfilteredData)
    {
        $unfilteredData = (array) $unfilteredData;
        $usersTable = $this->getUsersTable();

        $filters = $usersTable->getInputFilter();
        $filters->setData($unfilteredData);

        if ($filters->isValid()) {
            $data = $filters->getValues();

            $avatarContent = array_key_exists('avatar', $unfilteredData) ? $unfilteredData['avatar'] : NULL;

            $bcrypt = new Bcrypt();
            $data['password'] = $bcrypt->create($data['password']);

            if ($usersTable->create($data)) {
                $user = $usersTable->getByUsername($data['username']);
                if (!empty($avatarContent)) {
                    $userImagesTable = $this->getUserImagesTable();

                    $filename = sprintf('public/images/%s.png', sha1(uniqid(time(), TRUE)));
                    $content = base64_decode($avatarContent);
                    $image = imagecreatefromstring($content);

                    if (imagepng($image, $filename) === TRUE) {
                        $userImagesTable->create($user['id'], basename($filename));
                    }
                    imagedestroy($image);

                    $image = $userImagesTable->getByFilename(basename($filename));
                    $usersTable->updateAvatar($image['id'], $user['id']);
                }

                Mailer::sendWelcomeEmail($user['email'], $user['name']);

                $result = array(
                    'result'    => true,
                    'id'        =>  $usersTable->getLastInsertValue()

                );
            } else {
                $result = array(
                    'result' => false
                );
            }
        } else {
            $result = array(
                'result' => false,
                'errors' => $filters->getMessages()
            );
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
     * Fetch a user
     *
     * @param string $username
     * @return ApiProblem|mixed
     */
    public function fetch($username)
    {

        $usersTable = $this->getUsersTable();
        $userImagesTable = $this->getUserImagesTable();

        $userData = $usersTable->getByUsername($username);

        if ($userData !== false) {
            $userData['avatar'] = $userImagesTable->getById($userData['avatar_id']);
            return $userData;
        } else {
            throw new \Exception('User not found', 404);
        }
        // return new ApiProblem(405, 'The GET method has not been defined for individual resources');
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
     * This is a convenience method to load the userImagesTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserImagesTable
     */
    protected function getUserImagesTable()
    {
        if (!$this->userImagesTable) {
            $sm = $this->getServiceManager();
            $this->userImagesTable = $sm->get('Users\Model\UserImagesTable');
        }
        return $this->userImagesTable;
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
