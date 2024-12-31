<?php
namespace xbcode\service\bt;

use Exception;
use xbcode\providers\ConfigProvider;

/**
 * 宝塔服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class BtService
{
    /**
     * 宝塔面板地址
     * @var string
     */
    private $BT_PANEL;

    /**
     * 宝塔API密钥
     * @var string
     */
    private $BT_KEY;

    /**
     * 获取网站列表
     * @param array $query
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getWebSites(array $query = [])
    {
        $query    = array_merge([
            'p' => 1,
            'limit' => 15,
            'order' => 'id desc',
        ], $query);
        $result   = $this->reqeuest('data?action=getData&table=sites', $query);
        $datalist = empty($result['data']) ? [] : $result['data'];
        $datalist = (array) $datalist;
        return $datalist;
    }

    /**
     * 获取网站域名列表
     * @param int $id
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getDomains(int $id)
    {
        $query  = [
            'search' => $id,
            'list' => true,
        ];
        $result = $this->reqeuest('data?action=getData&table=domain', $query);
        if (is_array($result) && empty($result)) {
            throw new Exception('获取网站域名列表失败');
        }
        $datalist = (array) $result;
        return $datalist;
    }

    /**
     * 获取网站信息
     * @param string $host
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site(string $host)
    {
        $data    = $this->getWebSites();
        $domains = array_column($data, null, 'name');
        $result  = $domains[$host] ?? null;
        if (empty($result)) {
            return [];
        }
        return $result;
    }

    /**
     * 获取本站网站信息
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function siteInfo()
    {
        $host = request()->host();
        $data    = $this->site($host);
        return $data;
    }

    /**
     * 获取网站域名列表
     * @param int $id
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getDomainList(int $id)
    {
        $result = $this->getDomains($id);
        $data   = array_column($result, 'name');
        return $data;
    }
    
    /**
     * 添加网站域名
     * @param int $id
     * @param string $name
     * @param string $domain
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addDomain(int $id, string $name, string $domain)
    {
        $query  = [
            'id' => $id,
            'webname' => $name,
            'domain' => $domain,
        ]; 
        $result = $this->reqeuest('site?action=AddDomain', $query);
        if (empty($result)) {
            throw new Exception('添加网站域名失败');
        }
        return $result;
    }
    
    /**
     * 删除网站域名
     * @param int $id
     * @param string $name
     * @param string $domain
     * @param int $port
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function delDomain(int $id,string $name, string $domain, int $port = 80)
    {
        $query  = [
            'id' => $id,
            'webname' => $name,
            'domain' => $domain,
            'port' => $port,
        ]; 
        $result = $this->reqeuest('site?action=DelDomain', $query);
        return $result;
    }

    /**
     * 发送请求
     * @param string $api
     * @param array $data
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function reqeuest(string $api, array $data = [])
    {
        // 获取配置
        $this->config();
        //定义cookie保存位置
        $cookie     = md5($this->BT_PANEL) . '.cookie';
        $cookieFile = base_path() . '/runtime/cookie/' . $cookie;
        // 检测cookie目录是否存在
        if (!is_dir(dirname($cookieFile))) {
            mkdir(dirname($cookieFile), 0755, true);
        }
        // 检测cookie文件是否存在
        if (!file_exists($cookieFile)) {
            $fp = fopen($cookieFile, 'w+');
            fclose($fp);
        }
        // 请求地址
        $url = $this->BT_PANEL . $api;
        // 获取签名参数
        $data = array_merge($data, $this->GetKeyData());
        // 构造请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        // 转换数据结构
        $result = json_decode($output, true);
        // 返回结果
        return $result;
    }

    /**
     * 构造签名参数
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function GetKeyData()
    {
        $now_time = time();
        $p_data   = [
            'request_token' => md5($now_time . '' . md5($this->BT_KEY)),
            'request_time' => $now_time
        ];
        return $p_data;
    }

    /**
     * 获取宝塔配置
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function config()
    {
        $config = ConfigProvider::get('bt');
        if (empty($config)) {
            return [];
        }
        if (empty($config['bt_api_domain'])) {
            throw new Exception('请配置宝塔API域名');
        }
        if (empty($config['bt_api_key'])) {
            throw new Exception('请配置宝塔API密钥');
        }
        // 验证接口是否斜杠结尾
        if (!str_ends_with($config['bt_api_domain'], '/')) {
            throw new Exception('宝塔面板地址必须以斜杠结尾');
        }
        // 设置宝塔配置
        $this->BT_PANEL = $config['bt_api_domain'];
        $this->BT_KEY   = $config['bt_api_key'];
        // 返回配置
        return $config;
    }
}