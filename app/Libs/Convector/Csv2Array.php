<?php

namespace App\Libs\Convector;

/**
 * Created by Nay Zaw Oo<nayzawoo.me@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM.
 */
class Csv2Array
{
    public static function convert($path)
    {
        $output = [];
        $row = 0;
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                for ($i = 0;$i < count($data);$i++) {
                    $output[$row][] = $data[$i];
                }
                $row++;
            }
            fclose($handle);
        }

        return $output;
    }
}
