<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
/**
 * 日志处理类
 */
class Log {

    // 日志级别 从上到下，由低到高
    const EMERG     = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT     = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT      = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR       = 'ERR';  // 一般错误: 一般性错误
    const WARN      = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE    = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO      = 'INFO';  // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  // 调试: 调试信息
    const SQL       = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效
    // 日志信息
    static protected $log       =  array();

    // 日志存储
    static protected $storage   =   null;

    // 日志初始化
    static public function init($config=array()){
        $type   =   isset($config['type']) ? $config['type'] : 'File';
        $class  =   strpos($type,'\\')? $type: 'Think\\Log\\Driver\\'. ucwords(strtolower($type));           
        unset($config['type']);
        self::$storage = new $class($config);
    }

    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     */
    static function record($message,$level=self::ERR,$record=false) {
        if($record || false !== strpos(C('LOG_LEVEL'),$level)) {
            self::$log[] =   "{$level}: {$message}\r\n";
        }
    }

    /**
     * 日志保存
     * @static
     * @access public
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function save($type='',$destination='') {
        if(empty(self::$log)) return ;

        $d=date('Y',time());
        $m=date('m',time());
        //判断是否存在以当前年份为名的目录，没有就创建
        if(!is_dir(C('LOG_PATH').$d.'/')) {
            mkdir(C('LOG_PATH').$d. '/',077,ture);
        }
        //判断是否存在以当前月份为名的目录，没有就创建
        if(!is_dir(C('LOG_PATH').$d.'/'.$m.'/')) {
            mkdir(C('LOG_PATH').$d. '/'.$m.'/',077,ture);
        }

        if(empty($destination)){
            $destination = C('LOG_PATH').$d. '/'.$m.'/'.date('y_m_d',time()).'.log';
        }
        if(!self::$storage){
            $type 	= 	$type ? : C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();            
        }
        $message    =   implode('',self::$log);
        self::$storage->write($message,$destination);
        // 保存后清空日志缓存
        self::$log = array();
    }

    /**
     * 日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function write($message,$level=self::ERR,$type='',$destination='') {
        if(!self::$storage){
            $type 	= 	$type ? : C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            $config['log_path'] = C('LOG_PATH');
            self::$storage = new $class($config);            
        }
        $d=date('Y',time());
        $m=date('m',time());
        //判断是否存在以当前年份为名的目录，没有就创建
        if(!is_dir(C('LOG_PATH').$d.'/')) {
            mkdir(C('LOG_PATH').$d. '/',077,ture);
        }
        //判断是否存在以当前月份为名的目录，没有就创建
        if(!is_dir(C('LOG_PATH').$d.'/'.$m.'/')) {
            mkdir(C('LOG_PATH').$d. '/'.$m.'/',077,ture);
        }
        if(empty($destination)){
            $destination = C('LOG_PATH').$d. '/'.$m.'/'.date('y_m_d',time()).'.log';        
        }
        self::$storage->write("{$level}: {$message}", $destination);
    }

    /**
     * 用户操作日志写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function writeUseroperation($message,$level=self::INFO,$type='',$destination=''){
        if(!self::$storage){
            $type   =   $type ? : C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            $config['log_path'] = C('LOG_PATH');
            self::$storage = new $class($config);            
        }

        $d=date('Y',time());
        $m=date('m',time());
        //判断是否存在以当前年份为名的目录，没有就创建
        if(!is_dir(C('LOG_PATH').$d.'/')) {
            mkdir(C('LOG_PATH').$d. '/',077,ture);
        }
        //判断是否存在以当前月份为名的目录，没有就创建
        if(!is_dir(C('LOG_PATH').$d.'/'.$m.'/')) {
            mkdir(C('LOG_PATH').$d. '/'.$m.'/',077,ture);
        }

        if(empty($destination)){
            $destination = C('LOG_PATH').$d. '/'.$m.'/'.date('y_m_d',time()).'.log';        
        }
        $date=date('Y-m-d H:i:s',time());
        self::$storage->write("[{$date}]{$level}: {$message}", $destination);
        //self::$storage->write("{$level}: {$message}", $destination);
        // $fp=fopen(C('LOG_PATH').$d. '/'.$m.'/'.'UserOperation_'.date('ymd',time()).'.log','a+');
        // $data='['.$level.']'.date('Y-m-d H:i:s',time()).'=>'.$message."\r\n";
        // fwrite($fp,$data);
        // fclose($fp);
    }
}