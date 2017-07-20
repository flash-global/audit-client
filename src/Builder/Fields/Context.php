<?php
namespace Fei\Service\Audit\Client\Builder\Fields;

use Fei\Service\Audit\Client\Builder\OperatorBuilder;

/**
 * Class Context
 * @package Fei\Service\Audit\Client\Builder\Fields
 */
class Context extends OperatorBuilder
{
    /**
     * @param $value string the value used to filter this field
     * @param string $operator string the operator used to filter this field
     *
     * @return mixed|void
     */
    public function build($value, $operator = '=')
    {
        $search = $this->builder->getParams();
        $search['context_value'][] = $value;
        $search['context_operator'][] = $operator;
        $search['context_key'][] = $this->getInCache();

        $this->builder->setParams($search);
    }

    /**
     * @param string $key the key used to filter context
     *
     * @return $this
     */
    public function key($key)
    {
        $this->setInCache($key);
        return $this;
    }
}
