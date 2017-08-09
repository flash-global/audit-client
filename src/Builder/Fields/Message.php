<?php
namespace Fei\Service\Audit\Client\Builder\Fields;

use Fei\Service\Audit\Client\Builder\OperatorBuilder;

/**
 * Class Message
 * @package Fei\Service\Audit\Client\Builder\Fields
 */
class Message extends OperatorBuilder
{
    /**
     * @param $value string the value used to filter this field
     * @param string $operator the operator used to filter this field
     *
     * @return mixed|void
     */
    public function build($value, $operator = null)
    {
        $search = $this->builder->getParams();
        $search['auditEvent_message'] = $value;
        $search['auditEvent_operator'] = (isset($operator)) ? $operator : '=';

        $this->builder->setParams($search);
    }
}
