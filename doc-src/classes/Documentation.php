<?php

class Documentation {

    public $description;
    public $signature;
    public $params;
    public $returnType;
    public $returnDesc;

    private $docBlock;

    public function __construct( \ReflectionClass $class, $methodName ) {
        try {
            $method = $class->getMethod($methodName);
            $params = $method->getParameters();
            $comment = $method->getDocComment();
            $this->docBlock = new DocBlock($comment);

            $this->description = $this->docBlock->description;
            $this->signature = $this->buildSignature($method);
            $this->params = $this->buildParams($params);
            if (isset($this->docBlock->all_params['return'])) {
                $this->returnType = $this->return_type($this->docBlock->all_params['return'][0]);
                $this->returnDesc = $this->return_desc($this->docBlock->all_params['return'][0]);
            }
        } catch (\Exception $e){
            
        }
    }
    
    protected function isAssoc(array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    protected function arrayToStr($array){
        $string = 'array(';

        if($this->isAssoc($array)){
            $string .= ' ';
            $el = array();
            foreach($array as $name=>$val){
                $el[] = $name.' => '.$this->formatValue($val);
            }
            $string .= implode(',', $el);
            $string .= ' ';
        } else {
            $string .= ' ';
            $el = array();
            foreach($array as $name=>$val){
                $el[] = $this->formatValue($val);
            }
            $string .= implode(', ', $el);
            $string .= ' ';
        }
        return $string.')';

    }

    protected function formatValue($value){
        if( is_bool( $value ) ){
            $value = ($value) ? 'true' : 'false';
        } else if ( is_string( $value ) ) {
            $value = "'{$value}'"; // Surround with quotes
        } else if( null === $value){
            $value = 'null'; // Use the word null
        } else if ( is_array($value)){
            $value = $this->arrayToStr($value);
        }
        return $value;
    }

    public function buildSignature( \ReflectionMethod $method){
        $params = array();

        foreach($method->getParameters() as $param){
            $default = '';
            if($param->isOptional()) {

                $default = ' = '. $this->formatValue($param->getDefaultValue());
            }
            $params[] = '$'.$param->getName().$default;
        }
        $param_str = implode(', ', $params);
        return "{$method->getName()}( {$param_str} )";
    }

    public function buildParams( array $params ){
        $array = array();
        /**
         * @var \ReflectionParameter $param
         */
        foreach($params as $i=>$param){
            $array[$i] = array(
                'name' => $param->getName(),
                'desc' => $this->param_desc($this->docBlock->all_params['param'][$i]),
                'type' => $this->param_type($this->docBlock->all_params['param'][$i]),
            );
        }
        return $array;
    }

    public function param_type($paramLine){
        $dollar = substr($paramLine, 0, 1);
        if($dollar !== '$'){ // not var name
            $space1 = strpos($paramLine, ' ');
            if($space1 !== false){
                return substr( $paramLine, 0, $space1 );

            }
        }

        return '';
    }

    public function param_desc($str){
        $space1 = strpos($str, ' ');
        if($space1 !== false){
            $space2 = strpos($str, ' ', $space1+1);
            if($space2 !== false) {
                return substr( $str, $space2 );
            }
        }
        return '';
    }
    function return_type($str){
        $space1 = strpos($str, ' ');
        return substr( $str, 0, $space1 );
    }

    function return_desc($str){
        $space1 = strpos($str, ' ');
        return substr( $str, $space1 );
    }
}