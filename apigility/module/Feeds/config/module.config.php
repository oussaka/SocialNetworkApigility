<?php
return array(
    'router' => array(
        'routes' => array(
            'feeds.rest.feeds' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/feeds[/:username]',
                    'defaults' => array(
                        'controller' => 'Feeds\\V1\\Rest\\Feeds\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'feeds-process' => array(
                    'options' => array(
                        'route' => 'feeds process [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'Feeds\Controller\Cli',
                            'action'     => 'processFeeds'
                        )
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Feeds\Controller\Cli' => 'Feeds\Controller\CliController',
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'feeds.rest.feeds',
            1 => 'feeds.rest.feeds',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Feeds\\V1\\Rest\\Feeds\\FeedsResource' => 'Feeds\\V1\\Rest\\Feeds\\FeedsResourceFactory',
        ),
    ),
    'zf-rest' => array(
        'Feeds\\V1\\Rest\\Feeds\\Controller' => array(
            'listener' => 'Feeds\\V1\\Rest\\Feeds\\FeedsResource',
            'route_name' => 'feeds.rest.feeds',
            'route_identifier_name' => 'username',
            'collection_name' => 'feeds',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
                2 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => '10',
            'page_size_param' => null,
            'entity_class' => 'Feeds\\V1\\Rest\\Feeds\\FeedsEntity',
            'collection_class' => 'Feeds\\V1\\Rest\\Feeds\\FeedsCollection',
            'service_name' => 'feeds',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Feeds\\V1\\Rest\\Feeds\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Feeds\\V1\\Rest\\Feeds\\Controller' => array(
                0 => 'application/vnd.feeds.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Feeds\\V1\\Rest\\Feeds\\Controller' => array(
                0 => 'application/vnd.feeds.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Feeds\\V1\\Rest\\Feeds\\FeedsEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'feeds.rest.feeds',
                'route_identifier_name' => 'username',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Feeds\\V1\\Rest\\Feeds\\FeedsCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'feeds.rest.feeds',
                'route_identifier_name' => 'username',
                'is_collection' => true,
            ),
        ),
    ),
);
