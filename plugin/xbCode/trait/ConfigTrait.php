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
namespace plugin\xbCode\trait;

use Exception;
use plugin\xbCode\api\Url;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\ConfigView;

/**
 * 系统配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ConfigTrait
{
    /**
     * 普通表单配置项
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function normalConfig(string $group = '')
    {
        if(empty($group)) {
            $plugin = request()->plugin;
            $action = request()->action;
            $group = "{$plugin}/{$action}";
        }
        if (!method_exists($this, 'success') || !method_exists($this, 'fail')) {
            throw new Exception('必须在控制器内引入使用');
        }
        if (request()->method() === 'PUT') {
            $post = request()->post();
            // 保存配置
            ConfigApi::set($group,$post);
            // 返回数据
            return $this->success('保存成功');
        }
        $formData = ConfigApi::get($group, [], [
            'layer' => false,
            'replace' => false,
        ]);
        // 获取配置数据
        $formData = ConfigApi::get("{$group}.*", []);
        // 获取表单视图
        $builder = ConfigView::formView($group, $group);
        $builder->useForm()->wrapWithPanel(false);
        $saveAPI = Url::make("Setting/config/{$group}");
        $builder->setApi($saveAPI);
        $builder->setMethod('PUT');
        $builder->setData($formData);
        return $this->successRes($builder);
    }
}