<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:51
 */

namespace Louisk\Zenvia\Resources;


use Zenvia\Exceptions\FieldMissingException;

class TextResource extends Resource
{
    /**
     * @var string
     */
    private $text;

    /**
     * TextResource constructor.
     * @param string $text
     * @throws FieldMissingException
     */
    public function __construct(string $text)
    {
        if(blank($text)){
            throw new FieldMissingException('Texto não pode ser vazio');
        }
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
