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
 * 平滑停止异步任务守护的主进程
 * Class StopQueue
 * @package think\admin\queue
 */
class StopQueue extends Command
{

    /**
     * 指令属性配置
     */
    protected function configure()
    {
        $this->setName('xtask:stop')->setDescription('[控制]平滑停止所有的异步任务进程');
    }

    /**
     * 停止所有任务执行
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $command = ProcessExtend::think('xtask:');
        if (count($result = ProcessExtend::query($command)) < 1) {
            $output->writeln("没有需要结束的任务进程哦！");
        } else foreach ($result as $item) {
            ProcessExtend::close($item['pid']);
            $output->writeln("发送结束任务进程{$item['pid']}指令成功！");
        }
    }
}
