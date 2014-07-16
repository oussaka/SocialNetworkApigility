<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'di' => array(
        'services' => array(
            'Users\Model\UsersTable' => 'Users\Model\UsersTable',
            'Users\Model\UserStatusesTable' => 'Users\Model\UserStatusesTable',
            'Users\Model\UserLinksTable' => 'Users\Model\UserLinksTable',
            'Users\Model\UserImagesTable' => 'Users\Model\UserImagesTable',
        )
    ),
    'service_manager'=>array(
        'initializers' => array(
            function ($instance, $sm) {
                if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
                    $instance->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                }
            }
        ),
    ),
);