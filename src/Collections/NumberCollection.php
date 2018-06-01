<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:53
 */

namespace Louisk\Zenvia\Collections;


use Zenvia\Resources\NumberResource;
use Illuminate\Support\Collection;

class NumberCollection
{
    /**
     * @var NumberResource[]|Collection $numbers
     */
    private $numbers;

    public function __construct(array $numbers = null)
    {
        $this->numbers = collect($numbers);
    }

    /**
     * @param Collection $numbers
     * @return NumberCollection
     */
    public function setNumbers(Collection $numbers): NumberCollection
    {
        $this->numbers = $numbers;
        return $this;
    }

    /**
     * @param NumberResource $number
     * @return $this
     * @throws \Zenvia\Exceptions\FieldMissingException
     */
    public function addNumber($number)
    {
        if(!$number instanceof NumberResource){
            $number = new NumberResource($number);
        }
        $this->numbers[] = $number;
        return $this;
    }

    /**
     * @param NumberResource $numberToRemove
     * @return $this
     */
    public function removeNumber(NumberResource $numberToRemove)
    {
        $this->numbers = $this->numbers->reject(function(NumberResource $number) use ($numberToRemove) {
           return $number->isSameNumber($numberToRemove->getNumber());
        });

        return $this;
    }

    public function isEmpty()
    {
        return $this->numbers->isEmpty();
    }

    public function isNotEmpty()
    {
        return $this->numbers->isNotEmpty();
    }

    public function count()
    {
        return $this->numbers->count();
    }

    public function isMultiNumbers()
    {
        return $this->count() > 1;
    }

    public function first(): NumberResource
    {
        return $this->numbers->first();
    }

    public function get()
    {
        return $this->numbers;
    }
}
