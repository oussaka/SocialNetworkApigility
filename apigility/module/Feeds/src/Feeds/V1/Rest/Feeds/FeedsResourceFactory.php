<?php
namespace Feeds\V1\Rest\Feeds;

use Zend\ServiceManager\ServiceManager;

class FeedsResourceFactory
{
    public function __invoke(ServiceManager $serviceManager)
    {
        $Feeds = new FeedsResource();
        $Feeds->setServiceManager($serviceManager);

        return $Feeds;
    }
}
