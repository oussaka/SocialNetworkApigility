<?php
namespace Wall\V1\Rest\Users;

use zend\ServiceManager\ServiceLocatorInterface;

class UsersResourceFactory
{
    public function __invoke(ServiceLocatorInterface $serviceManager)
    {
        $user = new UsersResource();
        $user->setServiceManager($serviceManager);

        return $user;
    }
}
