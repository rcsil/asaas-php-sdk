<?php

return [
    /**
     * Asaas API Environment
     * 
     * Accepted values: "production" or "sandbox".
     * The selected environment defines the URL used by the SDK.
     */
    'environment' => 'production',

    /**
     * Asaas API URLs
     * 
     * Official URLs for communicating with the Asaas API.
     * These URLs may change if Asaas modifies its structure.
     */
    'base_urls' => [
        'production' => 'https://api.asaas.com',
        'sandbox'    => 'https://api-sandbox.asaas.com'
    ],

    /**
     * API Version
     * 
     * If new versions of the API are released in the future, you can 
     * modify them here without altering other parts of the SDK.
     */
    'api_version' => 'v3',
];