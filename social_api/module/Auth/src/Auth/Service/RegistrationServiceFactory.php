<?php

namespace Auth\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Model\RegistrationMapper;
use Auth\Service\RegistrationService;

class RegistrationServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $mapper = new RegistrationMapper($serviceManager->get('Zend\Db\Adapter\Adapter'));
        // $mapper->setDbAdapter($serviceManager->get('Zend\Db\Adapter\Adapter'));
        // $mapper->setEntityPrototype($serviceManager->get('Auth\Model\RegistrationEntity'));
        // $mapper->getHydrator()->setUnderscoreSeparatedKeys(false);
        $mapper->setHydrator(new \Zend\Stdlib\Hydrator\ArraySerializable);


        $service = new RegistrationService();
        $service->setMapper($mapper);

        return $service;
    }
}