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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Answers</title>
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
    <div class="user-tab" id="userTab">
      <span>Welcome, <?php echo $user;?></span>
      <div class="user-options">
        <ul>
          <li><a href="#">Manage Users</a></li>
          <li><a href="admin_ques.php">Manage Questions</a></li>
          <li><a href="LoginPage.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
  <h1>Manage Answers</h1>
  <button class="back-button" onclick="goBack()">Back</button>
</header>
  <main>
    <section class="question">
      <h3>
        <?php
          if(isset($_POST['delete'])){
            $answer_id = $_POST['delete'];
            $AnsQ = $main->query("SELECT aid,imgfile FROM Answer");
            $AnsD = mysqli_fetch_assoc($AnsQ);
            del_file("ans_file/".$AnsD['aid'].".txt");
            del_file("ans_img/".$AnsD['aid'].".".$AnsD['imgfile']);
            $aid = $AnsD['aid'];
            $DeleteA = $main->query("DELETE A, L
                                    FROM Answer A
                                    LEFT JOIN Likes L ON A.answer_id = L.answer_id
                                    WHERE A.aid = '$aid';");
            if($DeleteA){
              echo "DELETED ANSWER ID: $answer_id";
            }
          }
        ?>
      </h3>
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
                              FROM Answer A
                              LEFT JOIN Likes L ON A.answer_id = L.answer_id
                              LEFT JOIN Users U ON A.user_id = U.user_id
                              WHERE A.question_id = $ques
                              GROUP BY A.answer_id, A.aid, A.user_id, U.username, A.imgfile, A.datetime
                              ORDER BY like_count DESC;");

        while($ann = mysqli_fetch_assoc($ans)){
            echo "<div class='answer'>
            <p class='answer-details'>Answered by <span class='username'>".$ann['username']."</span> on <span class='date'>".$ann['datetime']."</span></p>
            ";
            echo "<p><pre>".htmlspecialchars(file_get_contents("ans_file/".$ann['aid'].".txt"))."</pre></p>";
            if($ann['imgfile']){
                //$img = "quest_img/".$qdata['qid'].".".$qdata['imgfile'];
                echo "<img src=ans_img/".$ann['aid'].".".$ann['imgfile']." height=500px>";
                }
            echo "<div class='likes'>
              <button class='like-button' type=submit name=delete value=".$ann['answer_id'].">Delete Answer</button>
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
  </form>
</body>
</html>
