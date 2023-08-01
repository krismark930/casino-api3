<?php

namespace App\Utils\FTP;

use phpseclib3\Net\SFTP as SFTP;

class FTPUtils
{

    public function __construct()
    {
    }
    
    public function getAllDirectories()
    {
        $host = env('FTP_URL');
        $username = env('FTP_USER_NAME');
        $password = env('FTP_PASSWORD');

        $sftp = new SFTP($host);

        if (!$sftp->login($username, $password)) {
            throw new \Exception('FTP login failed');
        }

        $directories = $sftp->nlist();

        return $directories;
    }
}
