<?php
namespace xbcode\service\xbcode;

use Exception;

/**
 * 项目服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ProjectService extends XbBaseService
{
    /**
     * 下载项目版本包
     * @param string $version
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function download(string $version)
    {
        $localVersion = config('projects.version');
        $service = static::request()->get('Projects/download',[
            'version' => $version,
            'local_version' => $localVersion
        ]);
        $content = $service->body();
        $result = $service->array();
        if (empty($content) && empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        return $content;
    }
}