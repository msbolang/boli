<?php
//phpinfo();
error_reporting(E_ALL);
//traceHttp();
define("TOKEN", "tokenWX");

$wechatObj = new wechatCallbackapiTest();

if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
   // $wechatObj->responseMsg();
    $wechatObj->responseMsgtimeout();
}




class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
  
        if($this->checkSignature()){
            ob_clean();
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

   public function responseMsgtimeout()
    {
       if(isset($_GET['OpenID']))
       {   
           $info = $_GET;
           $tokenid = $this->getmytokenashx();
           $opsturl = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$tokenid;            
           $content = urlencode($info['message']);
           $text = array(
               "touser"=>$info['OpenID'],
               "msgtype"=>"text",
               "text"=>array(
                    "content"=>$content
               )
           );
          
      $isok =  $this->request_by_curl($opsturl, json_encode($text));  
    
      $newisok =json_decode($isok);
  //var_dump($newisok);echo 222;exit;
    //  echo $newisok->errmsg;
  //call me  http://m.top-booking.com/piao/tokenlog.php?OpenID=oT_0FwuCvXTu8By1WqVH5X0M8Yvs&message=%E4%BD%A0%E8%A6%81%E8%AE%B2%E7%9A%84%E8%AF%9D    
      }  
}   
    
 private function request_by_curl($opsturl, $post_string) {
   
  $ch = curl_init();
  @curl_setopt($ch, CURLOPT_URL, $opsturl);  
  @curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode($post_string));  
  @curl_setopt($ch, CURLOPT_TIMEOUT,60);   
  @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
  @curl_setopt($ch, CURLOPT_USERAGENT, "CURL Example beta");  
  @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $data = curl_exec($ch);
  traceHttp($data);
  curl_close($ch);
  
  return $data;  
}  
    
    
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            if($keyword == "?" || $keyword == "？")
            {
                $msgType = "text";
                $contentStr = date("Y-m-d H:i:s",time());
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }
        }else{
            echo "";
            exit;
        }
    }
    
    
    
    
    
    
    
    
   
    private function getmytokenashx(){
    $gettokenurl = 'http://hometest.easytonent.com:38080/TopHoliday/Interface/TopBooking/get_token.ashx';
        $gettokenurlinfo=$this->curl_get_contents($gettokenurl);
      
        $gettokenurlinfo= str_replace("zgtjsonpcallback(", "", $gettokenurlinfo);
        $gettokenurlinfo= str_replace(")", "", $gettokenurlinfo);
        $newgettokenurlinfo=json_decode($gettokenurlinfo);
        $access_token_app=$newgettokenurlinfo->JsonData; 
            
        
      
        if((time()-$access_token_app->time)>7200)
         {
            $access_token_app = $this->gettokenapp();
         };    
     return $access_token_app->access_token;
}




 private function curl_get_contents($url){
    
    $ch = curl_init();
     @curl_setopt($ch, CURLOPT_URL, $url);
     @curl_setopt($ch, CURLOPT_TIMEOUT, 30);
     @curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
     @curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
     @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
     @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
     @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   
    $r = curl_exec($ch);
 
    curl_close($ch);
    return $r;
}


  private function gettokenapp()
       {
                $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx9d5a7d36fe3ff22b&secret=25154ec8b737e79b89a251cb61afcc3e';
                $obnj = $this->curl_get_contents($url);
                $obnj = json_decode($obnj);

                if($obnj->access_token)
                {
                  
           $urlget_token='http://hometest.easytonent.com:38080/TopHoliday/Interface/TopBooking/get_token.ashx?access_token='.$obnj->access_token.'&time='.time();
           $jieguogettoken=$this->curl_get_contents($urlget_token);
           $jieguogettoken= str_replace("zgtjsonpcallback(", "", $jieguogettoken);
           $jieguogettoken= str_replace(")", "", $jieguogettoken);
           $jieguogettokenjson=json_decode($jieguogettoken);
                  }
                if($jieguogettokenjson->JsonData)
                {
                   return $jieguogettokenjson->JsonData;
                }
     } 
    
}



function traceHttp($data)
{
    if($data){
    logger("\n\nREMOTE_ADDR:".$_SERVER["REMOTE_ADDR"].(strstr($_SERVER["REMOTE_ADDR"],'101.226')? " FROM WeiXin": "Unknown IP---- $data"));
    logger("QUERY_STRING:".$_SERVER["QUERY_STRING"]);
    }else{
    
    logger("\n\nREMOTE_ADDR:".$_SERVER["REMOTE_ADDR"].(strstr($_SERVER["REMOTE_ADDR"],'101.226')? " FROM WeiXin": "Unknown IP"));
    logger("QUERY_STRING:".$_SERVER["QUERY_STRING"]);
    }
}
function logger($log_content)
{
    if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
        sae_set_display_errors(false);
        sae_debug($log_content);
        sae_set_display_errors(true);
    }else{ //LOCAL
        $max_size = 500000;
        $log_filename = "log.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
    }
}






?>