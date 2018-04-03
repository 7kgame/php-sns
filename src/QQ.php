<?php

namespace QKPHP\SNS;

use \QKPHP\Common\Utils\Http;

class QQ {
  const AUTH_API = 'https://graph.qq.com/oauth2.0/authorize';
  const ACCESS_TOKEN_API = 'https://graph.qq.com/oauth2.0/token';
  const OPENID_API = 'https://graph.qq.com/oauth2.0/me';
  const USERINFO_API = 'https://graph.qq.com/user/get_user_info';

  public $appId;
  public $appSecret;

  public $openId;
  public $unionid;
  public $user;

  private $accessToken;

  public function __construct ($appId, $appSecret, array $config=null) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
    if (!empty($config)) {
      foreach ($config as $k=>$v) {
        $this->$k = $v;
      }
    }
  }

  public function toAuth ($redirect, $useInMobile=true, $state=null, $scope=null) {
    if (empty($state)) {
      $state = time();
    }
    if (empty($scope)) {
      $scope = 'get_user_info';
    }
    $display = $useInMobile ? 'mobile' : '';
    
    return self::AUTH_API .
      '?response_type=code' . 
      '&client_id=' . $this->appId .
      '&redirect_uri=' . urlencode($redirect) . 
      "&state=$state&scope=$scope&display=$display";
  }

  public function getAccessToken ($code, $redirect) {
    if (!empty($this->accessToken)) {
      return $this->accessToken;
    }
    $querys = array(
      'grant_type' => 'authorization_code',
      'client_id'  => $this->appId,
      'client_secret' => $this->appSecret,
      'code' => $code,
      'redirect_uri' => $redirect
    );
    list($status, $content) = Http::get(self::ACCESS_TOKEN_API, $querys);
    if (empty($content)) {
      return null;
    }
    $content = json_decode($content, true);
    if (empty($content) || !isset($content['access_token'])) {
      return null;
    }
    $accessToken = $content['access_token'];

    $querys = array(
      'access_token' => $accessToken
    );
    list($status, $content) = Http::get(self::OPENID_API, $querys);
    if (empty($content)) {
      return null;
    }
    $content = preg_replace("callback(", "", $content);
    $content = trim(preg_replace(");", "", $content));
    $content = json_decode($content, true);
    if (empty($content) || !isset($content['openid'])) {
      return null;
    }
    $this->openId = $content['openid'];
    $this->accessToken = $accessToken;
    return $accessToken;
  }

  public function getUserInfo () {
    $querys = array(
      'access_token' => $this->accessToken,
      'oauth_consumer_key' => $this->appId,
      'openid' => $this->openId
    );
    list($status, $content) = Http::get(self::USERINFO_API, $querys);
    if (empty($content)) {
      return null;
    }
    $content = json_decode($content, true);
    if (empty($content) || !isset($content['nickname'])) {
      return null;
    }
    $this->user = array(
      'name' => $content['nickname']
    );
    return $this->user;
  }
}
