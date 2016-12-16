<?php

error_reporting(0);
 class Gtoken  {
     
     public $tokenfileName;
     public $nowTime;
     public $ticketfileName;
     
     public function __construct() {
         date_default_timezone_set('Asia/Shanghai');
         $this->tokenfileName = $_SERVER['DOCUMENT_ROOT'].'/wc_acctoken.txt';
         $this->ticketfileName = $_SERVER['DOCUMENT_ROOT'].'/wc_ticket.txt';
         $this->nowTime = time();
    }
     
     public function readfile($w='') {
         if($w){
              $filename = $this->ticketfileName;  
         }else{
             $filename = $this->tokenfileName;
         }
        if ($fp=@fopen($filename,'rb')) {
            if(PHP_VERSION >='4.3.0' && function_exists('file_get_contents')){
                return file_get_contents($filename);
            }else{
                flock($fp,LOCK_EX);
                $data=fread($fp,filesize($filename));
                flock($fp,LOCK_UN);
                fclose($fp);
                return $data;
            }
        }else{
           return false;
        }
    }
    
    public function writefile($data,$w=''){
       if($w){
             $filename = $this->ticketfileName;  
         }else{
             $filename = $this->tokenfileName;
         }
        if($fp=@fopen($filename,'wb')){
            if (PHP_VERSION >='4.3.0' && function_exists('file_put_contents')) {
                return file_put_contents($filename,$data,LOCK_EX);
            }else{
                flock($fp, LOCK_EX);
                $bytes=fwrite($fp, $data);
                flock($fp,LOCK_UN);
                fclose($fp);
                return $bytes;
            }
        }else{
            if (PHP_VERSION >='4.3.0' && function_exists('file_put_contents')) {
                return file_put_contents($filename,$data,LOCK_EX);
            }
          
        }
    }
    
 }
 
?>