<?php
return array(
    'router' => array(
        'routes' => array(
            'wall.rest.wall' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/wall[/:id]',
                    'defaults' => array(
                        'controller' => 'Wall\\V1\\Rest\\Wall\\Controller',
                    ),
                ),
            ),
            'wall.rest.users' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/users[/:username]',
                    'defaults' => array(
                        'controller' => 'Wall\\V1\\Rest\\Users\\Controller',
                    ),
                ),
            ),
            'wall.rest.login' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/users/login',
                    'defaults' => array(
                        'controller' => 'Wall\\V1\\Rest\\Login\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'wall.rest.wall',
            1 => 'wall.rest.users',
            2 => 'wall.rest.login',
        ),
        'default_version' => 1,
    ),
    'service_manager' => array(
        'factories' => array(
            'Wall\\V1\\Rest\\Wall\\WallResource' => 'Wall\\V1\\Rest\\Wall\\WallResourceFactory',
            'Wall\\V1\\Rest\\Users\\UsersResource' => 'Wall\\V1\\Rest\\Users\\UsersResourceFactory',
            'Wall\\V1\\Rest\\Login\\LoginResource' => 'Wall\\V1\\Rest\\Login\\LoginResourceFactory',
        ),
        'invokables' => array(
            'Wall\\V1\\WallService' => 'Wall\\V1\\Rest\\Wall\\WallService',
        ),
    ),
    'zf-rest' => array(
        'Wall\\V1\\Rest\\Wall\\Controller' => array(
            'listener' => 'Wall\\V1\\Rest\\Wall\\WallResource',
            'route_name' => 'wall.rest.wall',
            'route_identifier_name' => 'id',
            'collection_name' => 'wall',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_http_methods' => array(),
            'collection_query_whitelist' => array(),
            'page_size' => '10',
            'page_size_param' => null,
            'entity_class' => 'Wall\\V1\\Rest\\Wall\\WallEntity',
            'collection_class' => 'Wall\\V1\\Rest\\Wall\\WallCollection',
            'service_name' => 'wall',
        ),
        'Wall\\V1\\Rest\\Users\\Controller' => array(
            'listener' => 'Wall\\V1\\Rest\\Users\\UsersResource',
            'route_name' => 'wall.rest.users',
            'route_identifier_name' => 'username',
            'collection_name' => 'users',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => '10',
            'page_size_param' => null,
            'entity_class' => 'Wall\\V1\\Rest\\Users\\UsersEntity',
            'collection_class' => 'Wall\\V1\\Rest\\Users\\UsersCollection',
            'service_name' => 'users',
        ),
        'Wall\\V1\\Rest\\Login\\Controller' => array(
            'listener' => 'Wall\\V1\\Rest\\Login\\LoginResource',
            'route_name' => 'wall.rest.login',
            'route_identifier_name' => 'id',
            'collection_name' => 'login',
            'entity_http_methods' => array(
                0 => 'POST',
            ),
            'collection_http_methods' => array(
                0 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Wall\\V1\\Rest\\Login\\LoginEntity',
            'collection_class' => 'Wall\\V1\\Rest\\Login\\LoginCollection',
            'service_name' => 'Login',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Wall\\V1\\Rest\\Wall\\Controller' => 'HalJson',
            'Wall\\V1\\Rest\\Users\\Controller' => 'HalJson',
            'Wall\\V1\\Rest\\Login\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Wall\\V1\\Rest\\Wall\\Controller' => array(
                0 => 'application/vnd.wall.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Wall\\V1\\Rest\\Users\\Controller' => array(
                0 => 'application/vnd.wall.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Wall\\V1\\Rest\\Login\\Controller' => array(
                0 => 'application/vnd.wall.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Wall\\V1\\Rest\\Wall\\Controller' => array(
                0 => 'application/vnd.wall.v1+json',
                1 => 'application/json',
            ),
            'Wall\\V1\\Rest\\Users\\Controller' => array(
                0 => 'application/vnd.wall.v1+json',
                1 => 'application/json',
            ),
            'Wall\\V1\\Rest\\Login\\Controller' => array(
                0 => 'application/vnd.wall.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Wall\\V1\\Rest\\Wall\\WallEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'wall.rest.wall',
                'route_identifier_name' => 'id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ObjectProperty',
            ),
            'Wall\\V1\\Rest\\Wall\\WallCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'wall.rest.wall',
                'route_identifier_name' => 'id',
                'is_collection' => true,
            ),
            'Wall\\V1\\Rest\\Users\\UsersEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'wall.rest.users',
                'route_identifier_name' => 'username',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Wall\\V1\\Rest\\Users\\UsersCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'wall.rest.users',
                'route_identifier_name' => 'username',
                'is_collection' => true,
            ),
            'Wall\\V1\\Rest\\Login\\LoginEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'wall.rest.login',
                'route_identifier_name' => 'id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Wall\\V1\\Rest\\Login\\LoginCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'wall.rest.login',
                'route_identifier_name' => 'id',
                'is_collection' => true,
            ),
        ),
    ),
);
