<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\engine;

use Exception;
use plugin\xbAdmin\app\model\Config;
use plugin\xbAdmin\service\Server;

/**
 * 本地文件驱动
 *
 * 把文件保存到 public 目录下，访问地址由站点域名拼接文件相对路径得到。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class LocalServer extends Server
{
    /**
     * 系统域名缓存
     * @var string|null
     */
    protected static ?string $siteDomain = null;

    /**
     * 上传文件到本地目录
     * @param string $save_dir 上传目录路径
     * @param array|null $callback 上传回调函数
     * @throws Exception
     * @return array
     */
    public function upload(string $save_dir, ?array $callback = null)
    {
        // 上传目录
        $path = public_path() . '/' . $save_dir;
        // 检测目录是否存在
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        // 储存文件地址
        $filePath = "{$path}/{$this->fileName}";
        // 验证文件并上传
        $info = $this->file->move($filePath);
        if (empty($info)) {
            throw new Exception('文件上传失败');
        }
        return [];
    }

    /**
     * 检测文件是否存在
     * @param string $filePath 文件路径
     * @return bool
     */
    public function exist(string $filePath)
    {
        return file_exists(public_path($filePath));
    }

    /**
     * 获取文件URL
     * @param string $filePath
     * @return string
     */
    public function url(string $filePath)
    {
        return $this->format($this->domain(), $filePath);
    }

    /**
     * 获取签名URL
     * @param string $filePath 文件地址
     * @return string
     */
    public function getSignUrl(string $filePath)
    {
        return $this->url($filePath);
    }

    /**
     * 获取服务域名
     * @return string
     */
    public function domain()
    {
        // 优先使用后台配置的站点域名
        $domain = static::getSiteDomain();
        if ($domain !== '') {
            return $domain;
        }
        // 未配置时按当前请求推导
        $request = request();
        if ($request) {
            $schema = $request->header('x-forwarded-proto') === 'https' ? 'https' : 'http';
            return "{$schema}://" . $request->host();
        }
        return '';
    }

    /**
     * 读取后台配置的站点域名
     * @return string
     */
    protected static function getSiteDomain(): string
    {
        if (static::$siteDomain !== null) {
            return static::$siteDomain;
        }
        $domain = '';
        try {
            $config = Config::groupValues('xbAdmin', 'system');
            $domain = (string) ($config['web_url'] ?? '');
        } catch (\Throwable $e) {
            $domain = '';
        }
        static::$siteDomain = $domain;
        return $domain;
    }

    /**
     * 抓取网络资源
     * @param string $url
     * @param mixed $key
     * @return void
     */
    public function fetch(string $url, $key = null)
    {
    }

    /**
     * 删除文件
     * @param string $fileName
     * @return mixed
     */
    public function delete($fileName)
    {
        // 去掉路径开头的斜杠，统一通过 public_path 定位到 public 目录下
        $filePath = public_path(ltrim((string) $fileName, '/'));
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }

    /**
     * 设置文件名
     * @param string $fileName
     * @return void
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * 返回文件路径
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}