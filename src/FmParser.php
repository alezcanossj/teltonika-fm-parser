<?php

declare(strict_types=1);

namespace Uro\TeltonikaFmParser;

use Uro\TeltonikaFmParser\Model\Imei;
use Uro\TeltonikaFmParser\Protocol\Tcp\Packet;
use Uro\TeltonikaFmParser\Support\Acknowledgeable;

class FmParser
{
    private DecoderInterface $decoder;

    private EncoderInterface $encoder;

    public function __construct(string $protocol)
    {
        $namespace = 'Uro\\TeltonikaFmParser\\Protocol\\' . ucfirst($protocol) . '\\';

        // Cargar dinámicamente la clase Decoder
        $decoderClassName = $namespace . 'Decoder';
        if (!class_exists($decoderClassName)) {
            throw new \RuntimeException("Class $decoderClassName not found");
        }
        $this->decoder = new $decoderClassName;

        // Cargar dinámicamente la clase Encoder
        $encoderClassName = $namespace . 'Encoder';
        if (!class_exists($encoderClassName)) {
            throw new \RuntimeException("Class $encoderClassName not found");
        }
        $this->encoder = new $encoderClassName;
    }


    public function decodeImei(string $data): Imei
    {
        return $this->decoder->decodeImei($data);
    }

    public function decodeData(string $data): Packet
    {
        return $this->decoder->decodeData($data);
    }

    public function encodeAcknowledge(Acknowledgeable $acknowledgeable): string
    {
        return $this->encoder->encodeAcknowledge($acknowledgeable);
    }
}
