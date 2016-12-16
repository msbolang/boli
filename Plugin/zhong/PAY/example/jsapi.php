<?php 
ini_set('date.timezone','Asia/Shanghai');
error_reporting('E_ALL');
//ini_set('display_errors', 1);//设置开启错误提示
//error_reporting('E_ALL & ~E_NOTICE ');//错误等级提示
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志

$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
/*
function printf_info($data)
{
    foreach($data as $key=>$value)
    {
     //   echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}
 * 
 */
//?price ='+Price+'&order='+INorder+'&openid='+Openid+'&orderinfo='+Info+'&daytime='+INDatatime+'&wayone='+WAYone+'
//&huiinfo='+HInfo+'&huidaytime='+INDatatime_h;
 
/*
if(isset($_GET['price']))
{
    $price = $_GET['price']*100;
}

$order = $_GET['order'];

$openid= $_GET['openid'];

$orderinfo = $_GET['orderinfo'];
$daytime = $_GET['daytime'];
$wayone = $_GET['wayone'];
//$openId = $_GET['Openid'];
if(isset($_GET['huiinfo']))
{
    $huiinfo = $_GET['huiinfo'];
}   

if(isset($_GET['huidaytime']))
{
  $huidaytime = $_GET['huidaytime'];
}  

*/



function traceHttp($order)
{
    logger("order=".$order);
   // logger("QUERY_STRING:".$_SERVER["QUERY_STRING"]);
}
function logger($log_content)
{
    if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
        sae_set_display_errors(false);
        sae_debug($log_content);
        sae_set_display_errors(true);
    }else{ //LOCAL
        $max_size = 500000;
        $log_filename = "zflog.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
    }
}




//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

 $hell=array();
        if(isset($_GET['state']))
        {
             $state = $_GET['state'];
             $hell = explode(',',$state); 
             if($hell[3]==0){
                 $body='购票单程';
             }else{
                 $body='购票双程';
             }
        }
$intfee =intval($hell[0])*100;
//appid, mch_id, nonce_str, body, attach, out_trade_no, total_fee, spbill_create_ip, notify_url, trade_type, openid(jsapi必须)  ， product_id（native必须） 。
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($body);
$input->SetAttach("中港通");
//$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetOut_trade_no($hell[1]);
traceHttp($hell[1]);//记录order-id----------------2016-05-31
$input->SetTotal_fee($intfee);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("购票");

//$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");

$input->SetNotify_url("http://m.top-booking.com/piao/PAY/example/notify.php");
$input->SetTrade_type("JSAPI");

$input->SetOpenid($openId);

$order = WxPayApi::unifiedOrder($input);

//echo '<font color="#f00"><b>支付单信息</b></font><br/>'; ($order);
$jsApiParameters = $tools->GetJsApiParameters($order);
//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    
    <title></title>
      <script type="text/javascript" src="/piao/js/jquery-1.11.1.min.js?20160204">        </script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript"></script>
    
   
    <script type="text/javascript">
        
   var url_="<?=$hell[4]?>";   
   var pric_="<?=$hell[0]?>";
   var orderNo="<?=$hell[1]?>";    
    function callbacktonet()
    {
  
           $.ajax({
                    type: "get",

                    async: true,

                    url:url_+'getTickets.ashx?orderNo='+orderNo+'&price='+pric_,

                    dataType: "jsonp",

                    jsonp: "jsonpCallback",

                    jsonpCallback: "zgtjsonpcallback",

                    error: function(){                            
    },
                    success: function(json){
                        
                   window.location.href='/piao/index.html?i=query_order';        
    }

                })
              
       
    }    
        
        
        
        
        
        
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res)
                        {
			WeixinJSBridge.log(res.err_msg);
                             //   alert(res.err_msg);
                              if(res.err_msg=='get_brand_wcpay_request:ok')
                                {
                                    //成功支付
                                 alert('支付成功！');
                                 callbacktonet();
                                    
                                }else{
                                  alert('支付失敗！');
                                  window.location.href='/piao/index.html?i=query_order';
                                }

			}
		);
	}

	function callpay()
	{
		if (typeof(WeixinJSBridge) == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
        
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
				alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}
	
        /*
	window.onload = function(){
		if (typeof(WeixinJSBridge) == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		} 
	};
	*/
       window.onload = function(){
       callpay();
   }
	</script>
</head>
<body>
  <!--  
    <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onClick="callpay();" >立即支付</button>
	</div>
    -->
</body>
</html>