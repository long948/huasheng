<?php

namespace App\Libraries;

class SendAppNotify
{

    protected $AppKey;
    protected $AppId;
    protected $MasterSecret;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($AppId, $AppKey, $MasterSecret)
    {
        $this->AppKey = $AppKey;
        $this->AppId = $AppId;
        $this->MasterSecret = $MasterSecret;
    }

    public function Push($cid, $title, $content)
    {
        $igt = new \IGeTui('', $this->AppKey, $this->MasterSecret);

        //消息模版：
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        $template = $this->IGtNotyPopLoadTemplate($title, $content);

        //定义"SingleMessage"
        $message = new \IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->AppId);
        $target->set_clientId($cid);

        try {
            $rep = $igt->pushMessageToSingle($message, $target);
        } catch (\RequestException $e) {
            $requstId = $e->getRequestId();
            //失败时重发
            $rep = $igt->pushMessageToSingle($message, $target, $requstId);
        }
    }

    private function IGtNotyPopLoadTemplate($title, $content)
    {
        $template =  new \IGtNotificationTemplate();
        $template->set_appId($this->AppId);//应用appid
        $template->set_appkey($this->AppKey);//应用appkey
        $template->set_transmissionType(1);//透传消息类型，Android平台控制点击消息后是否启动应用
        $template->set_transmissionContent('test');//透传内容，点击消息后触发透传数据
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
        //    $template->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo，不设置使用默认程序图标
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        return $template;
    }
}
