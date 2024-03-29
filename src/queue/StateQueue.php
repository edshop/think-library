<?php
// +----------------------------------------------------------------------
// | Think-Library
// +----------------------------------------------------------------------
// | 版权所有 2015~2020 泉州市牧滨电子商务有限公司 [ http://www.mubin.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.mubin.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://github.com/edshop/think-library
// +----------------------------------------------------------------------

namespace think\admin\queue;

use think\admin\extend\ProcessExtend;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * 查看异步任务监听的主进程状态
 * Class StateQueue
 * @package think\admin\queue
 */
class StateQueue extends Command
{
    /**
     * 指令属性配置
     */
    protected function configure()
    {
        $this->setName('xtask:state')->setDescription('[控制]查看异步任务监听主进程状态');
    }

    /**
     * 指令执行状态
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $command = ProcessExtend::think('xtask:listen');
        if (count($result = ProcessExtend::query($command)) > 0) {
            $output->info("异步任务监听主进程{$result[0]['pid']}正在运行...");
        } else {
            $output->error("异步任务监听主进程没有运行哦!");
        }
    }
}
