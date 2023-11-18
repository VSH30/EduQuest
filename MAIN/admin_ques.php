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
  <title>Manage Questions</title>
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
        <h1>ATSS EduQuest (Admin)</h1>
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
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">Manage Questions</a></li>
            <li><a href="LoginPage.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
    <h1>Manage Questions</h1>
    <button class="back-button" onclick="location.href='LoginPage.php'">LogOut</button>
  </header>

  <main>
    <section class="questions-list">
      <h3>
          <?php
             if(isset($_POST['delete'])){
                $question_id = $_POST['delete'];
                $quesQ = $main->query("SELECT qid, imgfile FROM Question WHERE question_id=$question_id");
                $quesD = mysqli_fetch_assoc($quesQ);

                $ansQ = $main->query("SELECT aid, imgfile FROM Answer WHERE question_id=$question_id");
                while($ans = mysqli_fetch_assoc($ansQ)){
                    del_file("ans_file/".$ans['aid'].".txt");
                    del_file("ans_img/".$ans['aid'].".".$ans['imgfile']);
                }

                del_file("quest_file/".$quesD['qid'].".txt");
                del_file("quest_img/".$quesD['qid'].".".$quesD['imgfile']);

                $DelteQ = $main->query("DELETE Q, A, V, L
                                        FROM Question Q
                                        LEFT JOIN Answer A ON Q.question_id = A.question_id
                                        LEFT JOIN Views V ON Q.qid = V.qid
                                        LEFT JOIN Likes L ON A.answer_id = L.answer_id
                                        WHERE Q.question_id = $question_id;
                                        ");
                if($DelteQ)
                    echo "DELETED Question ID:".$question_id;
             }
          ?>
      </h3>
      <h2>All Questions</h2>
      <form method=POST action=#>
      <ul>
        <?php
          $squery = $main->query("SELECT Q.question_id,Q.title,Q.qid,Q.imgfile, U.username, Q.datetime, COUNT(V.user_id) AS view_count 
                                  FROM Question Q 
                                  LEFT JOIN Views V ON Q.qid = V.qid 
                                  LEFT JOIN Users U ON Q.user_id = U.user_id 
                                  GROUP BY Q.question_id, Q.qid, Q.user_id, Q.title, Q.imgfile, Q.datetime, U.username, U.user_type, U.dept, U.name, U.email 
                                  ORDER BY Q.question_id ASC;");
          //$squery = $main->query("SELECT * FROM Question, Users WHERE Question.user_id = Users.user_id");
          $cnt = 0;
          if(isset($_POST['search'])){
            while($arr = mysqli_fetch_assoc($squery)){
              if(stristr($arr['title'],$_POST['srch'])){
                $cnt++;
              echo "<li>Views : ".$arr['view_count']."<br>
              <a href='admin_qna.php?ques=".$arr['question_id']."'>Question ID ".$arr['question_id']."(".$arr['qid'].",".$arr['imgfile']."): ".$arr['title']."</a>
              <p class='question-details'>Asked by <span class='username'>".$arr['username']."</span> on <span class='date'>".$arr['datetime']."</span></p><input type=submit name=delete value=".$arr['question_id'].">
            </li><hr>";
              }
            }
          }else{
            while($arr = mysqli_fetch_assoc($squery)){
              $cnt++;
              echo "<li>Views : ".$arr['view_count']."<br>
              <a href='admin_qna.php?ques=".$arr['question_id']."'>Question ID ".$arr['question_id']."(".$arr['qid'].",".$arr['imgfile']."): ".$arr['title']."</a>
              <p class='question-details'>Asked by <span class='username'>".$arr['username']."</span> on <span class='date'>".$arr['datetime']."</span></p><input type=submit name=delete value=".$arr['question_id'].">
            </li><hr>";
            }
          }
        ?>
        <!-- More question -->
      </ul>
      </form>
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
