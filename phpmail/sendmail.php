<?php

	/**
	 * ע�����ʼ��඼�Ǿ����Ҳ��Գɹ��˵ģ������ҷ����ʼ���ʱ��������ʧ�ܵ����⣬������¼����Ų飺
	 * 1. �û����������Ƿ���ȷ��
	 * 2. ������������Ƿ�������smtp����
	 * 3. �Ƿ���php���������⵼�£�
	 * 4. ��26�е�$smtp->debug = false��Ϊtrue��������ʾ������Ϣ��Ȼ����Ը��Ʊ�����Ϣ��������һ�´����ԭ��
	 * 5. ������ǲ��ܽ�������Է��ʣ�http://www.daixiaorui.com/read/16.html#viewpl 
	 *    ����������У���������Ҫ�ҵĴ𰸡�
	 */

	require_once "email.class.php";
	//******************** ������Ϣ ********************************
	$smtpserver = "smtp.exmail.qq.com";//SMTP������
	$smtpserverport =25;//SMTP�������˿�
	$smtpusermail = "oa@easytonent.com";//SMTP���������û�����
	$smtpemailto = "bo.li@easytonent.com";//���͸�˭
	$smtpuser = "oa@easytonent.com";//SMTP���������û��ʺ�
	$smtppass = "Aa123456789";//SMTP���������û�����
	$mailtitle = "easycheckͣ���������޷���ȡ";//�ʼ�����
	$mailcontent = "<h1>easycheckͣ���������޷���ȡ������/easycheckapi/car.php </h1>";//�ʼ�����
	$mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
	//************************ ������Ϣ ****************************
	$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
	$smtp->debug = false;//�Ƿ���ʾ���͵ĵ�����Ϣ
	$state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

	echo "<div style='width:300px; margin:36px auto;'>";
	if($state==""){
		echo "�Բ����ʼ�����ʧ�ܣ�����������д�Ƿ�����";
		exit();
	}
	echo "�ѳɹ������ʼ�֪ͨ��";




/*
ʹ�õ���Ŀ

function senmallfunction(){
require_once "/bo/email.class.php";
	//******************** ������Ϣ ********************************
	$smtpserver = "smtp.exmail.qq.com";//SMTP������
	$smtpserverport =25;//SMTP�������˿�
	$smtpusermail = "oa@easytonent.com";//SMTP���������û�����
	$smtpemailto = "bo.li@easytonent.com";//���͸�˭
	$smtpuser = "oa@easytonent.com";//SMTP���������û��ʺ�
	$smtppass = "Aa123456789";//SMTP���������û�����
	$mailtitle = "easycheckͣ���������޷���ȡ";//�ʼ�����
	$mailcontent = "<h1>easycheckͣ���������޷���ȡ������/easycheck/car.php </h1>";//�ʼ�����
	$mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
	//************************ ������Ϣ ****************************
	$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
	$smtp->debug = false;//�Ƿ���ʾ���͵ĵ�����Ϣ
	$state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

	if($state==""){
		echo "�Բ����ʼ�����ʧ�ܣ�����������д�Ƿ�����";
		exit();
	}
	echo "�ѳɹ������ʼ�֪ͨ��";
}

*/




?>