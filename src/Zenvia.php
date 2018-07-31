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
use Illuminate\Support\Collection;
use Louis\Zenvia\Responses\Response;

class Zenvia
{
    const LOG_INFO = 'info';
    const LOG_ERROR = 'error';
    /**
     * @var NumberCollection
     */
    private $numbers;
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
     * @var FromResource
     */
    private $from;

    /**
     * ZenviaService constructor.
     * @param $account
     * @param $password
     * @throws AuthenticationNotFoundedException
     * @throws FieldMissingException
     */
    public function __construct($account, $password)
    {
        $this->authentication = new AuthenticationResource($account, $password);

        $this->from = new FromResource(config('zenvia.from', env('ZENVIA_FROM', 'Sistema')));
    }

    /**
     * @param string|string[]|NumberResource|NumberResource[] $numbers
     * @return Zenvia
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
            try{
                $this->numbers->addNumber($number instanceof NumberResource ? $number : new NumberResource($number));
            }catch (FieldMissingException $exception){
                
            }
        }
        
        
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     * @throws FieldMissingException
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
        $responses = [];
        Zenvia::log('Gerando mensagens');
        try{
            foreach($this->getMessage()->get() as $message){
                $responses[] = $request->send($message);
            }
            Zenvia::log('Mensagens enviadas com sucesso');
        }catch (\Exception $exception){
            Zenvia::log($exception->getMessage(), self::LOG_ERROR);
        }
        /**
         * @var Response $response
         */
        foreach($responses as $response){
            if($response->failed()){
                Zenvia::log('Error: '.$response->getDetailCode(), self::LOG_ERROR);
            }
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

    public function withoutFrom()
    {   
        $this->from = null;
        return $this;
    }

    public function setFrom($from)
    {
        if(!$from instanceof FromResource){
            $from = new FromResource($from);
        }
        
        $this->from = $from;
        
        return $this;
    }

    static public function log($message, $type = self::LOG_INFO)
    {
        if(!config('zenvia.log', true)){
            return;
        }
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
