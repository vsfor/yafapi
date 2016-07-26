<?php
define("APP_PATH", dirname(dirname(__DIR__)));
define("DS", DIRECTORY_SEPARATOR); /* 目录分隔符 */
define("PS", PATH_SEPARATOR); /* 路径分隔符 */
define("J_ENV", "local"); //项目运行环境划分
define("J_DEBUG", true); //全局调试控制
if(J_DEBUG) {
    error_reporting(E_ALL);
} else {
    error_reporting(E_ERROR);
}
class SYNAPI {

    public function handle($task)
    {
        try {
            $ps = func_get_args();
            if(!is_array($task)) {
                $task = [
                    'r' => $ps[0], //route
                    'p' => $ps[1], //params
                    'm' => $ps[2], //method
                    't' => $ps[3] //timestamp
                ];
            }
            $_app = new \Yaf\Application(APP_PATH . "/apiyar/config/". J_ENV .".ini");
            $_app->bootstrap();//执行bootstrap 初始化相关资源
            \vendor\jeen\JLog::debug('Task to Handle:|ps:'.json_encode($ps).'|Task:'.json_encode($task),[],'japi/debug');
            $r = explode('/',$task['r']);
            $request = new \Yaf\Request\Simple($task['m'],$r[0],$r[1],$r[2],$task['p']);
            ob_start();
            $_app->getDispatcher()->dispatch($request);
            $response = ob_get_clean();
            \vendor\jeen\JLog::debug("Task Handle Response: \n".$response,[],'japi/debug');
            return $response;
        } catch(\Exception $e) {
            \vendor\jeen\JLog::debug("Task Handle Exception: \n".$e->getMessage().PHP_EOL.$e->getTraceAsString(),[],'japi/debug');
            if (J_DEBUG) {
                return $e->getMessage().PHP_EOL.$e->getTraceAsString().PHP_EOL;
            } else {
                return null;
            }
        }
    }

}

$server = new Yar_Server(new SYNAPI());
$server->handle();