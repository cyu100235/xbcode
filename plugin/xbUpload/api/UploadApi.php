<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\api;

use Exception;
use Webman\Http\UploadFile;
use plugin\xbCode\api\ConfigApi;
use plugin\xbUpload\service\Driver;
use plugin\xbUpload\app\model\Upload;
use plugin\xbUpload\enum\UploadExtEnum;

/**
 * 附件接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UploadApi
{
    /**
     * 获取引擎SDK实例
     * @var Driver
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Driver $driver;

    /**
     * 获取上传适配器
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $adapter;

    /**
     * 获取上传配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $config;

    /**
     * 用户ID
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected int $uid = 0;


    /**
     * 是否保存记录
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $saveRecord = true;

    /**
     * 保存目录
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $saveDir = '';

    /**
     * 保存文件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $fileName = '';

    /**
     * 构造函数
     * @param string $adapter 上传适配器
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function __construct(string $adapter = '')
    {
        if (empty($adapter)) {
            $adapter = ConfigApi::make('upload')->get('active', 'local');
        }
        $config = EngineApi::make()->getConfig($adapter);
        $this->adapter = $adapter;
        $this->config = $config;
        $this->driver = Driver::make($config);
    }

    /**
     * 获取上传实例
     * @param string $adapter 上传适配器
     * @return UploadApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(string $adapter = '')
    {
        $class = new static($adapter);
        return $class;
    }

    /**
     * 上传文件
     * @param string $name 键名称
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function upload(string $name = 'file')
    {
        $file = request()->file($name);
        if (!$file instanceof UploadFile) {
            throw new Exception('上传文件不存在');
        }
        return $this->uploadFile($file);
    }

    /**
     * 执行文件上传
     * @param UploadFile $file
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uploadFile(UploadFile $file)
    {
        // 1.验证文件对象
        if (empty($file)) {
            throw new Exception('上传文件不存在');
        }
        // 2.设置上传文件
        $this->driver->setUploadFile($file);
        // 3.获取文件信息
        $fileInfo = $this->driver->getFileInfo();
        // 4.计算文件MD5值
        $md5 = md5_file($fileInfo['realPath']);
        if (empty($md5)) {
            throw new Exception('上传文件MD5值为空');
        }
        // 5.设置储存方式与文件方式MD5
        $md5 = $fileInfo['md5'];
        // 6.检测文件是否存在
        $where = [
            'md5' => $md5,
            'uid' => $this->uid,
            'adapter' => $this->adapter,
        ];
        $model = Upload::where($where)->find();
        // 7.如果文件存在则返回文件信息
        if ($model) {
            // 检测文件是否存在
            if ($this->driver->exist($model['uri'])) {
                $model->update_at = date('Y-m-d H:i:s');
                $model->value = Files::make()->url($model->uri);
                $model->save();
                $data = $model->toArray();
                return $this->getResult($data);
            }
        }
        // 8.获取文件信息
        $ext = $fileInfo['ext'] ?? '';
        if (empty($this->fileName)) {
            $this->fileName = $this->driver->getFileName();
        }
        // 9.获取上传文件储存路径
        if (empty($this->saveDir)) {
            $this->saveDir = $this->getUploadPath($ext);
        }
        // 10.设置上传文件名
        $this->driver->setFileName($this->fileName);
        // 11.执行文件上传
        $this->driver->upload($this->saveDir);
        // 12.组装文件信息
        $data = [
            'uid' => $this->uid ?? 0,
            'title' => $fileInfo['name'],
            'name' => $fileInfo['name'],
            'md5' => $md5,
            'size' => $fileInfo['size'],
            'format' => $ext,
            'adapter' => $this->adapter,
            'uri' => "{$this->saveDir}/{$this->fileName}",
        ];
        // 10.是否保存记录
        if ($this->saveRecord) {
            Upload::create($data);
        }
        return $this->getResult($data);
    }

    /**
     * 下载文件并保存文件
     * @param string $url
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function download(string $url)
    {
        // 1.验证下载地址
        if (empty($url)) {
            throw new Exception('下载地址不能为空');
        }
        // 2.验证下载地址是否URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('下载地址格式不正确');
        }
        // 3.保存至运行时临时目录
        $saveDir = runtime_path('/temp');
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }
        // 4.设置文件名
        if (empty($this->fileName)) {
            $this->fileName = basename($url);
        }
        // 5.执行文件下载
        $filePath = $saveDir . '/' . $this->fileName;
        file_put_contents($filePath, fopen($url, 'r'));
        // 6.上传文件
        return $this->uploadFilePath($filePath);
    }
    
    /**
     * 上传本地文件路径
     * @param string $path 文件路径
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uploadFilePath(string $path)
    {
        // 1.验证文件路径
        if (empty($path)) {
            throw new Exception('文件路径不能为空');
        }
        // 2.验证文件路径是否文件
        if (!is_file($path) || !file_exists($path)) {
            throw new Exception('文件路径不存在');
        }
        // 3.获取文件MIME类型
        $miniType = mime_content_type($path);
        // 4.获取文件名（带扩展名）
        $fileName = pathinfo($path, PATHINFO_BASENAME);
        // 5.设置文件对象
        $file = new UploadFile($path, $fileName, $miniType, 0);
        // 6.执行文件上传
        return $this->uploadFile($file);
    }

    /**
     * 设置保存目录
     * @param string $saveDir 保存目录
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setSaveDir(string $saveDir)
    {
        $this->saveDir = $saveDir;
        return $this;
    }

    /**
     * 设置文件名
     * @param string $fileName
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * 设置是否保存记录
     * @param bool $saveRecord 是否保存记录：true保存记录，false不保存记录
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setSaveRecord(bool $saveRecord)
    {
        $this->saveRecord = $saveRecord;
        return $this;
    }

    /**
     * 设置用户ID
     * @param int $uid 用户ID
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setUid(int $uid)
    {
        $this->uid = $uid;
        return $this;
    }


    /**
     * 获取文件信息
     * @param array $result
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getResult(array $result)
    {
        $url = Files::make()->url($result['uri']);
        return [
            'uid' => (int) $this->uid,
            'title' => $result['name'],
            'name' => $result['name'],
            'md5' => $result['md5'],
            'size' => $result['size'],
            'format' => $result['format'],
            'adapter' => $this->adapter,
            'uri' => $result['uri'],
            'url' => $url,
            'value' => $url,
            'link' => $url,
        ];
    }

    /**
     * 获取上传文件储存路径
     * @param string $extension
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getUploadPath(string $extension)
    {
        $name = self::getDictDirName($extension);
        return "attachment/{$name}/" . date('Ymd');
    }

    /**
     * 获取上传文件储存目录名
     * @param string $extension
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getDictDirName(string $extension)
    {
        $uploadFormat = UploadExtEnum::toArray();
        foreach ($uploadFormat as $value) {
            $format = explode(',', $value['ext'] ?? '');
            if (in_array($extension, $format)) {
                return $value['value'] ?? 'other';
            }
        }
        return 'other';
    }

    /**
     * 获取上传适配器
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * 设置上传适配器
     * @param string $adapter 上传适配器名称
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setAdapter(string $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * 获取上传实例
     * @return Driver
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getDriver()
    {
        return $this->driver;
    }
}