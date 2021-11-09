<?php


namespace App\Services;

use App\Exceptions\ArException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\MembersModel as Members;

/**
 * 身份证识别
 */
class AutoRecognitionService
{
    /** 把图片转成base64
     * @param string $img 图片地址
     * @return string
     */
    public static function imgToBase64($img='')
    {
        return base64_encode(file_get_contents($img));
    }

    /**
     * @param $url
     * @param string $save_dir
     * @param string $filename
     * @param int $type
     * @return array
     */
    public static function urlImageToFile($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return array('file_name' => '', 'save_path' => '', 'error' => 1);
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg') {
                return array('file_name' => '', 'save_path' => '', 'error' => 3);
            }
            $filename = time() . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
    }

    /**
     * 压缩图片
     * @param $file
     * @throws ArException
     */
    public static function compressImage($file) {
        $percent = 0.3; //图片压缩比
        list($width, $height, $type) = getimagesize($file); //获取原图尺寸
        //缩放尺寸
        $newwidth = $width * $percent;
        $newheight = $height * $percent;
        if ($type == 2) {
            $src_im = imagecreatefromjpeg($file);
        } else if ($type == 3){
            $src_im = imagecreatefrompng($file);
        } else {
            throw new ArException(ArException::USER_ID_CARD_AUTH_IMAGE);
        }
        $dst_im = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($dst_im, $file); // 输出压缩后的图片
        imagedestroy($dst_im);
        imagedestroy($src_im);
    }

    /**
     * 身份证识别
     * @param $appcode $appcode 阿里云APPCODE
     * @param string $file 你的文件路径
     * @param string $side 身份证正反面类型：face/back
     * @return mixed
     * @throws ArException
     */
    public static function push($appcode, $file = "", $side = "face")
    {
        if (empty($file)) {
            throw new ArException(ArException::USER_ID_CARD_AUTH_IMAGE);
        }
        //请求阿里云api地址
        $url = "https://dm-51.data.aliyun.com/rest/160601/ocr/ocr_idcard.json";
        //如果输入带有inputs, 设置为True，否则设为False
        $is_old_format = false;
        //如果没有configure字段，config设为空
        $config = array(
            "side" => $side // 身份证正反面类型: face/back
        );
        //$config = array()
        // 保存图片到本地
        $save_dir = public_path("id_card") . DIRECTORY_SEPARATOR;
        $filename = time() . ".jpg";
        $save_data = AutoRecognitionService::urlImageToFile($file, $save_dir, $filename);
        if (!empty($save_data["code"])) {
            throw new ArException(ArException::USER_ID_CARD_AUTH_IMAGE);
        }
        $save_path = $save_data["save_path"];
        // 压缩图片
        AutoRecognitionService::compressImage($save_path);
        // 转为base64
        $base64 = AutoRecognitionService::imgToBase64($save_path);
        // 删除文件
        unlink($save_path);
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/json; charset=UTF-8");
        $querys = "";
        if ($is_old_format == TRUE) {
            $request = array();
            $request["image"] = array(
                "dataType" => 50,
                "dataValue" => "$base64"
            );

            if (count($config) > 0) {
                $request["configure"] = array(
                    "dataType" => 50,
                    "dataValue" => json_encode($config)
                );
            }
            $body = json_encode(array("inputs" => array($request)));
        } else {
            $request = array(
                "image" => "$base64"
            );
            if (count($config) > 0) {
                $request["configure"] = json_encode($config);
            }
            $body = json_encode($request);
        }
        $method = "POST";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $url, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rheader = substr($result, 0, $header_size);
        $rbody = substr($result, $header_size);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            if ($is_old_format) {
                $output = json_decode($rbody, true);
                $result_str = $output["outputs"][0]["outputValue"]["dataValue"];
            } else {
                $result_str = $rbody;
            }
            // 身份证读取内容输出
            return json_decode($result_str, true);
        }
        throw new ArException(ArException::USER_ID_CARD_AUTH_IMAGE);
    }
}
