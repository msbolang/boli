<?php
/*
namespace app\components;

use Yii;
use yii\base\Component;
 * 
 */
  //原生改寫
include_once 'Gtoken.php';
Class Wechattoken {

    public $appid;
    public $secret;
    public $getToken;
    public function __construct() {
        date_default_timezone_set('Asia/Shanghai');
     //   $this->appid = 'wxbd79232e5db670ed';
      //  $this->secret = '998669c7ba39cd6100642253d90491ff';
        $this->appid = 'wxc10920f516aaf18f';
        $this->secret = 'abb34b96da1bf7182773ed5cf9de82bf';
        $this->getToken = new Gtoken();
    }

    public function Token() {
       //原生改寫
     
        $token_str = $this->getToken->readfile();
      //  $token_str = Yii::$app->gettoken->readfile();
        if (!$token_str) {
            $Gtoken = $this->get_token_jc();
            $Gtoken->token_time = time();
            //原生改寫
       
            $this->getToken->writefile(json_encode($Gtoken));
            //Yii::$app->gettoken->writefile(json_encode($Gtoken));
            $model = $Gtoken;
        } else {
            $model = json_decode($token_str);
        }

        if ($model->access_token) {
           if ((time() - $model->token_time) >= $model->expires_in) {
                $Gtoken_ = $this->get_token_jc();
                $Gtoken_->token_time = time();
                 //原生改寫
        
          $saveToken =   $this->getToken->writefile(json_encode($Gtoken_));
             //   $saveToken = Yii::$app->gettoken->writefile(json_encode($Gtoken_));
                if ($saveToken) {
                    return $Gtoken_->access_token;
                }
            } else {
                return $model->access_token;
            }
        } else {
            $Gtoken_ = $this->get_token_jc();
            $Gtoken_->token_time = time();
             //原生改寫
        
            $saveToken = $this->getToken->writefile(json_encode($Gtoken_));
         //   $saveToken = Yii::$app->gettoken->writefile(json_encode($Gtoken_));
            if ($saveToken) {
                return $Gtoken_->access_token;
            }
        }
    }

    private function get_token_jc() {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret";
        $obj = $this->curl_get_contents($url);
        return json_decode($obj);
    }

    private function curl_get_contents($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        @curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        @curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

}

?>