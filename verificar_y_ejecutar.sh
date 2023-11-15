#!/bin/bash

PUERTO=21328;
IP_SERVIDOR=88.198.45.251
LOG_FILE=/home/locchase/repositories/teltonika-fm-parser/log_bash.log
FECHA_HORA=$(date +"%Y-%m-%d %H:%M:%S")
 echo "$FECHA_HORA:Ejecutando Archivo" >> $LOG_FILE
# Verificar si el puerto está en uso
if nc -z -w1 $IP_SERVIDOR $PUERTO; then
     echo "$FECHA_HORA: El puerto $PUERTO está en uso." >> $LOG_FILE
else
    echo "$FECHA_HORA: El puerto $PUERTO no está en uso. Ejecutando archivo PHP." >> $LOG_FILE
    php /home/locchase/repositories/teltonika-fm-parser/decode.php
fi