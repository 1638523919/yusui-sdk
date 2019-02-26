<?php
namespace think\yusui;

use think\yusui\Tools;
include_once 'Tools.php';
/**
 * 鱼穗SDK
 * Class YuSui
 * @package yusui
 */
class YuSui
{
    private $appId = '';
    private $appKey = '';
    private $apiDomain = '';

    /**
     * YuSui constructor.
     */
    public function __construct()
    {
        include_once 'Config.php';
        $this->appId = \think\yusui\Config::APP_ID;
        $this->appKey = \think\yusui\Config::APP_KEY;
        $this->apiDomain = \think\yusui\Config::API_DOMAIN;
    }

    /**
     * 用户查询，查询用户手机号是否在当前企业中
     * @param $phone * 手机
     * @return string
     * @throws \Exception
     */
    public function checkUser($phone)
    {
        if (!$phone) {
            throw new \Exception('参数错误');
        }
        $url = $this->apiDomain . '/open/user/check';
        $param = ['phone' => $phone];
        $postData = $this->postDefaultData($param);
        $result = Tools::postCurl($postData, $url);
        return json_decode($result, true);
    }

    /**
     * 添加用户，添加用户并绑定到当前企业下，如已存在用户则直接绑定
     * @param $phone * 手机
     * @param $idCard * 身份证号码
     * @param $realName * 身份证姓名
     * @param $dept * 添加到的部门名称
     * @return string
     * @throws \Exception
     */
    public function bindUser($phone, $idCard, $realName, $dept)
    {
        if (!$phone || !$idCard || !$realName || !$dept) {
            throw new \Exception('参数错误');
        }
        $url = $this->apiDomain . '/open/user/bind';
        $param = [
            'phone' => $phone,
            'idcard' => $idCard,
            'realname' => $realName,
            'dept' => $dept,
        ];
        $postData = $this->postDefaultData($param);
        $result = Tools::postCurl($postData, $url);
        return json_decode($result, true);
    }

    /**
     * 移除用户关系，将用户从当前企业中移除
     * @param $phone * 手机
     * @return string
     * @throws \Exception
     */
    public function unBindUser($phone)
    {
        if (!$phone) {
            throw new \Exception('参数错误');
        }
        $url = $this->apiDomain . '/open/user/unbind';
        $param = ['phone' => $phone];
        $postData = $this->postDefaultData($param);
        $result = Tools::postCurl($postData, $url);
        return json_decode($result, true);
    }

    /**
     * 发包，发包给已绑定的用户，请在保证企业资金充足下发包
     * @param $phone * 手机
     * @param $type * 外包项目类型
     * @param $amount * 金额,单位:分,最小值:10
     * @param $outNo * 企业内部编号,同一企业唯一 只能是数字/大小写字母
     * @param $name * 发包名称(事由)
     * @param $mode * 服务费扣除模式: 1 企业支付,2 用户扣除
     * @param string $desc 本次发包备注
     * @param string $attach 附加数据
     * @return string
     * @throws \Exception
     */
    public function send($phone, $type, $amount, $outNo, $name, $mode, $desc = '', $attach = '')
    {
        if (!$phone || !$type || !$amount || !$outNo || !$name || !$mode) {
            throw new \Exception('参数错误');
        }
        $url = $this->apiDomain . '/open/package/send';
        $amountData = [
            'phone' => $phone,
            'type' => $type,
            'amount' => $amount
        ];
        if ($desc) {
            $amountData['desc'] = $desc;
        }
        $data[] = $amountData;
        $param = [
            'out_no' => $outNo,
            'name' => $name,
            'desc' => $desc,
            'mode' => $mode,
            'attach' => $attach,
            'data' => json_encode($data),
        ];
        $postData = $this->postDefaultData($param);
        $result = Tools::postCurl($postData, $url);
        return json_decode($result, true);
    }

    /**
     * 发包查询，发包给用户,当企业发包后,没有收到鱼穗系统的响应,为了保证数据的一致性,可以调用该接口用以核对数据
     * 注！企业内部编号 out_no 与 鱼穗系统发包编号 no 共存时以 鱼穗系统发包编号 no 优先
     * @param string $no 鱼穗系统发包编号
     * @param string $outNo 企业内部编号
     * @return string
     * @throws \Exception
     */
    public function search($no = '', $outNo = '')
    {
        if (!$no && !$outNo) {
            throw new \Exception('参数错误');
        }
        $url = $this->apiDomain . '/open/package/search';
        $param = [
            'no' => $no,
            'out_no' => $outNo
        ];
        $postData = $this->postDefaultData($param);
        $result = Tools::postCurl($postData, $url);
        return json_decode($result, true);
    }

    /**
     * 默认post的参数，生成签名
     * @param $param post的参数
     * @return mixed
     */
    private function postDefaultData($param)
    {
        $param['appid'] = $this->appId;
        $param['random'] = Tools::getRandomString();
        $postData = $param;
        $postData['sign'] = Tools::createSign($param, $this->appKey);
        return $postData;
    }
}