<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Database settings
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'absensi',
            'username' => 'root',
            'password' => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'abs_',
        ],

        'determineRouteBeforeAppMiddleware' => true,

        'baseUrl'        => "http://" . $_SERVER['HTTP_HOST'] . preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])) . "/",

        'dataStatic' => [
          'religion' => [
            [
              'key' => 'islam',
              'value' => 'Islam',
            ],
            [
              'key' => 'kristen',
              'value' => 'Kristen',
            ],
            [
              'key' => 'protestan',
              'value' => 'Protestan',
            ],
            [
              'key' => 'budha',
              'value' => 'Budha',
            ],
            [
              'key' => 'hindu',
              'value' => 'Hindu',
            ],
          ],
          'gender' => [
            [
              'key' => 'pria',
              'value' => 'Pria',
            ],
            [
              'key' => 'wanita',
              'value' => 'Wanita',
            ],
          ],
          'statusMarried' => [
            [
              'key' => 'belum_menikah',
              'value' => 'Belum Menikah',
            ],
            [
              'key' => 'sudah_menikah',
              'value' => 'Sudah Menikah',
            ],
            [
              'key' => 'duda',
              'value' => 'Duda',
            ],
            [
              'key' => 'janda',
              'value' => 'Janda',
            ],
          ],
          'statusActived' => [
            [
              'key' => 1,
              'value' => 'Aktif',
            ],
          ],
          'listMonth' => [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
          ],
          'listYear' => [
            '2017' => 2017,
            '2018' => 2018,
            '2019' => 2019,
            '2020' => 2020,
            '2021' => 2021,
          ],
          'listModulePrivilege' => [
            1 => 'Module Master Bagian',
            2 => 'Module Master Unit',
            3 => 'Module Master Jabatan',
            4 => 'Module Master Status',
            5 => 'Module Master Employee',
            6 => 'Module Master Setting',
            7 => 'Module Master User Admin',
            8 => 'Module Master Role Access',
            9 => 'Module Attendance Holiday',
            10 => 'Module Attendance Izin',
            11 => 'Module Attendance Overtime',
            12 => 'Module Attendance Schedule',
            13 => 'Module Proses Absensi Pengaturan Jadwal',
            14 => 'Module Proses Absensi Penyesuaian Jadwal',
            15 => 'Module Report Cuti',
            16 => 'Module Report Kehadiran',
            17 => 'Only Bagian',
            18 => 'Only Unit',
            19 => 'Report Form',
          ],
        ],
    ],
];
