<?php
  include("functions.php");
  $main = new Main();
  if(!isset($_SESSION["user"])) header('LOCATION:LoginPage.php');
  $user = $_SESSION["user"];
  $name = $_SESSION["name"];
  $uid = $_SESSION["id"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Questions List Page</title>
  <link rel="stylesheet" href="styles.css">
  <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
  </script>
</head>
<body><form action="#" method="post" enctype="multipart/form-data">
  <header>
    <div class="top-bar">
      <div class="logo">
        <img src="your-logo.png" alt="Your Website Logo">
        <h1>ATSS EduQuest</h1>
      </div>  
      <!-- Centered Search Bar -->
      <div class="search-bar-centered">
      <input type="text" name="srch" placeholder="Search...">
        <button type="submit" name="search" value="search">Search</button>
      </div>
      <!-- User tab code -->
      <div class="user-tab" id="userTab">
        <span>Welcome, <?php echo $user;?></span>
        <div class="user-options">
          <ul>
            <li><a href="#">My Profile</a></li>
            <li><a href="my_quest.php">My Questions</a></li>
            <li><a href="my_ans.php">My Answers</a></li>
            <li><a href="LoginPage.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
    <h1>Question List Page</h1>
    <button class="back-button" onclick="goBack()">LogOut</button>
  </header>

  <main>
    <section class="questions-list">
      <h2>All Questions</h2>
      <section class="add-question">
        <h2>Add a New Question</h2>
        
          <div>
            <label for="question-title">Question Title:</label>
            <input type="text" id="question-title" name="question-title">
          </div>
          <div>
            <label for="question-details">Question Details:</label>
            <textarea id="question-details" name="question-details" rows="4"></textarea>
          </div>
          <div>
            <input type="file" name="img">
            <button type="submit" name="submit_ques" value="submit">Submit Question</button>
<?php
  if(isset($_POST["logout"])){
    session_destroy();
    header("LOCATION:LoginPage.php");
  }
  if(isset($_POST["submit_ques"]) && !empty($_POST['question-title']) && $_POST['question-details']){
    $errors = array();
    $t = $_POST['question-title'];
    $q = $_POST['question-details'];
    $qid = time()."_".$uid;
    $img = 0;

    if(!save_quest($q,$qid)){
      $errors[0] .= "QUES ERROR";
    }else{
      $errors[0] = 0;
    }

    $i_err = save_img($_FILES["img"],$qid,'Q');
    if(in_array($i_err,array("jpeg","png","gif",0))){
      $img = $i_err;
      $errors[1] = 0;
    }else{
      $errors[1]= "IMG ERROR = ".$i_err;
    }

    if(!$errors[0] && !$errors[1]){
      $ques = $main->query("INSERT INTO Question(qid,user_id,title,imgfile) VALUES('$qid',$uid,'$t','$img')" );
      if(!$ques){
        echo "FAILED TO UPLOAD QUESTION!!!".$main->error();
        echo $img;
        del_file("quest_file/".$qid.".txt");
        del_file("quest_img/".$qid.".".$img);
      }
    }else{
      echo $errors[0]."<br>".$errors[1];
      del_file("quest_file/".$qid.".txt");
      del_file("quest_img/".$qid.".".$img);
    }
  }else{
    echo "ENTER TITLE AND QUESTION!!!";
  }
?>
          </div>
        </form>
      </section>
      <ul>
        <?php
          $squery = $main->query("SELECT Q.question_id,Q.title, U.username, Q.datetime, COUNT(V.user_id) AS view_count FROM Question Q LEFT JOIN Views V ON Q.qid = V.qid LEFT JOIN Users U ON Q.user_id = U.user_id GROUP BY Q.question_id, Q.qid, Q.user_id, Q.title, Q.imgfile, Q.datetime, U.username, U.user_type, U.dept, U.name, U.email ORDER BY view_count DESC, Q.datetime DESC;");
          //$squery = $main->query("SELECT * FROM Question, Users WHERE Question.user_id = Users.user_id");
          $cnt = 0;
          if(isset($_POST['search'])){
            while($arr = mysqli_fetch_assoc($squery)){
              if(stristr($arr['title'],$_POST['srch'])){
                $cnt++;
              echo "<li>Views : ".$arr['view_count']."<br>
              <a href='qna.php?ques=".$arr['question_id']."'>Question $cnt: ".$arr['title']."</a>
              <p class='question-details'>Asked by <span class='username'>".$arr['username']."</span> on <span class='date'>".$arr['datetime']."</span></p>
            </li><hr>";
              }
            }
          }else{
            while($arr = mysqli_fetch_assoc($squery)){
              $cnt++;
          echo "<li>Views : ".$arr['view_count']."<br>
          <a href='qna.php?ques=".$arr['question_id']."'>Question $cnt: ".$arr['title']."</a>
          <p class='question-details'>Asked by <span class='username'>".$arr['username']."</span> on <span class='date'>".$arr['datetime']."</span></p>
        </li><hr>";
            }
          }
        ?>
        <!-- More question -->
      </ul>
    </section>
  </main>

  <script>
    function goBack() {
      window.history.back();
    }

    const userTab = document.getElementById('userTab');
    userTab.addEventListener('mouseenter', () => {
      userTab.querySelector('.user-options').style.display = 'block';
    });

    userTab.addEventListener('mouseleave', () => {
      userTab.querySelector('.user-options').style.display = 'none';
    });
  </script>
</body>
</html>
