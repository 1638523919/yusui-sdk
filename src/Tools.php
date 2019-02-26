<?php
namespace OrangeMan\YuSui;

/**
 * 工具类
 * Class Tools
 * @package OrangeMan\YuSui
 */
class Tools
{
    /**
     * 以post方式提交data到对应的接口url
     * @param array $data 需要post的数据
     * @param $url
     * @param int $second url执行超时时间，默认30s
     * @return mixed
     * @throws \Exception
     */
    public static function postCurl(array $data, $url, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //运行curl
        $result = curl_exec($ch);
        //返回结果
        if (!$result) {
            $error = curl_errno($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo(curl_error($ch));
            curl_close($ch);
            throw new \Exception('curl出错，错误码:' . $error . ',http code:' . $httpCode);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string 产生的随机字符串
     */
    public static function getRandomString($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++ ) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 生成签名
     * @param $param
     * @param $appKey
     * @return string
     */
    public static function createSign($param, $appKey)
    {
        ksort($param);
        $signString = self::toUrlParams($param);
        $signString = $signString . '&appkey=' . $appKey;
        $sign = md5($signString);
        return strtoupper($sign);
    }

    /**
     * 格式化参数格式化成url参数
     * @param $param
     * @return string
     */
    public static function toUrlParams($param)
    {
        $buff = "";
        foreach ($param as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}