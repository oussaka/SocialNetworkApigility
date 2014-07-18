<?php
namespace Wall;

use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{


    public function getServiceConfig()
    {
        return array(
          'factories' => array(
                // 'Wall\V1\Rest\Wall\WallResource' => 'Wall\V1\Rest\Wall\WallResourceFactory',
                /* 'Wall\V1\Rest\Wall\WallResource' => function($sm) {
                    return new \Wall\V1\Rest\Wall\WallResource($sm);
                }, */
            /* 'Wall\V1\Rest\Wall\WallMapper' =>  function ($sm) {
              $adapter = $sm->get('Zend\Db\Adapter\Adapter');
              return new \Wall\V1\Rest\Wall\WallMapper($adapter);
            }, */
            /*'Wall\V1\Rest\Wall\WallResource' => function ($sm) {
              $mapper = $sm->get('Wall\V1\Rest\Wall\WallMapper');
              $wallresource = new \Wall\V1\Rest\Wall\WallResource($mapper);
              // $wallresource->setUsersTable($sm->get('Users\Model\UsersTable'));
              return $wallresource;
             },*/
          ),
          'invokables' => array(
              'Wall\V1\Wall\WallEntity' => 'Wall\V1\Rest\Wall\WallEntity',
          ),
          /* 'initializers' => array(
              function ($instance, $sm) {
                if ($instance instanceof \Zend\ServiceManager\ServiceLocatorAwareInterface) {
                  $instance->setServiceLocator($sm);
                }
              }
          ), */
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'ZF\Apigility\Autoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}
