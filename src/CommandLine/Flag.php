<?php

namespace Mix\Console\CommandLine;

/**
 * Class Flag
 * @package Mix\Console\CommandLine
 * @author liu,jian <coder.keda@gmail.com>
 */
class Flag
{

    /**
     * 命令行选项
     * @var array
     */
    protected static $_options = [];

    /**
     * 初始化
     */
    public static function initialize()
    {
        // 解析全部选项
        $start = 2;
        if (Argument::subCommand() == '') {
            $start = 1;
        }
        if (Argument::command() == '') {
            $start = 0;
        }
        $argv = $GLOBALS['argv'];
        $tmp  = [];
        foreach ($argv as $key => $item) {
            if ($key <= $start) {
                continue;
            }
            $name  = $item;
            $value = '';
            if (strpos($name, '=') !== false) {
                list($name, $value) = explode('=', $item);
            }
            if (substr($name, 0, 2) == '--' || substr($name, 0, 1) == '-') {
                if (substr($name, 0, 1) == '-' && $value === '' && isset($argv[$key + 1])) {
                    $next = $argv[$key + 1];
                    if (preg_match('/^[\S\s]+$/i', $next)) {
                        $value = $next;
                    }
                }
            } else {
                $name = '';
            }
            if ($name !== '') {
                $tmp[$name] = $value;
            }
        }
        self::$_options = $tmp;
    }

    /**
     * 获取布尔值
     * @param $name
     * @param bool $default
     * @return bool
     */
    public static function bool($name, $default = false)
    {
        foreach (self::$_options as $key => $value) {
            $names = [$name];
            if (is_array($name)) {
                $names = $name;
            }
            foreach ($names as $item) {
                if (strlen($item) == 1) {
                    $names[] = "-{$item}";
                } else {
                    $names[] = "--{$item}";
                }
            }
            if (in_array($key, $names)) {
                if ($value === 'false') {
                    return false;
                }
                return true;
            }
        }
        return $default;
    }

    /**
     * 获取字符值
     * @param string|array $name
     * @param string $default
     * @return string
     */
    public static function string($name, $default = '')
    {
        foreach (self::$_options as $key => $value) {
            $names = [$name];
            if (is_array($name)) {
                $names = $name;
            }
            foreach ($names as $item) {
                if (strlen($item) == 1) {
                    $names[] = "-{$item}";
                } else {
                    $names[] = "--{$item}";
                }
            }
            if (in_array($key, $names)) {
                if ($value === '') {
                    return $default;
                }
                return $value;
            }
        }
        return $default;
    }

    /**
     * 全部命令行选项
     * @return array
     */
    public static function options()
    {
        return self::$_options;
    }

    /**
     * 全部命令行值
     * @return array
     */
    public static function values()
    {
        $options = static::options();
        $values  = [];
        foreach ($options as $flag => $value) {
            if (substr($flag, 0, 2) == '--') {
                $values[substr($flag, 2)] = $value;
            }
            if (substr($flag, 0, 1) == '-') {
                $values[substr($flag, 1)] = $value;
            }
        }
        return $values;
    }

}
