#!/bin/bash

if [ -z "$ADAPTER" ]; then
    echo "Adapter no set. Please set 'mysql' or 'mongodb' to environment variable ADAPTER";
    exit 1
fi

cd /var/www/proophessor-do

touch ./config/event_store.local.php

composer update -o

if [ "$ADAPTER" == "mysql" ]; then
    echo "using adapter: $ADAPTER"
    touch ./config/dbal_connection.local.php

    composer require prooph/event-store-doctrine-adapter

    cat > config/dbal_connection.local.php << EOL
<?php
return [
    'doctrine' => [
        'connection' => [
            'default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'host' => 'mariadb',
                'port' => '3306',
                'user' => 'dev',
                'password' => 'dev',
                'dbname' => 'proophessor',
                'charset' => 'utf8',
                'driverOptions' => [
                    1002 => "SET NAMES 'UTF8'"
                ],
            ],
        ],
    ],
 ];
EOL

    cat > config/event_store.local.php << EOL
<?php
return [
    'prooph' => [
        'event_store' => [
            'adapter' => [
                'type' => 'Prooph\\EventStore\\Adapter\\Doctrine\\DoctrineEventStoreAdapter',
                'options' => [
                    'connection_alias' => 'doctrine.connection.default',
                ],
            ],
        ]
    ],
 ];
EOL
fi

if [ "$ADAPTER" == "mongodb" ]; then
    echo "using adapter: $ADAPTER"
    touch ./config/mongo_client.local.php

    composer require prooph/event-store-mongodb-adapter

    cat > config/mongo_client.local.php << EOL
<?php
return [
        'mongo_client' => function () {
            //Change set up of the mongo client, if you need to configure connection settings
            return new \MongoClient();
        },
    ];
EOL

    cat > config/event_store.local.php << EOL
<?php
return [
    'prooph' => [
        'event_store' => [
            'adapter' => [
                'type' => 'Prooph\\EventStore\\Adapter\\MongoDb\\MongoDbEventStoreAdapter',
                'options' => [
                    'db_name' => 'proophessor',
                    'mongo_connection_alias' => 'mongo_client',
               ],
            ],
        ]
    ],
 ];
EOL
fi

php bin/migrations.php migrations:migrate
