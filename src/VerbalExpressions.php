<?php
/**
* @author SignpostMarv
*/


namespace TraceverbalExpressions\PHPTraceverbalExpressions;

use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use VerbalExpressions\PHPVerbalExpressions\VerbalExpressions as UpstreamExpr;

class VerbalExpressions
    implements
        JsonSerializable
{

    protected $VerbalExpressions;


    protected $callStack = array();

    public function __construct($serializedFrom=null)
    {
        $from = null;
        $this->VerbalExpressions = new UpstreamExpr;
        if (!is_null($serializedFrom)) {
            if (is_string($serializedFrom)) {
                $from = json_decode($serializedFrom);
                if (!is_array($from)) {
                    throw new InvalidArgumentException(
                        'Serialized arguments must be passed as JSON strings!'
                    );
                }
            } else {
                $from = $serializedFrom;
            }
            if (is_array($from)) {
                foreach ($from as $callStackItem) {
                    if (
                        isset(
                            $callStackItem->method,
                            $callStackItem->arguments
                        )
                    ) {
                        call_user_func_array(
                            array($this, $callStackItem->method),
                            $callStackItem->arguments
                        );
                    } elseif (!isset($callStackItem->method)) {
                        throw new InvalidArgumentException(
                            'Serialized argument did not include method!'
                        );
                    } else {
                        throw new InvalidArgumentException(
                            'Serialized argument did not include arguments!'
                        );
                    }
                }
            } else {
                throw new InvalidArgumentException(
                    'Constructor argument was not an array or JSON string!'
                );
            }
        }
    }


    public function __call($method, array $arguments)
    {
        if (!method_exists($this->VerbalExpressions, $method)) {
            throw new BadMethodCallException(
                (get_class($this->VerbalExpressions) . '::' . $method) .
                ' does not exist!'
            );
        }
        $out = call_user_func_array(
            array($this->VerbalExpressions, $method),
            $arguments
        );
        $returnType = gettype($out);
        if ($out instanceof UpstreamExpr) {
            $returnType = 'sameInstance';
        }
        $this->callStack[] = array(
            'method' => $method,
            'arguments' => array_values($arguments),
            'returnType' => $returnType
        );
        if ($out instanceof UpstreamExpr) {
            return $this;
        }
        return $out;
    }

    public function jsonSerialize()
    {
        return $this->callStack;
    }
}
