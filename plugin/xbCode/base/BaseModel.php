<?php
namespace plugin\xbCode\base;

use think\Model;

/**
 * 基类模型
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class BaseModel extends Model
{
    /**
     * 开启自动时间戳
     * @var string
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 创建时间字段
     * @var string
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $createTime = 'create_at';

    /**
     * 更新时间字段
     * @var string
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $updateTime = 'update_at';
}