<?php
/*
namespace app\components;

use Yii;
use yii\base\Component;
 * 
 */
  //原生改寫
include_once 'Gtoken.php';
include_once 'Wechattoken.php';
Class Ticket {

    public $appid;
    public $secret;
    public $getToken;
    public $ticketSTR;
    public $gameUrl;
     public $SgameUrl;
     public $IndexTitle;
    
    public function __construct() {
        date_default_timezone_set('Asia/Shanghai');
        //测试
      //  $this->appid = 'wxbd79232e5db670ed';
      //  $this->secret = '998669c7ba39cd6100642253d90491ff';
        
        //正式
       $this->appid = 'wxc10920f516aaf18f';
       $this->secret = 'abb34b96da1bf7182773ed5cf9de82bf';
       $this->IndexTitle = '';
    
        if($_SESSION['isGuide_USR']){
                       $this->setUrlTO(); 
        }else{
            $this->SgameUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
        }
        
         $this->gameUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
         
         
         
         
         
        $this->getToken = new Gtoken();
    }

    public function setUrlTO(){
      //  $Url = http://'.$_SERVER['HTTP_HOST']
         
       
          if($_SERVER['REQUEST_URI']=='' || $_SERVER['REQUEST_URI']=='/' || $_SERVER['REQUEST_URI']=='/index.php'|| $_SERVER['REQUEST_URI']=='/index'){
            $this->SgameUrl = 'http://'. $_SERVER['HTTP_HOST'].'/index.php?&my_classify_id='. $_SESSION['isGuide_USR'];
            $this->IndexTitle = '互赢国际全球直购免税商城';
            
          }
        
          
           if(strstr($_SERVER['REQUEST_URI'],'/index/') || strstr($_SERVER['REQUEST_URI'],'/category/') || strstr($_SERVER['REQUEST_URI'],'/simple/') ){
          
                $this->SgameUrl = 'http://'. $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/my_classify_id/'. $_SESSION['isGuide_USR'];
           }
           
           if(strstr($_SERVER['REQUEST_URI'],'/index.php?')){
              
                $this->SgameUrl = 'http://'. $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&my_classify_id='. $_SESSION['isGuide_USR'];
           }
        
      
        
    }




    public function Token() {
       //原生改寫
        $tokArr = array();
        $tokArr['time'] = time();
        $tokArr['randstr'] = $this->getrandstr(10);
        $tokArr['signa'] = $this->signatureticket($tokArr['time'],$tokArr['randstr']);
        $tokArr['ticketSTR'] = $this->ticketSTR;
        $tokArr['gameUrl'] = $this->gameUrl;
         $tokArr['SgameUrl'] = $this->SgameUrl;
        $tokArr['IndexTitle'] = $this->IndexTitle;
        return $tokArr;
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

    
  private function getrandstr($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;
    for($i=0;$i<$length;$i++){
     $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }
    return $str;
 } 
 
 
   private function signatureticket($time_,$str_){
       
       $token_str = $this->getToken->readfile('t');

      //  $token_str = Yii::$app->gettoken->readfile('t');
        if (!$token_str) {
            $Gtoken = $this->get_ticket();
            $Gtoken->token_time = time();
            //原生改寫
            $this->getToken->writefile(json_encode($Gtoken),'t');
            //Yii::$app->gettoken->writefile(json_encode($Gtoken),'t');
             $this->ticketSTR = $Gtoken->ticket;
            $model = $Gtoken;
        } else {
            $model = json_decode($token_str);
            $this->ticketSTR = $model->ticket;
        }
 

        if($model->ticket){
             if ((time() - $model->ticket_time) >= $model->expires_in) {
               $ticketID = $this->get_ticket();
               $ticketID->ticket_time = time();
               
                 //原生改寫
             $this->ticketSTR = $ticketID->ticket;
                $saveToken =   $this->getToken->writefile(json_encode($ticketID),'t');
               if($saveToken){
                   $signat = $this->signatureticket_end($time_,$str_,$ticketID->ticket);
                    return $signat;
                }
             }else{
                   $signat = $this->signatureticket_end($time_,$str_,$model->ticket);
                   return $signat;
             }
        }else{
               $ticketID = $this->get_ticket();
               $ticketID->ticket_time = time();
                  $this->ticketSTR = $ticketID->ticket;
                  $saveToken = $this->getToken->writefile(json_encode($ticketID),'t');
               if($saveToken){
                   $signat = $this->signatureticket_end($time_,$str_,$ticketID->ticket);
                   return $signat;
               }
        }
          
   }
   
   
       
    private function signatureticket_end($time,$str,$ticket){
        $wxOri = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $ticket, $str, $time, $this->gameUrl);
        $wxSha1 = sha1($wxOri);
        return $wxSha1;
    }


   private function get_ticket(){
       $Gtoken = new Wechattoken();
       $token =$Gtoken->Token();
       $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
       $obj = $this->curl_get_contents($url);
    
       return json_decode($obj);
   }
 
}

?>