<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 10:51
 */

namespace Louis\Zenvia\Resources;


use Zenvia\Exceptions\FieldMissingException;

class TitleResource extends Resource
{
    /**
     * @var string
     */
    private $title;

    /**
     * TitleResource constructor.
     * @param string $title
     * @throws FieldMissingException
     */
    public function __construct(string $title)
    {
        if(blank($title)){
            throw new FieldMissingException('Titulo não pode ser vazio');
        }
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
