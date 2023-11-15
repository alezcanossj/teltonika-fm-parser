<?php
/**
 * Created by PhpStorm.
 * User: Alvaro
 * Date: 16/07/2018
 * Time: 13:23
 */

namespace Uro\TeltonikaFmParser\Server;
use Uro\TeltonikaFmParser\FmParser;
require 'vendor/autoload.php';
use Uro\TeltonikaFmParser\Entities\ImeiNumber;
use React\Socket\ConnectionInterface;
use Uro\TeltonikaFmParser\Database\DataStore;

class SocketServer
{

    private $host;
    private $port;
    private $dataBase;

    /**
     * SocketServer constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->dataBase = new DataStore();
    }

    public function runServer() {

        $loop = \React\EventLoop\Factory::create();

        //Creation of new TCP socket
        $socket = new \React\Socket\Server($this->host.":".$this->port, $loop);

        $socket->on('connection', function(ConnectionInterface $connection){
        $geofenceCoordinatesArray = $this->dataBase->getGeofences();
   
        $hexDataGPS = "";
        //We store the imei to store with the data
        $imei = "";
        $imeiNumber="";
            //We set a react event for every time we get data on our socket.
            $connection->on('data', function($data) use ($connection, &$hexDataGPS, &$imei, &$geofenceCoordinatesArray,&$imeiNumber){

                //If we get a 17 characters string it means we are getting IMEI number, we have to decode it, check IMEI and send confirmation
                if(strlen($data) == 17) {

                    //We always get binary info so we decode it into HEX
                    $data = bin2hex($data);

                    //DECODE IMEI
                    $imei = new ImeiNumber($data);
                    $imeiNumber = $imei->getImeiNumber();

                    //SEND CONFIRMATION
                    $connection->write("\x01"); //(Binary packet => 01)
                }

                else {

                    //We always get binary info so we decode it into HEX
                    $data = bin2hex($data);

                    //We get the first part of the data
                    if(strlen($data) == 20) {
                        $hexDataGPS .= $data;
                    }else {
                        $hexDataGPS .= $data;
                        $archivo = fopen('log.txt', 'a');

                        $informacion_recibida = $hexDataGPS;
                        echo "Got a complete AVLMessage:\n";
                        echo $hexDataGPS;
                        echo "\n";
                        // Escribir la información en el archivo
                        fwrite($archivo, "Hexadecimal: ".$informacion_recibida."\n" . PHP_EOL);

                        $binaryData = hex2bin($hexDataGPS);
                        $parser = new FmParser('tcp');
                        // Decodificar datos del paquete
                        $packet = $parser->decodeData($binaryData);
                        $AVLArray = $packet->getAvlDataCollection()->getAvlData();
                        //We decode the message 
                       // $decoder = new TeltonikaDecoderImp($hexDataGPS, $imei);
                       // $AVLArray = $decoder->getArrayOfAllData();

                        //Show output
                        echo json_encode($AVLArray);
                        echo "\n";
                        fwrite($archivo, "Array Formateado: ".json_encode($AVLArray) ."\n". PHP_EOL);

                        $numerOfElementsReceived = $packet->getAvlDataCollection()->getNumberOfData();
                        echo "Elements received: ".$numerOfElementsReceived."\n";
                        fwrite($archivo, "Numero de elementos recibido: ".$numerOfElementsReceived ."\n". PHP_EOL);
                       
                        foreach ($AVLArray as $avlData) {
                            echo "INICIO DATA". PHP_EOL;
                            fwrite($archivo, "INICIO DATA" ."\n". PHP_EOL);
                            $this->dataBase->storeDataFromDevice($avlData,$geofenceCoordinatesArray,$imeiNumber);

                        
                         
                            echo "FIN DATA". PHP_EOL;
                            fwrite($archivo, "FIN DATA" ."\n". PHP_EOL);
                        }
                        echo "Data saved into the database"."\n";
                        fwrite($archivo, "Se guardo en la base de datos: "."\n". PHP_EOL);
                        //Send the response to server with the number of records we got (4 bytes integer)
                      
                        $connection->write(pack('N', $numerOfElementsReceived));

                        //$connection->write($numerOfElementsReceived);
                        fclose($archivo);
                    }
                }
            });
        });
        echo "Listening on {$socket->getAddress()}\n";
        $loop->run();

    }

}