<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 2014/11/14
 * Time: 15:28
 */

class FileController extends BaseController {
    public $target = '/uploads';// Relative to the root

    public function upload()
    {
        $verifyToken = md5('unique_salt' . $_POST['timestamp']);
        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $this->target;
            $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];

            // Validate the file type
            $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (in_array($fileParts['extension'],$fileTypes)) {
                move_uploaded_file($tempFile,$targetFile);
                echo '1';
            } else {
                echo 'Invalid file type.';
            }
        }
    }

    public function checkExist()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->target . '/' . $_POST['filename'])) {
            echo 1;
        } else {
            echo 0;
        }
    }



} 