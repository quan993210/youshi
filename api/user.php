<?php
/**
 * Created by PhpStorm.
 * User: xkq
 * Date: 2017/9/1 0001
 * Time: 21:21
 * 绑定微信用户接口
 */
define('APPID','wxdb2efeed7976fe30');
define('APPSECRET',' d03366cff50d1e1207cb9488baf2e43c');
//AppID: wx6ce6752b26628e64
//appSecret: 4ca37043b96c71c8224e0299e92d969e

set_include_path(dirname(dirname(__FILE__)));
include_once("inc/init.php");
if (!session_id()) session_start();

$action = crequest("action");
$action = $action == '' ? 'bind_user' : $action;

switch ($action)
{
    case "bind_user":
        bind_user();
        break;
    case "login_openid":
        login_openid();
        break;
}

function bind_user(){
    global $db;
    if(isset($_POST['code']) && !empty($_POST['code']) && isset($_POST['mobile']) && !empty($_POST['mobile'])) {
        $mobile = addslashes(trim($_POST['mobile']));
        $sql = "SELECT * FROM member WHERE mobile = '{$mobile}'";
        $member = $db->get_row($sql);
        if($member){
            $code = $_POST['code'];
            $encryptedData = $_POST['encryptedData'];
            $iv = $_POST['iv'];
            $session_key = wxCode($code);
            $userInfo = decryptData($session_key,$encryptedData,$iv);
            if ($userInfo && !empty($userInfo) && isset($userInfo['openId']) && !empty($userInfo['openId'])) {
                $sql = "SELECT * FROM member WHERE openid = '{$userInfo['openId']}'";
                $member = $db->get_row($sql);
                if($member['mobile'] == $mobile &&  $member && isset($member['openid']) && !empty($member['openid'])){
                    showapisuccess($member);
                }else{
                    $nickname    	= $userInfo['nickName'];
                    $openid    	= $userInfo['openId'];
                    $avatar    	= $userInfo['avatarUrl'];
                    $unionid    	= $userInfo['unionid'];
                    $add_time	= time();
                    $add_time_format	= now_time();
                    $sql = "INSERT INTO member (openid, nickname, unionid,  avatar,add_time,add_time_format) VALUES ('{$openid}','{$nickname}','{$unionid}','{$avatar}', '{$add_time}', '{$add_time_format}')";
                    $db->query($sql);

                    $sql = "SELECT * FROM member WHERE mobile=$mobile";
                    $member = $db->get_row($sql);
                    showapisuccess($member);
                }
            }else{
                showapierror('参数错误！');
            }
        }else{
            showapierror('用户不存在！');
        }

    } else {
        showapierror('参数错误！');
    }
}

function login_openid(){
    global $db;
    if(isset($_POST['openid']) && !empty($_POST['openid'])) {
        $openid = $_POST['openid'];
        $sql = "SELECT * FROM member WHERE openid = '{$openid}'";
        $member = $db->get_row($sql);
        showapisuccess($member);

    }else{
        showapierror('参数错误！');
    }
}


/**
 * 微信登录对接接口
 */
function wxCode($code){

    if (empty($code)){
        showapierror(-1,'code缺失');
        die;
    }

    //拼装url
    $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".APPID."&secret=".APPSECRET."&js_code=".$code."&grant_type=authorization_code ";


    $data = https_request($url);

    $result = json_decode($data,true);


    if (!array_key_exists('errcode',$result)){
        $session_key = $result['session_key'];

        return $session_key;
    }else{
        //error log
        return false;
    }

}

/**
 * 解密encryptedData
 * @param $sessionKey
 * @param $encryptedData
 * @param $iv
 */
function decryptData($sessionKey,$encryptedData,$iv){
    if (empty($sessionKey)){
        showapierror('sessionKey缺失');
        die;
    }
    if (empty($encryptedData)){
        showapierror('encryptedData缺失');
        die;
    }
    if (empty($iv)){
        showapierror('iv缺失');
        die;
    }
    require(ROOT_PATH . 'inc/weixin/wxBizDataCrypt.php');
    $pc = new WXBizDataCrypt(APPID, $sessionKey);
    $errCode = $pc->decryptData($encryptedData, $iv, $data );
 ;

    if ($errCode == 0) {
       // var_dump($data);
        //成功
        return json_decode($data,true);
    } else {
//        print($errCode . "\n");
        return false;
    }
}

/**
 * @explain
 * 发送http请求，并返回数据
 **/
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


