<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Upload dir
    |--------------------------------------------------------------------------
    |
    | The dir where to store the images (relative from public)
    |
    */
    'dir' => ['uploads/files'],

    /*
    |--------------------------------------------------------------------------
    | Filesystem disks (Flysytem)
    |--------------------------------------------------------------------------
    |
    | Define an array of Filesystem disks, which use Flysystem.
    | You can set extra options, example:
    |
    | 'my-disk' => [
    |        'URL' => url('to/disk'),
    |        'alias' => 'Local storage',
    |    ]
    */
    'disks' => [
        'public' => [
            'glideURL' => '/glide',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */

    'route' => [
        'prefix' => 'elfinder'//,
        //'middleware' => 'replace-this-with-your-middleware', //Set to null to disable middleware filter
    ],

    /*
    |--------------------------------------------------------------------------
    | Access filter
    |--------------------------------------------------------------------------
    |
    | Filter callback to check the files
    |
    */

    'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

    /*
    |--------------------------------------------------------------------------
    | Roots
    |--------------------------------------------------------------------------
    |
    | By default, the roots file is LocalFileSystem, with the above public dir.
    | If you want custom options, you can set your own roots below.
    |
    */

    'roots' => [
        [
            'driver'        => 'LocalFileSystem',
            'path'          => 'uploads/files/',
            'accessControl' => 'Serverfireteam\Panel\libs\AppHelper::access',
            'URL'           => '/uploads/files',
            //'uploadAllow'   => array('image/png', 'image/jpeg', 'image/pjpeg', 'image/gif'),
            //'uploadDeny'    => array('all'),
            //'uploadOrder'   => array('deny', 'allow'),
            'acceptedName'  => 'Serverfireteam\Panel\libs\AppHelper::validName'
        ],
        [
            'driver' => 'FTP',
            'host'   => 'u330431.ftp.masterhost.ru',
            'user'   => 'u330431',
            'pass'   => 'spluriv2ri',
            'path'   => 'pamira.ru/pamira/public/uploads/files/'
        ]
        
    ],
                           

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | These options are merged, together with 'roots' and passed to the Connector.
    | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1
    |
    */

    'options' => array()
);
