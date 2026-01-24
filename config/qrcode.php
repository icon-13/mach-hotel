<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QR Code Backend
    |--------------------------------------------------------------------------
    | Use GD instead of Imagick (works on Windows without extensions)
    |
    */

    'renderer' => [
        'backend' => 'gd',
    ],

];
