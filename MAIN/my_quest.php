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
  <title>My Questions</title>
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
    <h1>My Questions</h1>
    <button class="back-button" onclick="goBack()">Back</button>
  </header>

  <main>
    <section class="questions-list">
      <h2>All Questions by <?php echo $user;?></h2>
      
      <ul>
        <?php
          $squery = $main->query("SELECT Q.question_id,Q.title, U.username, Q.datetime, COUNT(V.user_id) AS view_count FROM Question Q LEFT JOIN Views V ON Q.qid = V.qid LEFT JOIN Users U ON Q.user_id = U.user_id WHERE U.user_id=$uid GROUP BY Q.question_id, Q.qid, Q.user_id, Q.title, Q.imgfile, Q.datetime, U.username, U.user_type, U.dept, U.name, U.email ORDER BY view_count DESC, Q.datetime DESC;");
          //$squery = $main->query("SELECT * FROM Question, Users WHERE Question.user_id = Users.user_id");
          $cnt = 0;
          while($arr = mysqli_fetch_assoc($squery)){
            $cnt++;
            echo "<li>Views : ".$arr['view_count']."<br>
            <a href='qna.php?ques=".$arr['question_id']."'>Question $cnt: ".$arr['title']."</a>
            <p class='question-details'>Asked on <span class='date'>".$arr['datetime']."</span></p>
          </li><hr>";
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
