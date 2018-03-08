<?php
namespace AppBundle\DataWrapper;
use AppBundle\DataWrapper\AbstractDataWrapper;
use AppBundle\DataContainer\CharacterDataContainer;
/**
 * Class CharacterDataWrapper
 * @package AppBundle\DataWrapper
 */
class CharacterDataWrapper extends AbstractDataWrapper
{
    /**
     * The results returned by the call.
     *
     * @var mixed
     */
    private $data;
    /**
     * @return CharacterDataContainer
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @param CharacterDataContainer $data
     */
    public function setData(CharacterDataContainer $data)
    {
        $this->data = $data;
    }
}