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
    const URLMULTI = '/send-sms-multiple';
    /**
     * @param MessageResource $message
     * @throws \Louis\Zenvia\Exceptions\AuthenticationNotFoundedException
     * @throws \Louis\Zenvia\Exceptions\FieldMissingException
     * @throws \Louis\Zenvia\Exceptions\RequestException
     */
    public function send(MessageResource $message)
    {
        return $this->post($this->getEndpoint($message), $message->getBodyRequest());
    }

    public function getEndpoint(MessageResource $messageResource)
    {
        return $messageResource->isMultiMessage() ? self::URLMULTI : self::URL;
    }
}
