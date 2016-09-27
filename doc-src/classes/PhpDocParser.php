<?php


class PhpDocParser
{
    /**
     * @var ReflectionClass
     */
    protected $reflection;

    public function __construct(\ReflectionClass $class)
    {
        $this->reflection = $class;
    }

    public function classDesc(){
        $str = preg_replace(array('(\/\*\*[\s]+[*][\s])','([\s]\*\/)'), '', $this->reflection->getDocComment());
        $str = preg_split('/(\n)|(\r)/', $str);
        return $str[0];
    }

    public function methodSignature($methodInfo){

        $string = $methodInfo['name'].' ( ';

        if(isset($methodInfo['param'])){
            $tmp = array();
            foreach($methodInfo['param'] as $name=>$param){
                $str = $param['type'].' ';
                if($param['byref']) $str.='&';
                $str .= $param['name'];

                if($param['default']!=='__no_def__'){
                    $str .= ' = ' . $this->formatValue($param['default']);
                }
                $tmp[] = $str;
            }
            $string .= implode(', ', $tmp);
        }
        $string .= ' ): ';
        if(isset($methodInfo['return']['type'])) {
            $string .= $methodInfo['return']['type'].' ';
        } else {
            $string .= 'void ';
        }
        return $string;
    }

    public function documentMethod($name){
        try {
            $method     = $this->reflection->getMethod($name);
            $docComment = $this->parseDocComment($method->getDocComment());

            $params              = $method->getParameters();
            $docComment['name']  = $name;
            $docComment['scope'] = 'public';
            if ($method->isPrivate()) {
                $docComment['scope'] = 'private';
            } else if ($method->isProtected()) {
                $docComment['scope'] = 'protected';
            }
            foreach ($params as $param) {
                $docComment['param'][$param->getName()]['default'] = null;
                $docComment['param'][$param->getName()]['byref']   = $param->isPassedByReference();

                if ($param->isOptional()) {
                    $docComment['param'][$param->getName()]['default'] = $param->getDefaultValue();
                } else {
                    $docComment['param'][$param->getName()]['default'] = '__no_def__';
                }
            }

            return $docComment;
        } catch (Exception $e) {
            return '';
        }
    }

    public function parseDocComment($docComment)
    {
        $parsed = $docComment;
        $parsed = preg_replace('/(\/[\*]+[\r\n]+)/', '', $parsed); // Remove: /**
        $parsed = preg_replace('/([\r\n]+[ \t]+[\*]\/)/', '', $parsed); // Remove:  */
        $parsed = preg_replace('/([ \t]+[\*][\r\n]+)/', '', $parsed); // Remove blank lines with asterisks:  *
        $parsed = preg_replace('/([ \t]+[\*][ ]+)/', '', $parsed); // Remove asterisks:  *
        $parsed = preg_split('/[\r\n]+/', $parsed); // array

        $result = array(
            'desc' => '',
            'return' => ''
        );
        if (is_array($parsed)) {
            foreach ($parsed as $i => $line) {
                $tokens = explode(' ', $line, 2);
                if ($tokens[0] === '@param') { // @param

                    $param = $this->parseParam($line);
                    $result['param'][ltrim($param['name'],'$')] = $param;

                } else if ($tokens[0] === '@return') {
                    $result['return'] = $this->parseReturn($line);

                } else if ($tokens[0] === '@throws') {
                    $result['throws'][] = $this->parseReturn($line);

                } else {
                    if (substr($tokens[0], 0, 1) !== '@') {
                        $result['desc'].= $line;
                    }
                }
            }
        }

        return $result;
    }

    function parseParam($line)
    {
        $param = array(
            'type' => '',
            'name' => '',
            'desc' => '',
        );

        $tokens = explode(' ', $line, 4);

        if (isset($tokens[3])) {
            $param['type'] = $tokens[1];
            $param['name'] = $tokens[2];
            $param['desc'] = $tokens[3];

        } else if (isset($tokens[2])) {
            $param['type'] = $tokens[1];
            $param['name'] = $tokens[2];
            $param['desc'] = '';
        } else if (isset($tokens[1])) {
            $param['type'] = '';
            $param['name'] = $tokens[1];
            $param['desc'] = '';
        }

        return $param;
    }

    function parseReturn($line)
    {
        $return = array(
            'type' => '',
            'desc' => '',
        );

        $tokens = explode(' ', $line, 3);

        if (isset($tokens[2])) {
            $return['type'] = $tokens[1];
            $return['desc'] = $tokens[2];
        } else if (isset($tokens[1])) {
            $return['type'] = $tokens[1];
            $return['desc'] = '';
        }

        return $return;
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
}