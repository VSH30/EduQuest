<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../login.css">
    <!---<script>
      function verify(){
          var user = f1.username.value;
          var pass = f1.password.value;
          var x = new XMLHttpRequest();
          x.onreadystatechange = function(){
              var resp = JSON.parse(x.responseText);
                if(resp[0]=="VALID USERNAME AND PASSWORD"){
                    document.cookie = "user="+resp[1];
                    window.location.replace("home.php");
                }else{
                  document.getElementById("error").innerHTML = resp[0];
                }
          }
          x.open("POST","uservalidation.php",false);
          x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
          x.send("uname="+user+"&pass="+pass);
      }
  </script>--->
  <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
  </script>
  </head>
  <body>
    <header>
      <h1>Login Page</h1>
    </header>
    <main>
      <section class="login-form">
        <h2>Login</h2>
        <form action="#" method="post" name="f1">
          <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
          </div>
          <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
          </div>
          <div>
            <label for="utype">Login Type:</label>
            <select name="utype" required>
              <option value="student">Student</option>
              <!---<option value="faculty">Faculty</option>-->
              <option value="admin">Admin</option>
            </select>
          </div>
          <div id="error">
            <?php
                include("functions.php");
                $main = new Main();
                if(isset($_SESSION['user'])){
                  session_unset();
                }
                //$lquery =  $main->query("INSERT INTO logs(username,task,remark) VALUES('$ulout','User Logout','')");
                if(isset($_POST['submit'])){
                  if(!empty($_POST['username']) && !empty($_POST['password'])){
                      $uname=$_POST['username'];
                      $password=$_POST['password'];
                      $result=$main->query("SELECT * FROM Users WHERE (email='$uname' OR username='$uname')");
                      $pass=mysqli_fetch_row($result);
                      if(!$pass){
                          echo "INVALID USERNAME!!!";
                      }else{
                          if($pass[6]==$password){
                            if($_POST['utype']==$pass[2]){
                              echo "VALID USERNAME AND PASSWORD";
                              $_SESSION['user'] = $uname;
                              $_SESSION['name'] = $pass[4];
                              $_SESSION['id'] = $pass[0];
                              //$main->query("INSERT INTO logs(username,task,remark) VALUES('$uname','User Login','')");
                              if($pass[2]=='student')
                                header('LOCATION:ques.php');
                              else if($pass[2]== 'admin')
                                header('LOCATION:admin_ques.php');
                            }else{
                              echo "INVALID USER TYPE!!!";
                            }
                          }else{
                              echo "INVALID PASSWORD!!!";
                          }
                      }
                  }else{
                      echo "ENTER ALL DETAILS!!!".$_POST['username'].$_POST['password'];
                  }
                }
            ?>
          </div>
          <div>
            <button type="submit" name="submit" value="submit">Login</button> ----- <button type="button">SignUp</button>
          </div>
        </form>
      </section>
    </main>
  </body>
</html>
