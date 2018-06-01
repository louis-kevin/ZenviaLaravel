<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 30/05/2018
 * Time: 21:54
 */

namespace Louis\Zenvia\Exceptions;


use Illuminate\Http\Response;

class FieldMissingException extends \Exception
{
    public function __construct($field)
    {
        parent::__construct('Campo '.$field.' é requerido', Response::HTTP_BAD_REQUEST);
    }
}
