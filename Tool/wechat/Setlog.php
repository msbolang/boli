<?php

class Setlog {
    
    public $Today;
    public $LogDir;
    public $max_size;

    public function __construct() {
         date_default_timezone_set('Asia/Shanghai');
         $this->LogDir = $_SERVER['DOCUMENT_ROOT'].'/log';
         $this->Today = date('Y-m-d',time());
         $this->max_size = 500000;
    }
    

    public function WechatHttp()
    {
        $this->logger("\n\nREMOTE_ADDR:".$_SERVER["REMOTE_ADDR"].(strstr($_SERVER["REMOTE_ADDR"],'101.226')? " FROM WeiXin": "Unknown IP"),'wechat');
        $this->logger("QUERY_STRING:".$_SERVER["QUERY_STRING"],'wechat');
    }
    

    private function logger($log_content,$name='')
    {
        
        
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE

            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else{ //LOCAL
          if(!file_exists($this->LogDir))
          {
              mkdir($this->LogDir,0777);
          }
          
          if($name){
                 $log_filename = $this->LogDir.'/'.$name.'_'.$this->Today.'.txt';
            }else{
                 $log_filename = $this->LogDir.'/'.$this->Today.'.txt';
            }
            
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $this->max_size))
            {
                unlink($log_filename);
            }
              file_put_contents($log_filename, date('Y-m-d H:i:s',time()).'__'.$log_content."\r\n", FILE_APPEND);
        }
    }
}


 
 
?>