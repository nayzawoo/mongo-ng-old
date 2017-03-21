<?php namespace App\Libs\Controllers;

/**
* View Sharable
*/
trait ViewShareable
{
    public function share($data)
    {
        $data = is_array($data) ? $data : [$data];
        foreach ($data as $value) {
            view()->share(getVerName($value), $value);
        }
    }

    protected function getVerName( $v ) {
        $trace = debug_backtrace();
        $vLine = file( __FILE__ );
        $fLine = $vLine[ $trace[0]['line'] - 1 ];
        preg_match( "#\\$(\w+)#", $fLine, $match );
        return $match[1];
    }
}

?>