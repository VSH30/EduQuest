<?php
    class Main{
        private $conn, $time;
        public function __construct(){
            session_start();
            date_default_timezone_set('Asia/Kolkata');
            $this->conn = mysqli_connect("localhost","root","","CQ");
        }
        public function query($query){
            return(mysqli_query($this->conn,$query));
        }
        public function error(){
            return mysqli_error($this->conn);
        }
    }
    function save_quest($q,$qid){
        $filePath = "quest_file/".$qid.".txt";
        if (file_put_contents($filePath, $q)) {
            return true;
        }else{
            return "Error saving paragraph.";
        }
    }
    function save_ans($a,$aid){
        $filePath = "ans_file/".$aid.".txt";
        if (file_put_contents($filePath, $a)) {
            return true;
        }else{
            return "Error saving paragraph.";
        }
    }
    function save_img($f,$qid,$type){
            if (isset($f) && $f['error'] === UPLOAD_ERR_OK) {
                // Define allowed image file types (adjust as needed)
                $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
                // Define maximum file size (adjust as needed, in bytes)
                $maxFileSize = 5 * 1024 * 1024; // 5 MB
                // Check if the uploaded file is an allowed image type
                if (!in_array($f['type'], $allowedTypes)) {
                    return "Error: Only JPEG, PNG, and GIF images are allowed.";
                }
                // Check if the uploaded file size is within the allowed limit
                if ($f['size'] > $maxFileSize) {
                    return "Error: The file size exceeds the allowed limit (5 MB).";
                }
                //Moving
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                if($type == 'Q')    $pth = "quest_img/";
                else if($type == 'A')   $pth = "ans_img/";
                $targetPath = $pth.$qid.'.'.$ext;
                if(!move_uploaded_file($f["tmp_name"], $targetPath)){
                    return "FAILED TO MOVE!!!";
                }else{
                    return $ext;
                }
        }else{
            return 0;
        }
    }
    function del_file($f){
        if(file_exists($f))
            unlink($f);
    }
?>