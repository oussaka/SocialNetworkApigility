<?php

namespace Wall\V1\Rest\Wall; 

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Http\Client;
use Zend\Filter\FilterChain;
use Zend\Filter\StripTags;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewLines;
use Zend\Dom\Query;
use Common\Mailer;

class WallService implements ServiceManagerAwareInterface
{
    protected $serviceManager;

    protected $mapper;

    /**
     * Holds the table object
     *
     * @var UsersTable
     */
    protected $usersTable;

    /**
     * Holds the table object
     *
     * @var UserStatusesTable
     */
    protected $userStatusesTable;

    /**
     * Holds the table object
     *
     * @var UserImagesTable
     */
    protected $userImagesTable;

    /**
     * Holds the table object
     *
     * @var UserLinksTable
     */
    protected $userLinksTable;

    /* public function getWalls()
    {
        return $this->getMapper()->findAll($this->getUserId());
    } */

    public function getWall($username)
    {

        $usersTable = $this->getUsersTable();
        $userStatusesTable = $this->getUserStatusesTable();
        $userImagesTable = $this->getUserImagesTable();
        $userLinksTable = $this->getUserLinksTable();

        $userData = $usersTable->getByUsername($username);
        $userStatuses = $userStatusesTable->getByUserId($userData->id)->toArray();
        $userImages = $userImagesTable->getByUserId($userData->id, $userData->avatar_id)->toArray();
        $userLinks = $userLinksTable->getByUserId($userData->id)->toArray();

        if (!empty($userData)) {
            $wallData = $userData->getArrayCopy();
        }
        $wallData['feed'] = array_merge($userStatuses, $userImages, $userLinks);
        
        usort($wallData['feed'], function($a, $b){
            $timestampA = strtotime($a['created_at']);
            $timestampB = strtotime($b['created_at']);
            
            if ($timestampA == $timestampB) {
                return 0;
            }
            
            return ($timestampA > $timestampB) ? -1 : 1;
        });

        if ($userData !== false) {
            return $wallData;
        } else {
            throw new \Exception('User not found', 404);
        }

    }

    public function saveWall($data) 
    {

        $dataArray = (array) $data;

        // $data = $dataArray->getArrayCopy();
        if (array_key_exists('status', $dataArray) && !empty($dataArray['status'])) {
            $result = $this->createStatus($dataArray);
        }

        if (array_key_exists('image', $dataArray) && !empty($dataArray['image'])) {
            $result = $this->createImage($dataArray);
        }

        if (array_key_exists('url', $dataArray) && !empty($dataArray['url'])) {
            $result = $this->createLink($dataArray);
        }

        if (array_key_exists('comment', $dataArray) && !empty($dataArray['comment'])) {
            $result = $this->createComment($dataArray);
        }

        return $result;
    }

    public function deleteWall($id)
    {
        return $this->getMapper()->delete($this->getUserId(), $id);
    }


