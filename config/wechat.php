<?php

return [
    /**
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug'  => true,

    /**
     * 使用 Laravel 的缓存系统
     */
    'use_laravel_cache' => true,

    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    //'app_id'  => env('WECHAT_APPID', 'wx564b0f968fb2e7b0'),         // AppID
    //'secret'  => env('WECHAT_SECRET', 'b86098aac94590c8f113dac8d24c0623'),     // AppSecret
    //'token'   => env('WECHAT_TOKEN', 'weixin'),          // Token
    //'aes_key' => env('WECHAT_AES_KEY', '5dsEN3K49elrqIH9ngJuruPPOMenHd9vyrhHSAc6Jdu'),                    // EncodingAESKey

	'app_id'  => env('WECHAT_APPID', 'wx1a6d72cb63c2e01b'),         // AppID
	'secret'  => env('WECHAT_SECRET', '15fa82877812dfbcf1543e4c40cf97e3'),     // AppSecret
	'token'   => env('WECHAT_TOKEN', 'riventest'),          // Token
	'aes_key' => env('WECHAT_AES_KEY', '5dsEN3K49elrqIH9ngJuruPPOMenHd9vyrhHSAc6Jdu'),
	
    /**
     * 日志配置
     *
     * level: 日志级别，可选为：
     *                 debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level' => env('WECHAT_LOG_LEVEL', 'debug'),
        'file'  => env('WECHAT_LOG_FILE', storage_path('logs/easywechat/easywechat_'.date('Ymd').'.log')),
    ],

    /**
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
     */
    // 'oauth' => [
    //     'scopes'   => array_map('trim', explode(',', env('WECHAT_OAUTH_SCOPES', 'snsapi_userinfo'))),
    //     'callback' => env('WECHAT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
    // ],

    /**
     * 微信支付
     */
    'payment' => [
        'merchant_id'        => env('WECHAT_PAYMENT_MERCHANT_ID', '1334760401'),
        'key'                => env('WECHAT_PAYMENT_KEY', 'herunwanqingherunwanqingherunwan'),
        'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', config_path('wechatcert/apiclient_cert.pem')), // XXX: 绝对路径！！！！
        'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', config_path('wechatcert/apiclient_key.pem')),      // XXX: 绝对路径！！！！
        'notify_url'           => env('WECHAT_NOTIFY_URL', 'http://m.hrwq.com/wechat/notify'),
    //     // 'device_info'     => env('WECHAT_PAYMENT_DEVICE_INFO', ''),
    //     // 'sub_app_id'      => env('WECHAT_PAYMENT_SUB_APP_ID', ''),
    //     // 'sub_merchant_id' => env('WECHAT_PAYMENT_SUB_MERCHANT_ID', ''),
    //     // ...
    ],
];
