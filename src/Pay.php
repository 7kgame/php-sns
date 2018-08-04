<?php
namespace QKPHP\SNS;

use \QKPHP\Common\Utils\Http;
use \QKPHP\Common\Utils\Utils;
use \QKPHP\SNS\Weixin;

class Pay {

  private $mch_config;
  public function __construct($options) {
    $this->mch_config = $options;
  }

  public function transfer ($data) {
    $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
    $pre = array(
      'mch_appid' => $this->mch_config['mch_appid'],
      'mchid' => $this->mch_config['mchid'],
      'nonce_str' => Weixin::createNonceStr(32),
      'check_name' =>'NO_CHECK',
      'openid' => $data['openid'],
      'partner_trade_no' => $data['partner_trade_no'],
      'amount' => $data['money']*100,
      'desc' => $data['desc'],
      'spbill_create_ip' => $this->mch_config['spbill_create_ip']
    );
    $secrect_key = $this->mch_config['secrect_key'];
    $pre['sign'] = Weixin::getSignature($pre, $secrect_key);
    $options['ca'] = array(
      'cert_type' => 'PEM',
      'cert_path' => $this->mch_config['cert_path'],
      'key_type' => 'PEM',
      'key_path' => $this->mch_config['key_path']
    );
    $param = Utils::toXML($pre);
    list($errorcode, $content) = Http::post($url, $param, $options);
    $res = Utils::xmlToArr($content);
    return array($errorcode,$res);
  }

}
