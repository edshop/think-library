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

use think\db\Query;

/**
 * 搜索条件处理器
 * Class QueryHelper
 * @package think\admin\helper
 * @see \think\Db\Query
 * @mixin \think\Db\Query
 */
class QueryHelper extends Helper
{

    /**
     * Query call.
     * @param string $name 调用方法名称
     * @param array $args 调用参数内容
     * @return $this
     */
    public function __call($name, $args)
    {
        if (is_callable($callable = [$this->query, $name])) {
            call_user_func_array($callable, $args);
        }
        return $this;
    }

    /**
     * 逻辑器初始化
     * @param string|Query $dbQuery
     * @return $this
     */
    public function init($dbQuery)
    {
        $this->query = $this->buildQuery($dbQuery);
        return $this;
    }

    /**
     * 获取当前Db操作对象
     * @return \think\db\Query
     */
    public function db()
    {
        return $this->query;
    }

    /**
     * 设置Like查询条件
     * @param string|array $fields 查询字段
     * @param string $input 输入类型 get|post
     * @param string $alias 别名分割符
     * @return $this
     */
    public function like($fields, $input = 'request', $alias = '#')
    {
        $data = $this->app->request->$input();
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query->whereLike($dk, "%{$data[$qk]}%");
            }
        }
        return $this;
    }

    /**
     * 设置Equal查询条件
     * @param string|array $fields 查询字段
     * @param string $input 输入类型 get|post
     * @param string $alias 别名分割符
     * @return $this
     */
    public function equal($fields, $input = 'request', $alias = '#')
    {
        $data = $this->app->request->$input();
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query->where($dk, "{$data[$qk]}");
            }
        }
        return $this;
    }

    /**
     * 设置IN区间查询
     * @param string $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $input 输入类型 get|post
     * @param string $alias 别名分割符
     * @return $this
     */
    public function in($fields, $split = ',', $input = 'request', $alias = '#')
    {
        $data = $this->app->request->$input();
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query->whereIn($dk, explode($split, $data[$qk]));
            }
        }
        return $this;
    }

    /**
     * 设置内容区间查询
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $input 输入类型 get|post
     * @param string $alias 别名分割符
     * @return $this
     */
    public function valueBetween($fields, $split = ' ', $input = 'request', $alias = '#')
    {
        return $this->setBetweenWhere($fields, $split, $input, $alias);
    }

    /**
     * 设置日期时间区间查询
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $input 输入类型
     * @param string $alias 别名分割符
     * @return $this
     */
    public function dateBetween($fields, $split = ' - ', $input = 'request', $alias = '#')
    {
        return $this->setBetweenWhere($fields, $split, $input, $alias, function ($value, $type) {
            if ($type === 'after') {
                return "{$value} 23:59:59";
            } else {
                return "{$value} 00:00:00";
            }
        });
    }

    /**
     * 设置时间戳区间查询
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $input 输入类型
     * @param string $alias 别名分割符
     * @return $this
     */
    public function timeBetween($fields, $split = ' - ', $input = 'request', $alias = '#')
    {
        return $this->setBetweenWhere($fields, $split, $input, $alias, function ($value, $type) {
            if ($type === 'after') {
                return strtotime("{$value} 23:59:59");
            } else {
                return strtotime("{$value} 00:00:00");
            }
        });
    }

    /**
     * 设置区域查询条件
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $input 输入类型
     * @param string $alias 别名分割符
     * @param callable $callback
     * @return $this
     */
    private function setBetweenWhere($fields, $split = ' ', $input = 'request', $alias = '#', $callback = null)
    {
        $data = $this->app->request->$input();
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                list($begin, $after) = explode($split, $data[$qk]);
                if (is_callable($callback)) {
                    $after = call_user_func($callback, $after, 'after');
                    $begin = call_user_func($callback, $begin, 'begin');
                }
                $this->query->whereBetween($dk, [$begin, $after]);
            }
        }
        return $this;
    }

    /**
     * 实例化分页管理器
     * @param boolean $isPage 是否启用分页
     * @param boolean $isDisplay 是否渲染模板
     * @param boolean $total 集合分页记录数
     * @param integer $limit 集合每页记录数
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function page($isPage = true, $isDisplay = true, $total = false, $limit = 0)
    {
        return PageHelper::instance($this->class, $this->app)->init($this->query, $isPage, $isDisplay, $total, $limit);
    }
}
