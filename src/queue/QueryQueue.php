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
 * 查询正在执行中的进程PID信息
 * Class QueryQueue
 * @package think\admin\queue
 */
class QueryQueue extends Command
{
    /**
     * 指令属性配置
     */
    protected function configure()
    {
        $this->setName('xtask:query')->setDescription('[控制]查询正在执行的所有任务进程');
    }

    /**
     * 执行相关进程查询
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $result = ProcessExtend::query(ProcessExtend::think("xtask:"));
        if (count($result) > 0) foreach ($result as $item) {
            $output->writeln("{$item['pid']}\t{$item['cmd']}");
        } else {
            $output->writeln('没有查询到相关任务进程');
        }
    }
}
