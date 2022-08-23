<?php
namespace App\Services;

class ConfigReceiver{
    private const configPath = __DIR__ . '/../../config/config.json';

    static public function  getDbConfig(){
        $config = json_decode(file_get_contents(self::configPath));
        return $config->database;
    }
}