<?php

return [
    'curl_config' => [
        CURLOPT_FAILONERROR => true,
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 5
    ]
];


