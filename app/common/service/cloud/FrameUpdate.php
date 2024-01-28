<?php

namespace app\common\service\cloud;
use app\common\service\SystemService;
use Exception;

/**
 * 框架升级服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait FrameUpdate
{
    /**
     * 获取框架升级信息
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFrameUpdate()
    {
        $systemInfo = SystemService::info();
        $params = [
            'version_name'  => $systemInfo['version_name'],
            'version'       => $systemInfo['version'],
        ];
        return self::send('Frame/getUpdate',$params)->array();
    }
    
    /**
     * 下载框架升级包
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function downloadFrame(int $version,string $file)
    {
        $params = [
            'version'       => $version,
        ];
        $content = self::send('Frame/download', $params);
        // 写入文件储存
        file_put_contents($file, $content);
    }

    /**
     * 获取框架升级日志
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFrameLogList()
    {
        return self::send('Frame/index')->array();
    }
    
    /**
     * 获取框架授权信息
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFrameAuth()
    {
        $data = self::send('Frame/auth')->array();
        if (empty($data)) {
            throw new Exception('获取授权信息失败，接口异常');
        }
        if ($data['code'] != 200) {
            throw new Exception($data['msg']);
        }
        $data['data']['system_info'] = SystemService::info();
        return $data;
    }
}
