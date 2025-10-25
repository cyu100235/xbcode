<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\api;

use Exception;
use Webman\Event\Event;
use plugin\xbCrontab\app\model\Crontab;
use plugin\xbCrontab\app\validate\CrontabValidate;

/**
 * 定时任务接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabApi
{
    /**
     * 实例化
     * @return CrontabApi
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 获取任务执行表达式
     * 0   1   2   3   4   5
     * |   |   |   |   |   |
     * |   |   |   |   |   +------ 星期几 (0 - 6) (Sunday=0)
     * |   |   |   |   +------ 月 (1 - 12)
     * |   |   |   +-------- 天 of month (1 - 31)
     * |   |   +---------- 小时 (0 - 23)
     * |   +------------ 分钟 (0 - 59)
     * +-------------- 秒钟 (0-59)[可省略，如果没有0位,则最小时间粒度是分钟]
     * @param int $id
     * @throws Exception
     * @return string
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getExpression(int $id)
    {
        $model = Crontab::find($id);
        if (!$model) {
            throw new Exception('定时任务不存在');
        }
        if ($model->state === '10') {
            throw new Exception('定时任务已禁用');
        }
        if (!$model->state === '30') {
            throw new Exception('定时任务异常错误，无法执行');
        }
        $expression = "";
        switch ($model->unit) {
            // 秒钟
            case '10':
                break;
        }
        return $expression;
    }

    /**
     * 添加定时任务
     * @param array $post
     * @throws Exception
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(array $post)
    {
        xbValidate(CrontabValidate::class, $post, 'add');
        $model = new Crontab;
        if (!$model->save($post)) {
            throw new Exception('添加定时任务失败');
        }
        $data = $model->toArray();
        Event::dispatch('xbCrontab.Crontab.add', $data);
        return $data;
    }

    /**
     * 编辑定时任务
     * @param int $id
     * @param array $post
     * @throws Exception
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit(int $id, array $post)
    {
        xbValidate(CrontabValidate::class, $post, 'edit');
        $model = Crontab::find($id);
        if (!$model) {
            throw new Exception('定时任务不存在');
        }
        if (!$model->save($post)) {
            throw new Exception('编辑定时任务失败');
        }
        $data = $model->toArray();
        Event::dispatch('xbCrontab.Crontab.edit', $data);
        return $data;
    }

    /**
     * 删除定时任务
     * @param int $id
     * @throws Exception
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(int $id)
    {
        $model = Crontab::find($id);
        if (!$model) {
            throw new Exception('定时任务不存在');
        }
        $data = $model->toArray();
        if (!$model->delete()) {
            throw new Exception('删除定时任务失败');
        }
        Event::dispatch('xbCrontab.Crontab.del', $data);
        return $data;
    }

    /**
     * 获取插件定时任务配置
     * @param string $pluginName
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getPluginCrontab(string $pluginName)
    {
        $configPath = base_path() . "/plugin/{$pluginName}/config/crontab.php";
        if (!file_exists($configPath)) {
            return [];
        }
        $data = include $configPath;
        if (empty($data)) {
            return [];
        }
        return [];
    }

    /**
     * 安装插件定时任务
     * @param string $pluginName
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install(string $pluginName)
    {
        $data = $this->getPluginCrontab($pluginName);
        foreach ($data as $value) {
            if (is_array($value)) {
                continue;
            }
            $value['plugin'] = $pluginName;
            try {
                $this->add($value);
            } catch (\Throwable $th) {
                continue;
            }
        }
    }
    
    /**
     * 卸载定时任务
     * @param string $pluginName
     * @return bool
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall(string $pluginName)
    {
        return Crontab::where('plugin', $pluginName)->delete();
    }
}