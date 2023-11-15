<?php
  include("functions.php");
  $main = new Main();
  if(!isset($_SESSION["user"])) header('LOCATION:LoginPage.php');
  if(!isset($_GET['ques'])) header('LOCATION:ques.php');
  else $ques = $_GET['ques'];
  
  $user = $_SESSION["user"];
  $name = $_SESSION["name"];
  $uid = $_SESSION["id"];
  $q = $main->query("SELECT * FROM Question, Users WHERE Question.user_id = Users.user_id AND question_id = $ques");
  $qdata = mysqli_fetch_assoc($q);
  $tqid = $qdata["qid"];
  $viewq = $main->query("INSERT INTO Views VALUES('$tqid',$uid)");
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
<body><header>
  <div class="top-bar">
    <div class="logo">
      <img src="your-logo.png" alt="Your Website Logo">
      <h1>ATSS EduQuest</h1>
    </div>
    <!-- Centered Search Bar -->
    <!--<div class="search-bar-centered">
      <input type="text" placeholder="Search...">
      <button type="submit">Search</button>
    </div>-->
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
  <h1>Question and Answer Page</h1>
  <button class="back-button" onclick="goBack()">Back</button>
</header>
  <main>
    <section class="question">
      <h2>Title: <?php echo $qdata['title'];?></h2>
      <p class="question-details">Asked by <span class="username"><?php echo $qdata['username'];?></span> on <span class="date"><?php echo $qdata['datetime'];?></span></p>
      <p>Question:<br>
        <?php
            //$filePath = "quest_file/".$qdata['qid'].".txt";
            //$fileContents = file_get_contents($filePath);
            echo "<pre>".htmlspecialchars(file_get_contents("quest_file/".$qdata['qid'].".txt"))."</pre>";
            if($qdata['imgfile']){
            //$img = "quest_img/".$qdata['qid'].".".$qdata['imgfile'];
            echo "<img src=quest_img/".$qdata['qid'].".".$qdata['imgfile']." height=500px>";
            }
        ?>
        </p>
    </section>
            <section class="new-answer">
      <h3>Your Answer</h3>
      <form action="#" method="post" enctype="multipart/form-data">
        <div>
          <label for="username">Your Username:</label>
          <input type="text" id="username" name="username" value=<?php echo $user;?> required disabled>
        </div>
        <div>
          <label for="answer">Your Answer:</label>
          <textarea id="answer" name="answer" rows="4" required></textarea>
        </div>
        <div>
          <input type="file" name="img">
          <button type="submit" name='submit_ans' value='submit'>Submit Answer</button>
          <?php
            if(isset($_POST["logout"])){
                session_destroy();
                header("LOCATION:LoginPage.php");
            }
            if(isset($_POST["submit_ans"])){
                $errors = array();
                $a = $_POST['answer'];
                $aid = time()."_".$uid;
                $img = 0;
            
                if(!save_ans($a,$aid)){
                  $errors[0] .= "ANS ERROR";
                }else{
                  $errors[0] = 0;
                }
            
                $i_err = save_img($_FILES['img'],$aid,'A');
                if(in_array($i_err,array("jpeg","png","gif",0))){
                  $img = $i_err;
                  $errors[1] = 0;
                }else{
                  $errors[1]= "IMG ERROR = ".$i_err;
                }
            
                if(!$errors[0] && !$errors[1]){
                  $ansq = $main->query("INSERT INTO Answer(aid,question_id,user_id,imgfile)
                                        VALUES('$aid',$ques,$uid,'$img');" );
                  if(!$ansq){
                    echo "FAILED TO UPLOAD Answer!!!".$main->error();
                    del_file("ans_file/".$aid.".txt");
                    del_file("ans_img/".$aid.".".$img);
                  }
                }else{
                  echo $errors[0]."<br>".$errors[1];
                  del_file("ans_file/".$aid.".txt");
                  del_file("ans_img/".$aid.".".$img);
                }
              }
          ?>
        </div>
      </form>
    </section>
     <!-- Answers -->
     <section class="answers">
      <h3>Answers</h3>
      <?php
        $ans = $main->query("SELECT
        A.answer_id,
        A.aid,
        A.user_id,
        U.username,
        A.imgfile,
        A.datetime,
        COUNT(L.user_id) AS like_count
    FROM
        Answer A
    LEFT JOIN
        Likes L ON A.answer_id = L.answer_id
    LEFT JOIN
        Users U ON A.user_id = U.user_id
    WHERE
        A.question_id = $ques
    GROUP BY
        A.answer_id, A.aid, A.user_id, U.username, A.imgfile, A.datetime
    ORDER BY
        like_count DESC;");

        while($ann = mysqli_fetch_assoc($ans)){
            echo "<div class='answer'>
            <p class='answer-details'>Answered by <span class='username'>".$ann['username']."</span> on <span class='date'>".$ann['datetime']."</span></p>";
            echo "<p><pre>".htmlspecialchars(file_get_contents("ans_file/".$ann['aid'].".txt"))."</pre></p>";
            if($ann['imgfile']){
                //$img = "quest_img/".$qdata['qid'].".".$qdata['imgfile'];
                echo "<img src=ans_img/".$ann['aid'].".".$ann['imgfile']." height=500px>";
                }
            echo "<div class='likes'>
              <button class='like-button'>Like</button>
              <span class='like-count'>".$ann['like_count']."</span> Likes
            </div>
          </div>";
        };
      ?>

      <!-- More answer -->
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
