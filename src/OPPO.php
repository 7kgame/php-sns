<?php

namespace QKPHP\SNS;

use \QKPHP\Common\Utils\Http;

class OPPO {

  public function __construct ($appId, $appSecret, array $config=null) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
    if (!empty($config)) {
      foreach ($config as $k=>$v) {
        $this->$k = $v;
      }
    }
  }

  public static function convert (array $user) {
    return array(
      'openId' => $user['uid'],
      'unionId' => '',
      'name' => $user['name'],
      'sex'  => $user['sex'] == 'F' ? 2 : 1,
      'avatar' => empty($user['headIcon']) ? '' : $user['headIcon'],
      'mobile' => isset($user['mobile']) ? $user['mobile'] : '',
      'city' => isset($user['city']) ? $user['city'] : '',
      'province' => isset($user['province']) ? $user['province'] : '',
      'country' => isset($user['country']) ? $user['country'] : '',
      'birthday' => isset($user['year']) ? $user['year'] : ''
    );
  }
}
