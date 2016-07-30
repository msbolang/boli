<?php

include '../conf/mysql.class.php';
$db = MyPDO::getInstance('localhost', 'root', '', 'xuexi', 'utf8');
//$db->destruct(); 关闭数据库链接

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $del = $db->delete('orders',"oid = $id");
    if($del){
        echo 1;
    
    }else{
        echo 0;
        
    }
    exit;
}

if (isset($_GET['getorder'])) {
    $order = $db->query('select * from orders left join goods on orders.gid=goods.gid order by  orders.oid asc');
    echo '{"records":'.json_encode($order).'}';
    exit;
}

if (isset($_GET['getgood'])) {
    $goods = $db->query('select * from goods');
    echo '{"records":'.json_encode($goods).'}';
    exit;
}

if(isset($_POST['gomainum']))
{
    $gomainum = $_POST['gomainum'];
    $goumaivalue = $_POST['goumaivalue'];
    $isok = $db->insert('orders',array('gid'=>$goumaivalue,'much'=>$gomainum));
    if($isok){
        echo 1;
    }else{
        echo 0;
    }
}


?>