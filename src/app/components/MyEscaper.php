<?php
// helper class for escaper
namespace App\Components;

use Phalcon\Escaper;

class MyEscaper
{

    /**
     * sanitize($input)
     * take form input return it after escapeHtml operation
     *
     * @param [type] $input
     * @return void
     */
    public function sanitize($input)
    {
        $escaper = new Escaper();
        $arr =  $input;
        $input = $escaper->escapeHtml($arr);
        return $input;
    }
}
