<?php
    //include ("functions.php");
    $uname=$_POST['uname'];
    $password=$_POST['pass'];
    $conn=mysqli_connect("localhost","root","","CQ");
    if(!empty($uname) && !empty($password)){
        $result=mysqli_query($conn,"SELECT pass FROM Users WHERE (email='$uname' OR username='$uname')");
        $pass=mysqli_fetch_row($result);
        $err[1]=$uname;
        if(empty($pass)){
            $err[0]="INVALID USERNAME!!!";
        }else{
            if($pass[0]==$password){
                $err[0]="VALID USERNAME AND PASSWORD";
            }else{
                $err[0]="INVALID PASSWORD!!!";
            }
        }
    }else{
        $err[0] = "ENTER ALL DETAILS!!!";
    }
    echo (json_encode($err));
?>