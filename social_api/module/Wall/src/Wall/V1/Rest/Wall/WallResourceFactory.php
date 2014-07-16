<?php
namespace Wall\V1\Rest\Wall;

use Zend\ServiceManager\ServiceLocatorInterface;

class WallResourceFactory
{
    public function __invoke(ServiceLocatorInterface $serviceManager)
    {

        $service = new WallService();
        $service->setServiceManager($serviceManager);
    	// $mapper = new WallMapper;
        // $mapper->setDbAdapter($serviceManager->get('Zend\Db\Adapter\Adapter'));
        // $mapper->setEntityPrototype($serviceManager->get('Wall\V1\Wall\WallEntity'));
        // $mapper->getHydrator()->setUnderscoreSeparatedKeys(false);
        
        $resource = new WallResource();
        $resource->setWallService($service);

        return $resource;
    }
}
