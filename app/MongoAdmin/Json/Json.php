<?php namespace App\MongoAdmin\Json;

use MongoBinData;
use MongoRegex;
use MongoDate;
use DateTime;
use MongoId;

/**
 * @copy Genghis
 */
class Json
{
    public static function encode($object)
    {
        return json_encode(
            self::doEncode($object),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }

    public static function encodeReadable($object)
    {
        return json_encode(
            self::doEncodeReadable($object),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }

    private static function doEncode($object)
    {
        if (is_object($object) && $object instanceof JsonEncodable) {
            $object = $object->asJson();
        }

        if (is_object($object)) {
            switch (get_class($object)) {
                case 'MongoId':
                    return array(
                        '$type' => 'ObjectId',
                        '$value' => (string) $object
                    );

                case 'MongoDate':
                    $str = gmdate('Y-m-d\TH:i:s', $object->sec);
                    if ($object->usec) {
                        $str .= rtrim(sprintf('.%06d', $object->usec), '0');
                    }
                    $str .= 'Z';

                    return array(
                        '$type' => 'ISODate',
                        '$value'       => $str       // 2012-08-30T06:35:22.056Z
                    );

                case 'MongoRegex':
                    return array(
                        '$type' => 'RegExp',
                        '$value' => array(
                            '$pattern' => $object->regex,
                            '$flags'   => $object->flags ? $object->flags : null
                        )
                    );

                case 'MongoBinData':
                    return array(
                        '$type' => 'BinData',
                        '$value' => array(
                            '$subtype' => $object->type,
                            '$binary'  => base64_encode($object->bin),
                        )
                    );
            }

            // everything else is likely a StdClass...
            foreach ($object as $prop => $value) {
                $object->$prop = self::doEncode($value);
            }

        } elseif (is_array($object)) {
            // walk.
            foreach ($object as $key => $value) {
                $object[$key] = self::doEncode($value);
            }
        }

        return $object;
    }

    private static function doEncodeReadable($object)
    {
        if (is_object($object) && $object instanceof JsonEncodable) {
            $object = $object->asJson();
        }

        if (is_object($object)) {
            switch (get_class($object)) {
                case 'MongoId':
                    return '`{{ObjectId(' . (string) $object .')}}`';
                case 'MongoDate':
                    $str = gmdate('Y-m-d\TH:i:s', $object->sec);
                    if ($object->usec) {
                        $str .= rtrim(sprintf('.%06d', $object->usec), '0');
                    }
                    $str .= 'Z';
                    return "`{{ISODate($str)}}`";

                case 'MongoRegex':
                    return "`{{RegExp($object->regex,$object->flags ? $object->flags : null)}}`";

                case 'MongoBinData':
                    return "`{{BinData($object->type, base64_encode($object->bin))}}`";
            }

            // everything else is likely a StdClass...
            foreach ($object as $prop => $value) {
                $object->$prop = self::doEncodeReadable($value);
            }

        } elseif (is_array($object)) {
            // walk.
            foreach ($object as $key => $value) {
                $object[$key] = self::doEncodeReadable($value);
            }
        }

        return $object;
    }

    public static function decode($object)
    {
        if (is_string($object)) {
            $object = json_decode($object);

            if ($object === false) {
                throw new JsonException;
            }
        }

        return self::doDecode($object);
    }

    private static function doDecode($object)
    {
        if (is_object($object)) {
            if ($type = self::getProp($object, 'type')) {
                $value = self::getProp($object, 'value');
                switch ($type) {
                    case 'ObjectId':
                        return new MongoId($value);

                    case 'ISODate':
                        if ($value === null) {
                            return new MongoDate;
                        } else {
                            $date = new DateTime($value);

                            return new MongoDate($date->getTimestamp(), (int) $date->format('u'));
                        }

                    case 'RegExp':
                        $pattern = self::getProp($value, 'pattern');
                        $flags   = self::getProp($value, 'flags');

                        return new MongoRegex(sprintf('/%s/%s', $pattern, $flags));

                    case 'BinData':
                        $data = base64_decode(self::getProp($value, 'binary'));
                        $type = self::getProp($value, 'subtype');

                        return new MongoBinData($data, $type);
                }
            } else {
                foreach ($object as $prop => $value) {
                    $object->$prop = self::doDecode($value);
                }
            }
        } elseif (is_array($object)) {
            foreach ($object as $key => $value) {
                $object[$key] = self::doDecode($value);
            }
        }

        return $object;
    }

    private static function getProp($object, $name)
    {
        $name = sprintf('$%s', $name);

        return isset($object->$name) ? $object->$name : null;
    }
}
