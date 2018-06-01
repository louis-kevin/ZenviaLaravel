<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 30/05/2018
 * Time: 21:16
 */

namespace Louisk\Zenvia\Exceptions;


use Illuminate\Http\Response;
use Throwable;

class AuthenticationNotFoundedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Autenticação não encontrada, verifique as variaveis no .env', Response::HTTP_UNAUTHORIZED);
    }
}
