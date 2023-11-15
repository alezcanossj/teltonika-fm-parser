<?php
/**
 * Created by PhpStorm.
 * User: Alvaro
 * Date: 18/07/2018
 * Time: 13:23
 */

 namespace Uro\TeltonikaFmParser\Database;

use Medoo\Medoo;
//include("/../../config.php");

class Device
{
    private $dataBaseInstance;
    /**
     * DataStore constructor.
     * @param $dataBaseInstance
     */


    public function __construct($database)
    {
        $this->dataBaseInstance = $database;
    }

    public function checkIfImeiExists($imei)
    {
        $result = $this->dataBaseInstance->has("devices", ["imei" => $imei]);
        return $result;
    }

    public function saveDevice($imei, $phoneNumber, $millage)
    {
        $this->dataBaseInstance->insert("devices", [
            "imei" => $imei,
            "phone_number" => $phoneNumber,
            "millage" => $millage
        ]);
    }

}