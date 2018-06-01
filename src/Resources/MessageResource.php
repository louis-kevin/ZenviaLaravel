<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:50
 */

namespace Louis\Zenvia\Resources;


use Louis\Zenvia\Exceptions\FieldMissingException;
use Louis\Zenvia\Collections\NumberCollection;

class MessageResource extends Resource
{
    /**
     * @var TitleResource
     */
    private $title;
    /**
     * @var TextResource
     */
    private $text;
    /**
     * @var NumberCollection
     */
    private $numbers;
    /**
     * @var bool
     */
    private $isMultiNumbers;
    /**
     * @var string
     */
    private $aggregateId;
    /**
     * @var FromResource
     */
    private $from;

    /**
     * MessageResource constructor.
     * @param FromResource $from
     * @param TitleResource $title
     * @param TextResource $text
     * @param NumberCollection $numbers
     * @throws FieldMissingException
     */
    public function __construct(FromResource $from, TitleResource $title, NumberCollection $numbers, TextResource $text = null)
    {
        if ($numbers->isEmpty()) {
            throw new FieldMissingException('Número não pode ser vazio');
        }
        $this->title = $title;
        $this->text = $text;
        $this->numbers = $numbers;
        $this->from = $from;

        $this->isMultiNumbers = $this->numbers->isMultiNumbers();

        $this->aggregateId = rand(1,9999);
    }


    /**
     * @return array
     * @throws FieldMissingException
     */
    public function getBodyRequest()
    {
        if ($this->isMultiNumbers) {
            return [
                'sendSmsMultiRequest' => [
                    'aggregateId' => $this->aggregateId,
                    'sendSmsRequestList' => $this->getBodyMultiNumbers()
                ]

            ];
        }
        return [
            'sendSmsRequest' => $this->getBodyOneNumber()
        ];
    }

    /**
     * @throws FieldMissingException
     */
    private function getBodyMultiNumbers()
    {
        $numbersBodys = [];

        /** @var NumberResource $number */
        foreach($this->numbers->get() as $number){
            $numbersBodys[] = $number->getBodyRequest($this->from, $this->text);
        }

        return $numbersBodys;
    }

    /**
     * @return array
     * @throws FieldMissingException
     */
    private function getBodyOneNumber()
    {
        $number = $this->numbers->first();

        $bodyNumber = $number->getBodyRequest($this->from, $this->text, $this->aggregateId);

        return $bodyNumber;
    }
}
