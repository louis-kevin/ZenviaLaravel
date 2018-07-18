<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:28
 */

namespace Louis\Zenvia\Services;


use Louis\Zenvia\Collections\MessageCollection;
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
    const LOG_INFO = 'info';
    const LOG_ERROR = 'error';
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
     * @var MessageCollection
     */
    private $messages;
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

        $this->from = new FromResource(config('zenvia.from', env('ZENVIA_FROM', 'Sistema')));
    }

    /**
     * @param string|string[]|NumberResource|NumberResource[] $numbers
     * @throws $this
     * @throws \Louis\Zenvia\Exceptions\FieldMissingException
     */
    public function setNumber($numbers)
    {
        if(!$this->numbers instanceof NumberCollection){
            $this->numbers = new NumberCollection();
        }

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
            throw new FieldMissingException('texto');
        }
        if($this->numbers->isEmpty()){
            throw new FieldMissingException('número');
        }

        $this->messages = new MessageCollection();

        foreach ($this->numbers->get()->chunk(100) as $numbersChunked){

            $this->messages->add(new MessageResource($this->from, new NumberCollection($numbersChunked), $this->text));
        }

        return $this->messages;
    }

    /**
     * @throws AuthenticationNotFoundedException
     * @throws \Louis\Zenvia\Exceptions\FieldMissingException
     * @throws \Louis\Zenvia\Exceptions\RequestException
     */
    public function send()
    {
        Zenvia::log('Tentativa de envio de sms');
        $request = new EnviarSmsRequest($this->authentication->getKey());
        Zenvia::log('Gerando mensagens');
        try{
            foreach($this->getMessage()->get() as $message){
                $response = $request->send($message);
            }
            Zenvia::log('Mensagens enviadas com sucesso');
        }catch (\Exception $exception){
            Zenvia::log($exception->getMessage(), self::LOG_ERROR);
        }
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

    static public function log($message, $type = self::LOG_INFO)
    {
        if(config('zenvia.log', true)){
            $log = \Log::channel(config('zenvia.channel', 'zenvia'));
            switch ($type){
                case self::LOG_ERROR:
                    $log->error($message);
                    break;
                default:
                    $log->info($message);
            }
        }
    }
}
