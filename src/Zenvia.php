<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:28
 */

namespace Zenvia\Services;


use Zenvia\Exceptions\AuthenticationNotFoundedException;
use Zenvia\Exceptions\FieldMissingException;
use Zenvia\Collections\NumberCollection;
use Zenvia\Requests\EnviarSmsRequest;
use Zenvia\Resources\AuthenticationResource;
use Zenvia\Resources\MessageResource;
use Zenvia\Resources\NumberResource;
use Zenvia\Resources\TextResource;
use Zenvia\Resources\TitleResource;
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

        $this->from = config('zenvia.from', 'Sistema');
    }

    /**
     * @param string|string[]|NumberResource|NumberResource[] $numbers
     * @return Zenvia
     * @throws \Zenvia\Exceptions\FieldMissingException
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
     * @param $title
     * @return Zenvia
     * @throws \Zenvia\Exceptions\FieldMissingException
     */
    public function setTitle(string $title)
    {
        $this->title = new TitleResource($title);

        return $this;
    }

    /**
     * @param mixed $message
     * @return Zenvia
     * @throws \Zenvia\Exceptions\FieldMissingException
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

        if(!$this->title){
            throw new FieldMissingException('Titulo não pode ser vazio');
        }

        if(!$this->numbers->isEmpty()){
            throw new FieldMissingException('Número não pode ser vazio');
        }

        $this->message = new MessageResource($this->from, $this->title, $this->numbers, $this->text);
    }

    /**
     * @throws AuthenticationNotFoundedException
     * @throws \Zenvia\Exceptions\FieldMissingException
     * @throws \Zenvia\Exceptions\RequestException
     */
    public function send()
    {
        $request = new EnviarSmsRequest($this->authentication->getKey());
        $this->message = new MessageResource($this->from, $this->title, $this->numbers, $this->text);

        $response = $request->send($this->message);

    }
}
