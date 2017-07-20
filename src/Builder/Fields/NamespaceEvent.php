<?php
namespace Fei\Service\Audit\Client\Builder\Fields;

use Fei\Service\Audit\Client\Builder\AbstractBuilder;

/**
 * Class NamespaceEvent
 * @package Fei\Service\Audit\Client\Builder\Fields
 */
class NamespaceEvent extends AbstractBuilder
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
        $search['namespace'] = $value;

        $this->builder->setParams($search);
    }
}
