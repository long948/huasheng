<?php

namespace App\Exceptions\Common;


/**
 * 公共异常类
 * 10xox Public
 * Class CommonException
 * @package App\Exceptions
 */
class CommonException extends \Exception
{
    const UNKNOW = 10000;
    const NOT_DATA = 10001;
    const PARAM_ERROR = 10002;
    const CUSTOMIZE_ERROR = 10003;
    const AUTHORIZATION_EMPTY = 10004;
    const AUTHORIZATION_ERROR = 10005;
    const ADD_ERROR = 10006;

    static public $__names = array(
        self::UNKNOW => 'UNKNOW',
        self::NOT_DATA => 'NOT_DATA',
        self::PARAM_ERROR => 'PARAM_ERROR',
        self::CUSTOMIZE_ERROR => ':message',
        self::AUTHORIZATION_EMPTY => 'AUTHORIZATION_EMPTY',
        self::AUTHORIZATION_ERROR => 'AUTHORIZATION_ERROR',
        self::ADD_ERROR => 'ADD_ERROR',
    );

    /**
     * CommonException constructor.
     * @param string $code
     * @param string $replace
     */
    public function __construct($code, $replace = '')
    {
        $message = self::$__names[$code];
        if(!empty($replace)){
            if(is_string($replace)){
                $message = $replace;
            }
            if(is_array($replace)){
                foreach($replace as $k => $v){
                    $message = str_replace(':'.$k, $v, $message);
                }
            }
        }
        parent::__construct($message, $code);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return response()->json([
                'code' => $this->code,
                'msg' => $this->message,
                'data' => []
            ]);
    }
}
