<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 11:26
 */

namespace Zenvia\Resources;


use Zenvia\Exceptions\FieldMissingException;

class FromResource extends Resource
{
    /**
     * @var string
     */
    private $from;

    /**
     * FromResource constructor.
     * @param string $from
     * @throws FieldMissingException
     */
    public function __construct(string $from)
    {
        if(blank($from)){
            throw new FieldMissingException('Remetente não pode ser vazio');
        }
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

}
