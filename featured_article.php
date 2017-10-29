<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Featured Article Count</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>

  <body>
    <div class="jumbotron" style="border-radius: 0px; text-align: center;">
    
      <h4 class="display-4">Shows number of featured articles where the user is among the top 10 editors</h4><hr/>
     
      <form class="" id="myForm" action="#" method="post">
        <div class="input-group col-sm-6" style="margin:auto;">
      <input type="text" class="form-control" name='user' aria-label="Text input with dropdown button" placeholder="Enter user name">
      <div class="input-group-btn">
 <input type="submit" class="btn btn-warning " aria-haspopup="true" aria-expanded="false">
          Action
        </button>
     
    </div>
      </form>
    </div>
  </div><br/>
<?php
  function getUserList() {
    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
    $mysqli = new mysqli('enwiki.labsdb', $ts_mycnf['user'], $ts_mycnf['password'], 'enwiki_p');
    if ($mysqli->connect_error) {
      echo "Connection failed: " . $mysqli->connect_error;
      return;
    }
    // Fetches the list of featured articles.
    $sql1 = "SELECT cl_from from categorylinks where cl_to = 'Featured_articles'";
    $res1 = $mysqli->query($sql1);
    if ($res1 == false) {
      echo 'The query failed.';
      return;
    }
    while($row1 = $res1->fetch_assoc()) {
      $pageid = $row1["cl_from"];
      // Fetches the top 10 contributors of a featured article.
      $sql2 = "SELECT rev_user from revision where rev_page = '$pageid' group by rev_user order by sum(rev_len) desc limit 10";
      $res2 = $mysqli->query($sql2);
      if ($res2 == false) {
        echo 'The query failed.';
        return;
      }
      while($row2 = $res2->fetch_assoc()) {
        $users[$row2["rev_user"]]++;
      }
    }
    $myfile = fopen("data.txt", "w");
    foreach ($users as $key => $value) {
      $txt = $key."=".$value."\n";
      fwrite($myfile, $txt);
    }
    
    fclose($myfile);
    
    $mysqli->close($myfile);
    
    return;
  }
  getUserList();
?> 
  </body>
</html>