<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 30/05/2018
 * Time: 21:56
 */

namespace Louis\Zenvia\Exceptions;


use Throwable;

class RequestException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        if($code >= 500){
            $message = 'Erro API Zenvia';
        }
        parent::__construct($message, $code, $previous);
    }
}
