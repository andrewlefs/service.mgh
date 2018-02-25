<?php

$config['account']['config'] = array(
    'max_account' => '6',
);

$config['account']['facebook'] = array(
    'config' => array(
        'appId' => '556438911107478',
        'secret' => '3a26098410074dcf22f365bcf83d4920'),
    'scope' => 'publish_stream,photo_upload,user_likes',
    
    'redirect' => 'http://service.8t.mobo.local/ajax/?control=user&func=facebook',
    'redirect_logout' => 'http://service.8t.mobo.local/account/logoutfacebook',
);