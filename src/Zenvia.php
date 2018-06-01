<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:28
 */

namespace Louis\Zenvia\Services;


use Louis\Zenvia\Exceptions\AuthenticationNotFoundedException;
use Louis\Zenvia\Exceptions\FieldMissingException;
use Louis\Zenvia\Collections\NumberCollection;
use Louis\Zenvia\Requests\EnviarSmsRequest;
use Louis\Zenvia\Resources\AuthenticationResource;
use Louis\Zenvia\Resources\FromResource;
use Louis\Zenvia\Resources\MessageResource;
use Louis\Zenvia\Resources\NumberResource;
use Louis\Zenvia\Resources\TextResource;
use Louis\Zenvia\Resources\TitleResource;
use Illuminate\Support\Collection;

class Zenvia
{
    /**
     * @var NumberCollection
     */
    private $numbers;
    /**
     * @var TitleResource
     */
    private $title;
    /**
     * @var TextResource
     */
    private $text;
    /**
     * @var MessageResource
     */
    private $message;
    /**
     * @var AuthenticationResource
     */
    private $authentication;
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $from;

    /**
     * ZenviaService constructor.
     * @param $account
     * @param $password
     * @throws AuthenticationNotFoundedException
     */
    public function __construct($account, $password)
    {
        $this->authentication = new AuthenticationResource($account, $password);

        $this->from = new FromResource(config('zenvia.from', 'Sistema'));
    }

    /**
     * @param string|string[]|NumberResource|NumberResource[] $numbers
     * @throws $this
     * @throws \Louis\Zenvia\Exceptions\FieldMissingException
     */
    public function setNumber($numbers)
    {
        $this->numbers = new NumberCollection();

        if(!is_array($numbers) && !$numbers instanceof Collection){
            $numbers = (array) $numbers;
        }

        foreach($numbers as $number){
            $this->numbers->addNumber($number instanceof NumberResource ? $number : new NumberResource($number));
        }

        return $this;
    }

    /**
     * @param mixed $message
     * @return $this
     * @throws \Louis\Zenvia\Exceptions\FieldMissingException
     */
    public function setText(string $text)
    {
        $this->text = new TextResource($text);

        return $this;
    }

    /**
     * @throws FieldMissingException
     */
    public function getMessage()
    {
        if(!$this->text){
            throw new FieldMissingException('Texto não pode ser vazio');
        }

        if(!$this->numbers->isEmpty()){
            throw new FieldMissingException('Número não pode ser vazio');
        }

        $this->message = new MessageResource($this->from, $this->numbers, $this->text);
    }

    /**
     * @throws AuthenticationNotFoundedException
     * @throws \Louis\Zenvia\Exceptions\FieldMissingException
     * @throws \Louis\Zenvia\Exceptions\RequestException
     */
    public function send()
    {
        $request = new EnviarSmsRequest($this->authentication->getKey());
        $this->message = $this->getMessage();

        $response = $request->send($this->message);

    }

    /**
     * @param string|string[]|NumberResource|NumberResource[] $numbers
     * @param string $text
     * @throws AuthenticationNotFoundedException
     * @throws FieldMissingException
     * @throws \Louis\Zenvia\Exceptions\RequestException
     */
    public function sendMessage($numbers, $text){
        $this->setNumber($numbers)->setText($text)->send();
    }
}
