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

namespace think\admin;

use think\admin\helper\DeleteHelper;
use think\admin\helper\FormHelper;
use think\admin\helper\PageHelper;
use think\admin\helper\QueryHelper;
use think\admin\helper\SaveHelper;
use think\admin\helper\TokenHelper;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use think\exception\HttpResponseException;
use think\Request;

/**
 * 标准控制器基类
 * Class Controller
 * @package think\admin
 */
class Controller extends \stdClass
{

    /**
     * 应用容器
     * @var App
     */
    public $app;

    /**
     * 请求对象
     * @var Request
     */
    public $request;

    /**
     * 表单CSRF验证状态
     * @var boolean
     */
    public $csrf_state = false;

    /**
     * 表单CSRF验证失败提示
     * @var string
     */
    public $csrf_message = '表单令牌验证失败，请刷新页面再试！';

    /**
     * Controller constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
        if (in_array($this->app->request->action(), get_class_methods(__CLASS__))) {
            $this->error('Access without permission.');
        }
        $this->initialize();
    }

    /**
     * 控制器初始化
     */
    protected function initialize()
    {
        $this->request = request();
        $action = $this->request->action();
        $method = strtolower($this->request->method());
        if (method_exists($this, $callback = "_{$action}_{$method}")) {
            call_user_func_array([$this, $callback], $this->request->route());
        }
    }

    /**
     * 返回失败的操作
     * @param mixed $info 消息内容
     * @param array $data 返回数据
     * @param integer $code 返回代码
     */
    public function error($info, $data = [], $code = 0)
    {
        throw new HttpResponseException(json([
            'code' => $code, 'info' => $info, 'data' => $data,
        ]));
    }

    /**
     * 返回成功的操作
     * @param mixed $info 消息内容
     * @param array $data 返回数据
     * @param integer $code 返回代码
     */
    public function success($info, $data = [], $code = 1)
    {
        if ($this->csrf_state) {
            TokenHelper::instance($this, $this->app)->clear();
        }
        throw new HttpResponseException(json([
            'code' => $code, 'info' => $info, 'data' => $data,
        ]));
    }

    /**
     * URL重定向
     * @param string $url 跳转链接
     * @param integer $code 跳转代码
     */
    public function redirect($url, $code = 301)
    {
        throw new HttpResponseException(redirect($url, $code));
    }

    /**
     * 返回视图内容
     * @param string $tpl 模板名称
     * @param array $vars 模板变量
     * @param string $node CSRF授权节点
     */
    public function fetch($tpl = '', $vars = [], $node = null)
    {
        foreach ($this as $name => $value) $vars[$name] = $value;
        if ($this->csrf_state) {
            TokenHelper::instance($this, $this->app)->fetchTemplate($tpl, $vars, $node);
        } else {
            throw new HttpResponseException(view($tpl, $vars));
        }
    }

    /**
     * 模板变量赋值
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        if (is_string($name)) {
            $this->$name = $value;
        } elseif (is_array($name)) {
            foreach ($name as $k => $v) {
                if (is_string($k)) {
                    $this->$k = $v;
                }
            }
        }
        return $this;
    }

    /**
     * 数据回调处理机制
     * @param string $name 回调方法名称
     * @param mixed $one 回调引用参数1
     * @param mixed $two 回调引用参数2
     * @return boolean
     */
    public function callback($name, &$one = [], &$two = [])
    {
        if (is_callable($name)) return call_user_func($name, $this, $one, $two);
        foreach ([$name, "_{$this->app->request->action()}{$name}"] as $method) {
            if (method_exists($this, $method) && false === $this->$method($one, $two)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 快捷查询逻辑器
     * @param string|Query $dbQuery
     * @return QueryHelper
     */
    protected function _query($dbQuery)
    {
        return QueryHelper::instance($this, $this->app)->init($dbQuery);
    }

    /**
     * 快捷分页逻辑器
     * @param string|Query $dbQuery
     * @param boolean $page 是否启用分页
     * @param boolean $display 是否渲染模板
     * @param boolean $total 集合分页记录数
     * @param integer $limit 集合每页记录数
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function _page($dbQuery, $page = true, $display = true, $total = false, $limit = 0)
    {
        return PageHelper::instance($this, $this->app)->init($dbQuery, $page, $display, $total, $limit);
    }

    /**
     * 快捷表单逻辑器
     * @param string|Query $dbQuery
     * @param string $template 模板名称
     * @param string $field 指定数据对象主键
     * @param array $where 额外更新条件
     * @param array $data 表单扩展数据
     * @return array|boolean
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function _form($dbQuery, $template = '', $field = '', $where = [], $data = [])
    {
        return FormHelper::instance($this, $this->app)->init($dbQuery, $template, $field, $where, $data);
    }

    /**
     * 快捷更新逻辑器
     * @param string|Query $dbQuery
     * @param array $data 表单扩展数据
     * @param string $field 数据对象主键
     * @param array $where 额外更新条件
     * @return boolean
     * @throws DbException
     */
    protected function _save($dbQuery, $data = [], $field = '', $where = [])
    {
        return SaveHelper::instance($this, $this->app)->init($dbQuery, $data, $field, $where);
    }

    /**
     * 快捷删除逻辑器
     * @param string|Query $dbQuery
     * @param string $field 数据对象主键
     * @param array $where 额外更新条件
     * @return boolean|null
     * @throws DbException
     */
    protected function _delete($dbQuery, $field = '', $where = [])
    {
        return DeleteHelper::instance($this, $this->app)->init($dbQuery, $field, $where);
    }

    /**
     * 检查表单令牌验证
     * @param boolean $return 是否返回结果
     * @return boolean
     */
    protected function _applyFormToken($return = false)
    {
        return TokenHelper::instance($this, $this->app)->init($return);
    }

}
