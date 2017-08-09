<?php
namespace Fei\Service\Audit\Client\Builder\Fields;

use Fei\Service\Audit\Client\Builder\AbstractBuilder;

/**
 * Class Level
 * @package Fei\Service\Audit\Client\Builder\Fields
 */
class Level extends AbstractBuilder
{
    /**
     * @param $value string the value used to filter this field
     * @param string $operator
     *
     * @return mixed|void
     */
    public function build($value, $operator = '=')
    {
        $search = $this->builder->getParams();
        $search['level'] = $value;

        $this->builder->setParams($search);
    }
}