    public function getUserId()
    {
        return $this->getServiceManager()->get('api-identity')->getRoleId();
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

    /**
     * Gets the value of mapper.
     *
     * @return mixed
     */
    public function getMapper()
    {
        return $this->mapper;
    }
    
    /**
     * Sets the value of mapper.
     *
     * @param mixed $mapper the mapper
     *
     * @return self
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;

        return $this;
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
     * This is a convenience method to load the userStatusesTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserStatusesTable
     */
    protected function getUserStatusesTable()
    {
        if (!$this->userStatusesTable) {
            $sm = $this->getServiceManager();
            $this->userStatusesTable = $sm->get('Users\Model\UserStatusesTable');
        }
        return $this->userStatusesTable;
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
     * This is a convenience method to load the userLinksTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserLinksTable
     */
    protected function getUserLinksTable()
    {
        if (!$this->userLinksTable) {
            $sm = $this->getServiceManager();
            $this->userLinksTable = $sm->get('Users\Model\UserLinksTable');
        }
        return $this->userLinksTable;
    }

    /**
     * Handle the creation of a new status
     *
     * @param array $data 
     * @return array
     */
    protected function createStatus($data)
    {
        $userStatusesTable = $this->getUserStatusesTable();
        
        $filters = $userStatusesTable->getInputFilter();
        $filters->setData($data);
        
        if ($filters->isValid()) {
            $data = $filters->getValues();

            $result = array(
                'result' => $userStatusesTable->create($data['user_id'], $data['status']),
                'id'     => $userStatusesTable->getLastInsertValue()
            );
        } else {
            $result = array(
                'result' => false,
                'errors' => $filters->getMessages()
            );
        }

        return $result;
    }

    /**
     * Handle the creation of a new image
     *
     * @param array $data
     * @return array
     */
    protected function createImage($data)
    {
        $userImagesTable = $this->getUserImagesTable();

        $filters = $userImagesTable->getInputFilter();
        $filters->setData($data);

        if ($filters->isValid()) {

            // create images folder if it does not yet exist
            if( !is_dir("public/images") ) {
                mkdir("public/images", 0777);
            }

            $filename = sprintf('public/images/%s.png', sha1(uniqid(time(), TRUE)));
            $content = base64_decode($data['image']);
            $image = imagecreatefromstring($content);

            if (imagepng($image, $filename) === TRUE) {
                $result = array(
                    'result' => $userImagesTable->create($data['user_id'], basename($filename)),
                    'id'     => $userImagesTable->getLastInsertValue()
                );
            } else {
                $result = array(
                    'result' => false,
                    'errors' => 'Error while storing the image'
                );
            }
            imagedestroy($image);
        } else {
            $result = array(
                'result' => false,
                'errors' => $filters->getMessages()
            );
        }

        return $result;
    }

    /**
     * Handle the creation of a new link
     *
     * @param array $data
     * @return array
     */
    protected function createLink($data)
    {
        $userLinksTable = $this->getUserLinksTable();

        $filters = $userLinksTable->getInputFilter();
        $filters->setData($data);

        if ($filters->isValid()) {
            $data = $filters->getValues();

            $client = new Client($data['url']);
            $client->setEncType(Client::ENC_URLENCODED);
            $client->setMethod(\Zend\Http\Request::METHOD_GET);
            $response = $client->send();

            if ($response->isSuccess()) {
                $html = $response->getBody();
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

                $dom = new Query($html);
                $title = $dom->execute('title')->current()->nodeValue;

                if (!empty($title)) {
                    $filterChain = new FilterChain();
                    $filterChain->attach(new StripTags());
                    $filterChain->attach(new StringTrim());
                    $filterChain->attach(new StripNewLines());

                    $title = $filterChain->filter($title);
                } else {
                    $title = NULL;
                }

                $result = $userLinksTable->create(
                    $data['user_id'],
                    $data['url'],
                    $title
                );

                return array(
                    'result' => $result,
                    'id'     => $userLinksTable->getLastInsertValue()
                );
            }
        }

        return array(
            'result' => false,
            'errors' => $filters->getMessages()
        );
    }

    /**
     * Handle the creation of a new comment
     *
     * @param array $data
     * @return array
     */
    protected function createComment($data)
    {
        $userCommentsTable = $this->getUserCommentsTable();
        $usersTable = $this->getUsersTable();
        $user = $usersTable->getById($data['user_id']);

        $data['comment'] = array(
            'user_ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
            'user_agent' => $this->getRequest()->getServer('HTTP_USER_AGENT'),
            'comment_type' => 'comment',
            'comment_author' => sprintf('%s %s', $user->name, $user->surname),
            'comment_author_email' => $user->email,
            'comment_content' => $data['comment']
        );

        switch ($data['type']) {
            case \Users\Model\UserStatusesTable::COMMENT_TYPE_ID:
                $validatorTable = \Users\Model\UserStatusesTable::TABLE_NAME;
                $table = $this->getUserStatusesTable();
                break;
            case \Users\Model\UserImagesTable::COMMENT_TYPE_ID:
                $validatorTable = \Users\Model\UserImagesTable::TABLE_NAME;
                $table = $this->getUserImagesTable();
                break;
            case \Users\Model\UserLinksTable::COMMENT_TYPE_ID:
                $validatorTable = \Users\Model\UserLinksTable::TABLE_NAME;
                $table = $this->getUserLinksTable();
                break;
        }

        $entry = $table->getById($data['entry_id']);
        $recipient = $usersTable->getById($entry['user_id']);

        $config = $this->getServiceLocator()->get('Config');
        $filters = $userCommentsTable->getInputFilter($validatorTable, $config['akismet']);
        $filters->setData($data);

        if ($filters->isValid()) {
            $data = $filters->getValues();

            $creationResult = $userCommentsTable->create($data['user_id'], $data['type'], $data['entry_id'], $data['comment']['comment_content']);

            $result = array(
                'result' => $creationResult
            );

            if($creationResult && $recipient['id'] != $user['id']) {
                Mailer::sendContentNotificationEmail($recipient['email'], $recipient['name'], $user['name']);
            }
        } else {
            $result = array(
                'result' => false,
                'errors' => $filters->getMessages()
            );
        }

        return $result;
    }


}
