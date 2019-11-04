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

namespace think\admin\helper;

use think\admin\Controller;
use think\App;
use think\db\Query;

/**
 * 基础管理器
 * Class Helper
 * @package think\admin\helper
 */
class Helper
{

    /**
     * 当前应用容器
     * @var App
     */
    public $app;

    /**
     * 当前控制器实例
     * @var Controller
     */
    public $class;

    /**
     * 数据库实例
     * @var Query
     */
    protected $query;

    /**
     * Helper constructor.
     * @param App $app
     * @param Controller $controller
     */
    public function __construct(Controller $controller, App $app)
    {
        $this->app = $app;
        $this->class = $controller;
    }

    /**
     * 获取数据库查询对象
     * @param string|Query $dbQuery
     * @return Query
     */
    protected function buildQuery($dbQuery)
    {
        return is_string($dbQuery) ? $this->app->db->name($dbQuery) : $dbQuery;
    }

    /**
     * 实例对象反射
     * @param Controller $controller
     * @param App $app
     * @return $this
     */
    public static function instance(Controller $controller, App $app)
    {
        return new static($controller, $app);
    }

}
