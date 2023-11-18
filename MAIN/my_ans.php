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
  <title>My Answers</title>
  <link rel="stylesheet" href="styles.css">
  <script>
    if ( window.history.replaceState ) {
       window.history.replaceState( null, null, window.location.href );
    }
  </script>
</head>
<body>
  <header>
    <div class="top-bar">
      <div class="logo">
        <img src="your-logo.png" alt="Your Website Logo">
        <h1>ATSS EduQuest</h1>
      </div>  
      <!-- Centered Search Bar -->
      <!---<div class="search-bar-centered">
        <input type="text" placeholder="Search...">
        <button type="submit">Search</button>
      </div>--->
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
    <h1>My Answers</h1>
    <button class="back-button" onclick="goBack()">Back</button>
  </header>

  <main>
    <section class="questions-list">
      <h2>All Answers by <?php echo $user;?></h2>
      <?php
        if(isset($_POST['delete'])){
          $d = $_POST['delete'];
          $delq = $main->query("SELECT imgfile FROM Answer WHERE aid ='$d'");
          $delf = mysqli_fetch_assoc($delq);
          $deli = $delf['imgfile'];
          del_file("ans_img/".$d.".".$deli);
          del_file("ans_file/".$d.".txt");
          $delete = $main->query("DELETE A, L FROM Answer A LEFT JOIN Likes L ON A.answer_id = L.answer_id WHERE A.aid = '$d';");
          echo "DELETED YOUR ANSWER!!!";
        }
      ?>
      <ul>
        <form method="POST" action="#">
        <?php
          $squery = $main->query("SELECT A.aid, A.imgfile, A.datetime AS answer_datetime, COUNT(L.user_id) AS like_count, Q.question_id, Q.title, Q.qid 
                                  FROM Answer A LEFT JOIN Likes L ON A.answer_id = L.answer_id 
                                  LEFT JOIN Question Q ON A.question_id = Q.question_id 
                                  WHERE A.user_id = $uid 
                                  GROUP BY A.aid, A.imgfile, A.datetime, Q.question_id, Q.title, Q.qid 
                                  ORDER BY like_count DESC;");
          //$squery = $main->query("SELECT * FROM Question, Users WHERE Question.user_id = Users.user_id");
          $cnt = 0;
          while($arr = mysqli_fetch_assoc($squery)){
            $cnt++;
            echo "<li>Likes : ".$arr['like_count']."<br>
            <a href='qna.php?ques=".$arr['question_id']."'>Answer $cnt: (".$arr['title'].") </a>";
            echo "<pre>".htmlspecialchars(file_get_contents("ans_file/".$arr['aid'].".txt"))."</pre>";
            echo "<p class='question-details'>Answered on <span class='date'>".$arr['answer_datetime']."</span></p>
            <button type='submit' name='delete' value='".$arr['aid']."'>Delete Answer</button>
          </li><hr>";
          }
        ?>
        <!-- More answer -->
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
