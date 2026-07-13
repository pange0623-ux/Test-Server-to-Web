<?php
//index in user folder, redirect to login page
?>

<!DOCTYPE html>
<html>

<head>
  
  <title>User</title>
  <style>
    body {
      display: flex;
      align-items: center;
      align-content: center;
      justify-content: center;
      background-color: black;
    }

  </style>
</head>

<body>
<h1>private page</h1>
</body>

<script>
  window.addEventListener("load", function() {
    setTimeout(function() {
      window.location.href = "login.php";
    }, 1);
  });
</script>

</html>