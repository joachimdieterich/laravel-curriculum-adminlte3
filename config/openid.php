<?php

return [
    'passport' => [
        /**
         * Place your Passport and OpenID Connect scopes here.
         * To receive an `id_token, you should at least provide the openid scope.
         */
        'tokens_can' => [
            'openid' => 'Enable OpenID Connect',
            // 'login' => 'See your login information',
        ],
    ],

    /**
     * Place your custom claim sets here.
     */
    'custom_claim_sets' => [
        // 'login' => [
        //     'last-login',
        // ],
        // 'company' => [
        //     'company_name',
        //     'company_address',
        //     'company_phone',
        //     'company_email',
        // ],
    ],

    /**
     * You can override the repositories below.
     */
    'repositories' => [
        'identity' => \OpenIDConnect\Repositories\IdentityRepository::class,
    ],

    'routes' => [
        /**
         * When set to true, this package will expose the OpenID Connect Discovery endpoint.
         *  - /.well-known/openid-configuration
         */
        'discovery' => true,
        /**
         * When set to true, this package will expose the JSON Web Key Set endpoint.
         */
        'jwks' => true,
         /**
          * Optional URL to change the JWKS path to align with your custom Passport routes.
          * Defaults to /oauth/jwks
          */
        'jwks_url' => '/oauth/jwks',
    ],

    /**
     * Settings for the discovery endpoint
     */
    'discovery' => [
        /**
        * Hide scopes that aren't from the OpenID Core spec from the Discovery,
        * default = false (all scopes are listed)
        */
        'hide_scopes' => false,
    ],

    /**
     * The signer to be used
     */
	'signer' => \Lcobucci\JWT\Signer\Rsa\Sha256::class,

    /**
     * Optional associative array that will be used to set headers on the JWT
     */
    'token_headers' => [],

    /**
     * By default, microseconds are included.
     */
    'use_microseconds' => true,

    /**
     * Value for the issuedBy params. By default: laravel to get the scheme and host from the $_SERVER variable.
     * Options: laravel (use Request to extract scheme and host), server (use $_SERVER to detect)
     * or another string that will be used as-is
     */
    'issuedBy' => 'laravel',
];
