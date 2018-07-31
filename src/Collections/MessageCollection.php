<?php
/**
 * Created by PhpStorm.
 * User: devmaker
 * Date: 18/07/18
 * Time: 18:36
 */

namespace Louis\Zenvia\Collections;


use Louis\Zenvia\Resources\MessageResource;

class MessageCollection
{
    private $messages;

    public function __construct($messages = [])
    {
        $this->messages = collect($messages);
    }

    public function add(MessageResource $messageResource)
    {
        $this->messages[] = $messageResource;
    }

    public function get()
    {
        return $this->messages->all();
    }
}