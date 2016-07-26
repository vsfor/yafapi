<?php
class Jeen
{
    public static function echoln($obj = null)
    {
        if(\Yaf\Dispatcher::getInstance()->getRequest()->isCli()) {
            $br = PHP_EOL;
        } else {
            $br = '<br>';
        }
        echo $br;
        if (is_bool($obj) || is_null($obj)) {
            var_dump($obj);
        } elseif (is_string($obj) && $br=='<br>') {
            echo htmlentities($obj);
        } else {
            if($br == '<br>') {
                echo '<pre>';
                print_r($obj);
                echo '</pre>';
            } else {
                print_r($obj);
            }
        }
        echo $br;
    }

    /**
     * 打印数组 信息
     * @param $arr
     * @param int $i
     * @return boolean true
     */
    public static function lsArray($arr, $i = 1)
    {
        if(\Yaf\Dispatcher::getInstance()->getRequest()->isCli()) {
            echo PHP_EOL; print_r($arr); echo PHP_EOL;
            return true;
        }
        for ($pre = '', $j = 0; $j < $i; $j++)
            $pre .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $colors = array(
            '#000000',
            '#002060',
            '#c00000',
            '#00b050',
            '#ff0000',
            '#0070c0',
            '#00b0f0',
            '#7030a0',
            '#c0504d',
            '#ffc000',
            '#000000'
        );
        $color = $colors[$i];
        foreach ($arr as $key => $item)
        {
            if (is_array($item)) {
                echo "$pre<span style='color:$color;'>\$array" . $i . "['$key'] == []</span><br>";
                self::lsArray($item, $i + 1);
            } elseif (is_object($item)) {
                echo "$pre<span style='color:$color;'>\$array" . $i . "['$key'] == class " . get_class($item) . "</span><br>";
            } else {
                echo "$pre<span style='color:$color;'>\$array" . $i . "['$key'] == " . htmlentities(strval($item)) . "</span><br>";
            }
        }
        return true;
    }

    /**打印对象调试信息
     * @param $jobj
     */
    public static function show($jobj)
    {
        $flag = \Yaf\Dispatcher::getInstance()->getRequest()->isCli();
        $br = $flag ? PHP_EOL : '<br>';
        $jtype = gettype($jobj);
        echo $flag ? "$br==Object Debug Info By Jeen==$br" : '<br><div style="background:#eee;padding:10px;"><font color="red">Object Debug Informations By Jeen :</font><br/>';
        echo $jtype . ': -- Information -------------'.$br;
        if ($jtype == 'array') {
            self::lsArray($jobj);
        } elseif ($jtype == 'boolean') {
            var_dump($jobj);
        } elseif ($jtype == 'object') {
            $jclass = get_class($jobj);
            $jref = new \ReflectionClass($jclass);
            $jvars = $jref->getProperties();
            $jmethods = $jref->getMethods();
            echo 'Class : ' . $jclass . ' | has: ' .
                count($jvars) . ' vars and ' .
                count($jmethods) . ' methods .'.$br.'You can see it in File:'
                . $jref->getFileName() . $br;
            $property_str = $flag ? "$br== Properties ==$br" : '<br><b>Properties:</b><hr>';
            foreach ($jvars as $key=>$property) {
                $modname = \Reflection::getModifierNames($property->getModifiers());
                $callmod = isset($modname[1]) && $modname[1] == 'static' ? '::$' : '->';
                $modname = $modname[0] ? : 'public';
                $property_str .= $flag ? ("$key|$modname " . $property->getDeclaringClass()->getName() . $callmod . $property->getName() . $br) : ("$key|$modname " . $property->getDeclaringClass()->getName() . "$callmod<b>" . $property->getName() . "</b>$br");
            }
            echo $property_str;
            echo $flag ? "$br== Functions ==$br" : '<br><b>Functions</b>:<hr>';
            foreach ($jmethods as $key => $value) {
                $params = '';
                $modname = \Reflection::getModifierNames($value->getModifiers());
                $callmod = isset($modname[1]) && $modname[1] == 'static' ? '::' : '->';
                $modname = $modname[0] ? : 'public';
                echo $flag ? ("$key|$modname " . $value->getDeclaringClass()->getName() . $callmod . $value->getName() . '(') : ("$key|<b>$modname</b> " . $value->getDeclaringClass()->getName() . "$callmod<b>" . $value->getName() . '</b>(');
                foreach ($value->getParameters() as $jparam)
                {
                    $params .= $jparam;
                }
                for ($i = 1; $i < 10; $i++)
                    $params = str_replace(']Parameter #' . $i . ' [', ',', $params);
                $params = str_replace('Parameter #0 [', '', $params);
                $params = str_replace(']', '', $params);
                echo $params;
                echo ')'.$br;
            }
        } else {
            print_r($jobj);
        }
        echo $flag? "$br==$jtype --Information End == $br": $br.$br. $jtype . ': -- Information End ----------<br></div>'.$br;
    }

    public static function trace()
    {
        $flag = \Yaf\Dispatcher::getInstance()->getRequest()->isCli();
        $br = $flag ? PHP_EOL : '<br>';
        $arr = debug_backtrace();
//        unset($arr[0]);// -- 可隐藏调用调试信息的方法
        $arr = array_reverse($arr);
        $info = '';
        foreach ($arr as $v) {
            $info .= (isset($v['file']) && isset($v['line'])) ? $br.'Path Information:' . $v['file'] . ':' . $v['line'] : '';
            $info .= $br.'Trace Information: ' . $v['class'] . $v['type'] . $v['function'] . '(' . json_encode($v['args']) . ')'.$br;
        }
        self::show($info);
    }

    public static function debugEcholn($obj)
    {
        if(defined("J_DEBUG") && J_DEBUG) {
            self::echoln($obj);
        }
    }

    public static function debugTrace()
    {
        if(defined("J_DEBUG") && J_DEBUG) {
            self::trace();
        }
    }

    public static function debugShow($obj)
    {
        if(defined("J_DEBUG") && J_DEBUG) {
            self::show($obj);
        }
    }
}