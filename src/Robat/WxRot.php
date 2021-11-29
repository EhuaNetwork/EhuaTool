<?php


namespace Ehua\Robat;

/**
 * 微信机器人http操作类{可爱猫}
 * 可爱猫|http-sdk 论坛：https://www.ikam.cn/
 * Class WxRot
 *
 *
 * 其他相关论坛:https://gitee.com/ikam/http-sdk
 * @package Ehua\Robat
 */
class WxRot
{
    public $domain;
    public $Client;

    public function __construct()
    {
        $this->Client = new GuzzleHttp\Client();
        $this->domain = "http://127.0.0.1:88/httpAPI";
    }

    public function quest($param)
    {
        $res = (string)$this->Client->get($this->domain . '?' . $param)->getBody();
        return json_decode($res, true);
    }

    //取框架版本号
    public function GetFrameVersion()
    {
        $param = [
            'api' => explode('::', __METHOD__)[1],
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //取群聊列表
    public function GetGroupList($wxid, $refresh = false)
    {
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'is_refresh' => $refresh,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //取好友列表
    public function GetFriendList($wxid, $refresh = false)
    {
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'is_refresh' => $refresh,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //置顶联系人
    public function OnTop($wxid,$friend_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'friend_wxid' => $friend_wxid,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
    //邀请加入群聊
    public function InviteInGrou($wxid,$group_wxid,$friend_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid'=>$group_wxid,
            'friend_wxid' => $friend_wxid,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //退出群聊
    public function QuitGroup($wxid,$group_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid'=>$group_wxid,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //建立新群
    public function BuildingGroupPlus($wxid,$friend_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'friendArr'=>$friend_wxid,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //修改群公告
    public function ModifyGroupNotice($wxid,$content,$group_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid'=>$group_wxid,
            'content'=>$content,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //修改群名称
    public function ModifyGroupName($wxid,$group_name,$group_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid'=>$group_wxid,
            'group_name'=>$group_name,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }


    //踢出群成员
    public function RemoveGroupMember($wxid,$group_name,$group_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid'=>$group_wxid,
            'group_name'=>$group_name,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }


    //系统添加日志
    public function AppendLogs($msg1,$msg2){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'msg1'=>$msg1,
            'msg2'=>$msg2,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
    //系统取应用目录
    public function GetAppDirectory(){
        $param = [
            'api' => explode('::', __METHOD__)[1],
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
    //删除好友
    public function DeleteFriend($wxid,$friend_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'friend_wxid'=>$friend_wxid,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //修改好友备注
    public function ModifyFriendNote($wxid,$friend_wxid,$node){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'friend_wxid'=>$friend_wxid,
            'node'=>$node,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //同意好友请求
    public function AgreeFriendVerify($wxid,$json_msg){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'json_msg'=>$json_msg,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
    //同意群聊邀请
    public function AgreeGroupInvite($wxid,$json_msg){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'json_msg'=>$json_msg,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
    //接收好友转账
    public function AcceptTransfer($wxid,$from_wxid,$json_msg){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'from_wxid'=>$from_wxid,
            'json_msg'=>$json_msg,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
    //取联系人头像
    public function GetContactHeadimgurl($wxid,$to_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid'=>$to_wxid,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //取群成员列表
    public function GetGroupMemberList($wxid,$group_wxid,$is_refresh=false){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid'=>$group_wxid,
            'is_refresh'=>$is_refresh,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //取群成员详细
    public function GetGroupMemberDetailInfo($wxid,$group_wxid,$member_wxid,$is_refresh=false){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid'=>$group_wxid,
            'member_wxid'=>$member_wxid,
            'is_refresh'=>$is_refresh,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //取登录账号列表
    public function GetLoggedAccountList(){
        $param = [
            'api' => explode('::', __METHOD__)[1],
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //取登录账号头像
    public function GetRobotHeadimgurl(){
        $param = [
            'api' => explode('::', __METHOD__)[1],
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //取登录账号昵称
    public function GetRobotName(){
        $param = [
            'api' => explode('::', __METHOD__)[1],
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //转发消息
    public function ForwardMsg($wxid,$to_wxid,$msg_id){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'msg_id' => $msg_id,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送音乐分享
    public function SendMusicMsg($wxid,$to_wxid,$name,$type){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'name' => $name,
            'type' => $type,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送小程序消息
    public function SendMiniAppMsg($wxid,$to_wxid,$xmlContent){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'xmlContent' => $xmlContent,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送分享链接
    public function SendLinkMsg($wxid,$to_wxid,$title,$text,$target_url,$pic_url,$icon_url){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'title' => $title,
            'text' => $text,
            'target_url' => $target_url,
            'pic_url' => $pic_url,
            'icon_url' => $icon_url,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
    //发送群消息并艾特
    public function SendGroupMsgAndAt($wxid,$group_wxid,$member_wxid,$member_name,$msg){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'group_wxid' => $group_wxid,
            'member_wxid' => $member_wxid,
            'member_name' => $member_name,
            'msg' => $msg,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送名片消息
    public function SendCardMsg($wxid,$to_wxid,$friend_wxid){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'friend_wxid' => $friend_wxid,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送动态表情
    public function SendEmojiMsg($wxid,$to_wxid,$path){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'path' => $path,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送文件消息
    public function SendFileMsg($wxid,$to_wxid,$path,$file){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'path' => $path,
            'file' => $file,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送视频消息
    public function SendVideoMsg($wxid,$to_wxid,$path){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'path' => $path,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送图片消息
    public function SendImageMsg($wxid,$to_wxid,$path){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'path' => $path,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }

    //发送文本消息
    public function SendTextMsg($wxid,$to_wxid,$msg){
        $param = [
            'api' => explode('::', __METHOD__)[1],
            'robot_wxid' => $wxid,
            'to_wxid' => $to_wxid,
            'msg' => $msg,
        ];
        $param = http_build_query($param);
        $res = $this->quest($param);
        return $res;
    }
}