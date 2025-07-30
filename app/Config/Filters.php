<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

use App\Filters\AuthAdminFilter; 

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'authAdmin'     => AuthAdminFilter::class, 
    ];

    public array $required = [
        'before' => [
            // 'forcehttps', // Aktifkan jika aplikasi Anda menggunakan HTTPS
            // 'pagecache',
        ],
        'after' => [
            // 'pagecache',
            // 'performance',
            'toolbar',     // Debug Toolbar
        ],
    ];

    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf',        // Penting: Aktifkan CSRF protection untuk semua form POST
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    public array $methods = [];

    public array $filters = [
        'authAdmin' => [ // Terapkan filter autentikasi admin
            'before' => [
                'admin/*', // Ini akan melindungi semua URL yang dimulai dengan '/admin/'
            ],
        ],
    ];
}
