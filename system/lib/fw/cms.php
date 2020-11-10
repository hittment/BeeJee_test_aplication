<?php

namespace fw;

class cms
{

    public $conf = [
        'db' => [
            'host' => 'localhost',
            'user' => 'hittment',
            'base' => 'hittment',
            'pass' => 'Temp_password123'
        ],
        'site' => [
            'name' => 'test',
            'title' => 'test',
            'keywords' => 'test',
            'description' => 'test',
            'tpl' => '/app/templates/'
        ]
    ];

    public function config($str1 = null, $str2 = null)
    {
        if ($str1 != null && $str2 != null) {
            return $this->conf[$str1][$str2];
        } elseif ($str1 != null) {
            return $this->conf[$str1];
        } else {
            return '';
        }
    }

    public function guard($str)
    {
        $str = strip_tags($str);
        $str = trim($str);
        $str = htmlspecialchars($str);
        $str = htmlentities($str);

        return $str;
    }

    public function accessSecure($user, $level = 0)
    {
        if (!$user && URL == '/login' && URL == '/registration') {
            header('location: /login');
            exit();
        } elseif ($user && $level == 0) {
            header('location: /');
            exit();
        } elseif ($level != 0 && $user['level'] < $level) {
            header('location: /');
            exit();
        }
        return '';
    }
}
