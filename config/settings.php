<?php

return [
    'appUrl' => [
        'ios' => env('IOS_APP_URL', ''),
        'android' => env('ANDROID_APP_URL', ''),
    ],
    'chatWork' => [
        'url' => 'https://lyl9xfgve5.execute-api.ap-southeast-1.amazonaws.com/test-luc83-g2cw?room_id=',
        'errorRoomId' => '257994701'
    ],
    'chatwork' => [
        'url'   => env('CHATWORK_URL', ''),
        'token' => env('CHATWORK_TOKEN', ''),
        'room'  => env('CHATWORK_ROOM', ''),
    ],
    'chunkDataSize' => env('CHUNK_DATA_SIZE', 1000),
    'countThreshold' => env('COUNT_THRESHOLD', 1000),
    'email' => [
        'deeplink_domain' => env('DEEP_LINK', 'hananohi-app://'),
    ],
    'maxKbImageUpload' => env('MAX_KB_IMAGE_UPLOAD', 1024),
    'maxKbOriginalImageUpload' => env('MAX_KB_ORIGINAL_IMAGE_UPLOAD', 1024*20),
    /*
     * NULL Ip to enter to match database schema
     */
    'nullIpAddress' => env('NULL_IP_ADDRESS', '0.0.0.0'),
    'pushNotificationMode' => env('PUSH_NOTIFICATION_MODE', 'app'),
    's3PublicDomain' => env('S3_PUBLIC_DOMAIN'),
    'storageBasePath' => [
        'postsImage' => 'posts',
        'avatarImage' => 'avatars',
        'coverImage' => 'covers'
    ],
    'web_url' => env('WEB_URL'),
    'webViewUrl' => [
        'pictureBook' => env('WEB_URL') . '/web-view/picture-book'
    ]
];
