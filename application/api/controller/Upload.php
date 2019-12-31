<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/7/3
 * Time: 16:25
 */
namespace app\api\controller;

class Upload extends Base{

    //上传图片限制512KB
    public function uploadImage() {
        if (!empty($_FILES)) {
            if (count($_FILES) > 1) {
                return ajax('最多上传一张图片', 9);
            }
            $path = ajaxUpload(array_keys($_FILES)[0]);
            return ajax(['path' => $path]);
        } else {
            return ajax('请上传图片', 3);
        }
    }

//上传图片限制2048KB
    public function uploadImage2m() {
        if (!empty($_FILES)) {
            if (count($_FILES) > 1) {
                return ajax('最多上传一张图片', 9);
            }
            $path = ajaxUpload(array_keys($_FILES)[0], 2048);
            return ajax(['path' => $path]);
        } else {
            return ajax('请上传图片', 3);
        }
    }





}