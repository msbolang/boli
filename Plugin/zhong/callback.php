<?php
error_reporting(0);
ini_set('display_errors', 1);//设置开启错误提示
error_reporting('E_ALL & ~E_NOTICE ');//错误等级提示
 $appid='wx9d5a7d36fe3ff22b';//中港通appid
 $appsecret='25154ec8b737e79b89a251cb61afcc3e';
if(isset($_GET['code'])){
    $code = !empty($_GET['code']) ? $_GET['code'] : '';
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
    $ret_oa_json = curl_get_contents($url);
    $ret_oa = json_decode($ret_oa_json);
   if(strlen($ret_oa -> openid) == 28)
     {
        $openid = $ret_oa -> openid;
           $w_openid=null;
           $getopenid="http://hometest.easytonent.com:38080/TopHoliday/Interface/TopBooking/getuser.ashx?id=$openid";
           $getopenidinfo = curl_get_contents($getopenid);  
           $getopenidinfo= str_replace("zgtjsonpcallback(", "", $getopenidinfo);
           $getopenidinfo= str_replace(")", "", $getopenidinfo);
           $newgetopenidinfo=json_decode($getopenidinfo);
           
           if($newgetopenidinfo->JsonData)
           {
             $w_openid=$newgetopenidinfo->JsonData[0];
           }
         
     
        $time = time();
        if(empty($w_openid)){
         if(!empty($openid)){    
        $gettokenurl = 'http://hometest.easytonent.com:38080/TopHoliday/Interface/TopBooking/get_token.ashx';
        $gettokenurlinfo=curl_get_contents($gettokenurl);
        $gettokenurlinfo= str_replace("zgtjsonpcallback(", "", $gettokenurlinfo);
        $gettokenurlinfo= str_replace(")", "", $gettokenurlinfo);
        $newgettokenurlinfo=json_decode($gettokenurlinfo);
        $access_token_app=$newgettokenurlinfo->JsonData; 
         
        
      
        if((time()-$access_token_app->time)>7200)
         {
            $access_token_app = gettokenapp();
         };       

                if($access_token_app)
                    {
                   $sns_url ='https://api.weixin.qq.com/cgi-bin/user/info?access_token='. $access_token_app->access_token .'&openid='. $openid .'&lang=zh_CN';
                   $ret_sns_json = curl_get_contents($sns_url);
                   $ret_sns = json_decode($ret_sns_json);
                        if($openid == $ret_sns -> openid)
                        {
           $geturl='http://hometest.easytonent.com:38080/TopHoliday/Interface/TopBooking/updatauser.ashx?id='.$openid.'&wechat_name='.$ret_sns->nickname.'&picture='.$ret_sns->headimgurl;
           $isgetintefe = curl_get_contents($geturl);  
           $isgetintefe= str_replace("zgtjsonpcallback(", "", $isgetintefe);
           $isgetintefe= str_replace(")", "", $isgetintefe);
           $isgetintefe=json_decode($isgetintefe);
           $isgetintefeJsonData=$isgetintefe->JsonData;
           if($isgetintefeJsonData)
                        {
                                              
                              setcookie('myopenid',$isgetintefeJsonData->ID);
                              setcookie('myimage',$isgetintefeJsonData->Picture);
                              setcookie('myname',$isgetintefeJsonData->WeChatName);
                              setcookie('myusrname',$isgetintefeJsonData->Name);
                              setcookie('myTel',$isgetintefeJsonData->Tel);
                              setcookie('myEmail',$isgetintefeJsonData->Email);
                              
                          
                              
                              
                              
                              
                              
                           
                              if($_GET['state']==1)
                                  {
                                    header("Location:index.html");
                                  }
                                  
                               if($_GET['state']==2)
                                  {
                                    header("Location:index.html?i=query_order");
                                  }  
                            
                                  if($_GET['state']==3)
                                  {
                                    header("Location:index.html?i=user_index");
                                  }        
                                
                                  
                             // echo  json_encode($data_f);
                           
                        }
                        
                        }
                   }
            
            }
        }else{
          

                              setcookie('myopenid',$w_openid->ID);
                              setcookie('myimage',$w_openid->Picture);
                              setcookie('myname',$w_openid->WeChatName);
                              setcookie('myusrname',$w_openid->Name);
                              setcookie('myTel',$w_openid->Tel);
                              setcookie('myEmail',$w_openid->Email);
             
             
      
                                if($_GET['state']==1)
                                  {
                                    header("Location:index.html");
                                  }
                                  
                               if($_GET['state']==2)
                                  {
                                    header("Location:index.html?i=query_order");
                                  }  
                            
                                  if($_GET['state']==3)
                                  {
                                    header("Location:index.html?i=user_index");
                                  }    
       
        }

    }else{
     //   var_dump($ret_oa -> openid);
        header("Location:index.html");
    }
}




function curl_get_contents($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
    curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}


 function gettokenapp()
       {
                $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx9d5a7d36fe3ff22b&secret=25154ec8b737e79b89a251cb61afcc3e';
                $obnj = curl_get_contents($url);
                $obnj = json_decode($obnj);

                if($obnj->access_token)
                {
                  
           $urlget_token='http://hometest.easytonent.com:38080/TopHoliday/Interface/TopBooking/get_token.ashx?access_token='.$obnj->access_token.'&time='.time();
           $jieguogettoken=curl_get_contents($urlget_token);
           $jieguogettoken= str_replace("zgtjsonpcallback(", "", $jieguogettoken);
           $jieguogettoken= str_replace(")", "", $jieguogettoken);
           $jieguogettokenjson=json_decode($jieguogettoken);
                  }
                if($jieguogettokenjson->JsonData)
                {
                   return $jieguogettokenjson->JsonData;
                }
     }


?>
  