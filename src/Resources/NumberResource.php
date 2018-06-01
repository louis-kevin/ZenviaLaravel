<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:47
 */

namespace Louisk\Zenvia\Resources;


use Zenvia\Exceptions\FieldMissingException;
use Carbon\Carbon;

class NumberResource extends Resource
{
    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $id;
    /**
     * @var Carbon|null
     */
    private $schedule;
    /**
     * @var bool
     */
    private $isFlashSms = false;
    /**
     * @var string|null
     */
    private $message;
    /**
     * @var string
     */
    private $callback = 'NONE';

    /**
     * NumberResource constructor.
     * @param $number
     * @throws FieldMissingException
     */
    public function __construct(string $number)
    {
        if (blank($number)) {
            throw new FieldMissingException('número não pode ser vazio');
        }
        $number = $this->removeMaskTelefone($number);
        if (strlen($number) < 12) {
            $number = '55' . $number;
        }
        $this->number = $number;
        $this->id = uniqid();
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isSchedule(): bool
    {
        return !!$this->schedule;
    }

    /**
     * @return string
     */
    public function getDateTimeSchedule(): string
    {
        if (!$this->schedule) {
            return '';
        }
        return $this->schedule->toDateString() . 'T' . $this->schedule->toTimeString();
    }

    /**
     * @param string $number
     * @return bool
     */
    public function isSameNumber(string $number)
    {
        return $this->number == $number;
    }

    public function setFlashSms(bool $isFlashSms = true)
    {
        $this->isFlashSms = $isFlashSms;
        return $this;
    }

    /**
     * @param Carbon|null $schedule
     * @return NumberResource
     */
    public function setSchedule(?Carbon $schedule): NumberResource
    {
        if (!$schedule instanceof Carbon && !is_null($schedule)) {
            $schedule = Carbon::parse($schedule);
        }
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * @return string
     */
    public function getCallback(): ?string
    {
        return $this->callback;
    }

    public function setCallBack(string $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function getIsFlashSms(): ?string
    {
        return $this->isFlashSms;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param FromResource $from
     * @param TextResource|null $text
     * @throws FieldMissingException
     */
    public function getBodyRequest(FromResource $from, TextResource $text = null, string $aggregateId = null)
    {
        if(!($message = $this->getMessage()) && !$text){
            throw new FieldMissingException('Texto não pode ser vazio');
        }
        $data = [
            'from' => $from->getFrom(),
            'to' => $this->getNumber(),
            'msg' => "uma mensagem",
            'callbackOption' => $this->getCallback(),
            'id' => $this->getId(),
            'flashSms' => $this->getIsFlashSms()
        ];

        if($this->isSchedule()){
            $data['schedule'] = $this->getDateTimeSchedule();
        }

        if($aggregateId){
            $data['aggregateId'] = $aggregateId;
        }

        return $data;
    }
}
