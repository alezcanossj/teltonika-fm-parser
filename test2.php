<?php
/* Inicializa el servidor y comenzará a recibir datos del dispositivo, 
analizándolos y almacenándolos en la base de datos
*/
use Uro\TeltonikaFmParser\FmParser;
require 'vendor/autoload.php';
use Uro\TeltonikaFmParser\Entities\ImeiNumber;
use Uro\TeltonikaFmParser\Database\DataStore;
require __DIR__ . '/src/Database/DataStore.php';
require __DIR__.'/config.php.dist.php';
$dataBase = new DataStore();
$hex="00000000000004cb08120000018bc55ea8880000ae97a318b0f84b13b70120090086000d05ef01f0011504c800450106b5000cb600094236c7430fcb4400002b000002f10000539f10049ce08e000000018bc55ebc100000ae46b618b1002c13b80113080088000d05ef01f0011504c800450106b50014b6000b4236c5430fcb4400002b000002f10000539f10049ce13d000000018bc55ed3800000ade25218b0fa0d13bc0108080088000d05ef01f0011504c800450106b5000db6000b4236cc430fcb4400002b000002f10000539f10049ce214000000018bc55f0a300000acf92f18b0d43a13cb010d090083000d05ef01f0011504c800450106b5000bb600094236d1430fcb4400002b000002f10000539f10049ce413000000018bc55f25880000ac82d318b0de6213d30118080087000d05ef01f0011504c800450106b5000bb600094236cd430fcb4400002b000002f10000539f10049ce512000000018bc55f3cf80000ac1afc18b0f97713d80120080000f00d05ef01f0001505c800450106b5000db6000b42366d430fc64400002b000002f10000539f10049ce5fd000000018bc55fb2280000a9e92218b12a2a13e00117090080f00d05ef01f0011504c800450106b5000bb600084236c9430fcb4400002b000002f10000539f10049ceab3000000018bc55fc9980000a988d818b133ab13e4011609008a000d05ef01f0011504c800450106b5000bb6000842369c430fcb4400002b000002f10000539f10049ceb83000000018bc55fe4f00000a90d3618b1367713eb010c0b0088000d05ef01f0011504c800450106b50009b600074236cf430fcb4400002b000002f10000539f10049cec8b000000018bc5600fe80000a8492f18b137a313f9011608008a000d05ef01f0011504c800450106b5000db6000b42362f430fc74400002b000002f10000539f10049cee2f000000018bc5601f880000a8030018b145c413ff012006008a000d05ef01f0011500c800450106b5000fb6000d423695430fc74400002b000002f10000539f10049ceeca000000018bc5602b400000a7ce3418b1576914030125070095000d05ef01f0011500c800450106b5000fb6000d423673430fcb4400002b000002f10000539f10049cef45000000018bc56056380000a7067618b18bd2140c0118070099000d05ef01f0011500c800450106b50018b6000a4236df430fcb4400002b000002f10000539f10049cf111000000018bc56061f00000a6cce918b18ba0140d010e080098000d05ef01f0011500c800450106b5000bb600094236de430fcb4400002b000002f10000539f10049cf18c000000018bc56085180000a621c018b17771140d00fd070094000d05ef01f0011500c800450106b50018b6000b42378a430fc44400002b000002f10000539f10049cf301000000018bc560b7e00000a5397618b143e1140c010807008e000d05ef01f0011504c800450106b5000cb6000a423676430fcb4400002b000002f10000539910049cf508000000018bc560d7200000a4a8bc18b1434b140c0110080082000d05ef01f0011504c800450106b5000cb600094236a5430fc94400002b000002f10000539910049cf63d000000018bc560e6c00000a465ce18b150f7140b011e07007c000d05ef01f0011504c800450106b50015b6000e423635430fc84400002b000002f10000539910049cf6d400120000ff2d";
$geofenceCoordinatesArray = $dataBase->getGeofences();

$imeiNumber="350544502596904";
$hexDataGPS = $hex;
$archivo = fopen('log.txt', 'a');
 //We always get binary info so we decode it into HEX
 $data = bin2hex($data);

 //We get the first part of the data

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
    
echo "\n";


$numerOfElementsReceived = $packet->getAvlDataCollection()->getNumberOfData();
echo "Elements received: ".$numerOfElementsReceived."\n";
fwrite($archivo, "Numero de elementos recibido: ".$numerOfElementsReceived ."\n". PHP_EOL);
    
foreach ($AVLArray as $avlData) {
         echo "INICIO DATA". PHP_EOL;
         fwrite($archivo, "INICIO DATA" ."\n". PHP_EOL);
         $dataBase->storeDataFromDevice($avlData,$geofenceCoordinatesArray,$imeiNumber);
        /* $timestampInSeconds = $avlData->getTimestamp() / 1000;
     
         // Crear un objeto DateTime
         $date = new \DateTime();
         $date->setTimestamp($timestampInSeconds);
     
         // Formatear la fecha como una cadena legible
         $formattedDate = $date->format('Y-m-d H:i:s');
         echo "Timestamp: " . $formattedDate . PHP_EOL;
       
         echo "Priority: " . $avlData->getPriority() . PHP_EOL;
     
         // Obtener el objeto GpsElement
         $gpsElement = $avlData->getGpsElement();
         echo "GpsElement:" . PHP_EOL;
         echo "  Longitude: " . $gpsElement->getLongitude() . PHP_EOL;
         echo "  Latitude: " . $gpsElement->getLatitude() . PHP_EOL;
         echo "  Altitude: " . $gpsElement->getAltitude() . PHP_EOL;
         echo "  Angle: " . $gpsElement->getAngle() . PHP_EOL;
         echo "  Satellites: " . $gpsElement->getSatellites() . PHP_EOL;
         echo "  Speed: " . $gpsElement->getSpeed() . PHP_EOL;
     
         // Obtener el objeto IoElement
         $ioElement = $avlData->getIoElement();
         echo "IoElement:" . PHP_EOL;
         echo "  Event ID: " . $ioElement->getEventId() . PHP_EOL;
         echo "  Number of Elements: " . $ioElement->getNumberOfElements() . PHP_EOL;
     
         // Obtener la matriz de propiedades de IoElement
         $ioProperties = $ioElement->getProperties();
     
         echo "  Properties:" . PHP_EOL;
         foreach ($ioProperties as $property) {
             echo "    Property ID: " . $property->getId() . PHP_EOL;
             echo "    Property Value: " . hexdec($property->getValue()) . PHP_EOL;
         }*/
     
      
         echo "FIN DATA". PHP_EOL;
         fwrite($archivo, "FIN DATA" ."\n". PHP_EOL);
     }
     echo "Data saved into the database"."\n";
     fwrite($archivo, "Se guardo en la base de datos "."\n". PHP_EOL);
     //Send the response to server with the number of records we got (4 bytes integer)
   
   

     //$connection->write($numerOfElementsReceived);
     fclose($archivo);