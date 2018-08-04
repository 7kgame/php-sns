<?php
namespace QKPHP\SNS;

use \QKPHP\Common\Config\Config;
use \QKPHP\Common\Utils\Http;
use \QKPHP\Common\Utils\Utils;
use \QKPHP\SNS\Weixin;

class Message {

  public static function send ($conf, $data) {
    if(!isset($conf['appId']) || !isset($conf['appSecret'])){
      return array(-1, '配置信息不正确');
    }
    if(!isset($data['openid']) || !isset($data['template_id']) || !isset($data['page']) 
      || !isset($data['formid']) || !isset($data['name']) || !isset($data['desc'])){
      return array(-1, '参数不正确');
    }
    $wxHelper = new Weixin($conf['appId'], $conf['appSecret']);
    $accessToken = $wxHelper->getAccessToken ();
    if(!empty($accessToken)){
      $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$accessToken";
      $params = array(
        'touser' => $data['openid'],
        'template_id' => $data['template_id'],
        'page' => $data['page'],
        'form_id' => $data['formid'],
        'data' => array(
          'keyword1' => array('value' => $data['name']),
          'keyword2' => array('value' => $data['desc'])
        )
      );
      list($errorcode, $content) = Http::post($url, json_encode($params), array());
      return array($errorcode, $content);
    }else{
      return array(-1, '获取accessToken失败');
    }
  }

}
