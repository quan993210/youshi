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
    case "add_member_data":
        add_member_data();
        break;
    case "add_share":
        add_share();
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

function add_member_data(){
    global $db;
    $userid = $_POST['userid'] ? $_POST['userid'] : 0;
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $school = $_POST['school'];
    $kindergarten = $_POST['kindergarten'];
    $is_certificate = $_POST['is_certificate'];
    $add_time	= time();
    $add_time_format	= now_time();
    $sql = "INSERT INTO member_data (userid,name,mobile,school,kindergarten,is_certificate,add_time,add_time_format) VALUES ('{$userid}','{$name}', '{$mobile}', '{$school}', '{$kindergarten}', '{$is_certificate}', '{$add_time}', '{$add_time_format}')";
    $db->query($sql);

    //提交资料发送短信
    $certificate = $is_certificate=1?"有":"无";
    $param ="$name.',".$mobile.','.$kindergarten.','.$school.','.$certificate;
    $data = sms($param);

    //更新会员是否领取过
    if($userid > 0){
        $sql = "SELECT * FROM member WHERE userid = '{$userid}'";
        $member = $db->get_row($sql);
        $collect = $member['collect']+1;
        $sql = "UPDATE member SET  collect = '{$collect}' WHERE userid = '{$userid}'";
        $db->query($sql);
    }


    showapisuccess('','提交成功');
}


function add_share(){
    global $db;
    $userid = $_POST['userid'];
    $sql = "SELECT * FROM member WHERE userid = '{$userid}'";
    $member = $db->get_row($sql);
    $share = $member['share'] =$member['share']+1;
    $sql = "UPDATE member SET  share = '{$share}' WHERE userid = '{$userid}'";
    $db->query($sql);
    showapisuccess($member,'分享成功成功');
}

function data_num(){
    global $db;
    $sql 		= "SELECT COUNT(id) FROM member_data ";
    $total 		= $db->get_one($sql);
    $total = 500 - $total;
    $data=[$total];
    showapisuccess($data,'剩余资料');
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

function sms($param){
    //载入ucpass类
    require_once('lib/Ucpaas.class.php');

//初始化必填
    $options['accountsid']='7535bf1a400a40c7bde41a81cfe0da89';
    $options['token']='24069b3e0cb5f9ecce2f7bf94855142d';


//初始化 $options必填
    $ucpass = new Ucpaas($options);

//开发者账号信息查询默认为json或xml
    header("Content-Type:text/html;charset=utf-8");

//短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
    $appId = "3cba4db84c384638b02018bf0be49af6";
    $to = "18807913658,13807066031,18630796687"; //发给袁总，佳琦，勇昌
    $templateId = "184713"; //短信模板id
   // $param='袁卫明,18888888888,艾溪湖幼儿园,抚州崇仁师范,有'; //模板参数，这里填写申请人的手机号，短信内容：”国编新申请：用户袁卫明，号码是18888888888，就职于艾溪湖幼儿园，毕业于抚州崇仁师范，有幼师资格证。“

   return $ucpass->templateSMS($appId,$to,$templateId,$param);
}


