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
use think\facade\Db;

/**
 * 检查并创建异步任务监听主进程
 * Class StartQueue
 * @package think\admin\queue
 */
class StartQueue extends Command
{

    /**
     * 指令属性配置
     */
    protected function configure()
    {
        $this->setName('xtask:start')->setDescription('[控制]创建异步任务守护监听主进程');
    }

    /**
     * 执行启动操作
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $this->app->db->name('SystemQueue')->count();
        $command = ProcessExtend::think("xtask:listen");
        if (count($result = ProcessExtend::query($command)) > 0) {
            $output->info("异步任务监听主进程{$result['0']['pid']}已经启动！");
        } else {
            ProcessExtend::create($command);
            sleep(1);
            if (count($result = ProcessExtend::query($command)) > 0) {
                $output->info("异步任务监听主进程{$result['0']['pid']}启动成功！");
            } else {
                $output->error('异步任务监听主进程创建失败！');
            }
        }
    }
}
