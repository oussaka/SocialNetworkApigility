<?php
namespace Wall\V1\Rest\Login;

use zend\ServiceManager\ServiceLocatorInterface;

class LoginResourceFactory
{
    public function __invoke(ServiceLocatorInterface $serviceManager)
    {
        $user = new LoginResource();
        $user->setServiceManager($serviceManager);

        return $user;
    }
}
