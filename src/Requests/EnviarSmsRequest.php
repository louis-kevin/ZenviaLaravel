<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 11:10
 */

namespace Louis\Zenvia\Requests;


use Louis\Zenvia\Resources\MessageResource;

class EnviarSmsRequest extends Request
{
    const URL = '/send-sms';
    /**
     * @param MessageResource $message
     * @throws \Louis\Zenvia\Exceptions\AuthenticationNotFoundedException
     * @throws \Louis\Zenvia\Exceptions\FieldMissingException
     * @throws \Louis\Zenvia\Exceptions\RequestException
     */
    public function send(MessageResource $message)
    {
        $this->post(self::URL, $message->getBodyRequest());
    }
}
