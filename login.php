<?php
session_start();
include "conn.php";

function check($data) {
  global $con;
  $data = trim($data);
  $data = htmlspecialchars($data);
  $data = stripslashes($data);
  $data = mysqli_real_escape_string($con, $data);
  return $data;
}

if (isset($_POST["login"])) {
  $uname = check($_POST['username']);
  $pword = md5($_POST['password']);

  $sqlTenant = "SELECT * FROM tenant WHERE u_name = '$uname' AND p_word = '$pword'";
  $sqlUser = "SELECT * FROM user WHERE u_name = '$uname' AND pword = '$pword'";

  $queryTenant = mysqli_query($con, $sqlTenant);
  $queryUser = mysqli_query($con, $sqlUser);

  $rowTenant = mysqli_fetch_assoc($queryTenant);
  $rowUser = mysqli_fetch_assoc($queryUser);

  $role = $rowUser['role'] ?? null;
  $fname = $rowTenant['fname'] ?? '';
  $lname = $rowTenant['lname'] ?? '';
  $stat = $rowTenant['status'] ?? null;
  $tenant_id = $rowTenant['tenant_id'] ?? null;

  // Contract Info
  if ($tenant_id) {
    $contractResult = mysqli_query($con, "SELECT * FROM contract WHERE tenant_id = '$tenant_id'");
    $contract = mysqli_fetch_assoc($contractResult);
    $end_date = $contract['end_day'] ?? null;
    $house_id = $contract['house_id'] ?? null;
  }

  if (mysqli_num_rows($queryTenant) === 1 || mysqli_num_rows($queryUser) === 1) {
    $_SESSION['username'] = $uname;
    echo "<script>alert('Welcome $fname $lname!');</script>";
    echo '<style>body{display:none;}</style>';

    if ($role === "Administrator") {
      echo '<script>window.location.href = "admin_home.php";</script>';
    } elseif ($role === "Manager") {
      echo '<script>window.location.href = "manager_home.php";</script>';
    } else {
      if ($stat == 0) {
        echo '<script>window.location.href = "initial_payment.php";</script>';
      } elseif ($stat == 1) {
        if (date('Y-m-d') > $end_date) {
          mysqli_query($con, "UPDATE tenant SET status = '3' WHERE tenant_id = '$tenant_id'");
          mysqli_query($con, "UPDATE contract SET status = 'Inactive' WHERE status = 'Active' AND tenant_id = '$tenant_id'");
          mysqli_query($con, "UPDATE house SET status = 'Empty' WHERE house_id = '$house_id'");
          echo "<script>alert('Your contract has expired. Please renew to continue.');</script>";
          echo '<script>window.location.href = "renew_contract.php";</script>';
        } else {
          echo '<script>window.location.href = "home.php";</script>';
        }
      } elseif ($stat == 2) {
        echo '<script>window.location.href = "waiting.php";</script>';
      } elseif ($stat == 3) {
        echo "<script>alert('Your contract has expired. Please renew to continue.');</script>";
        echo '<script>window.location.href = "renew_contract.php";</script>';
      }
    }
    mysqli_close($con);
  } else {
    echo "<script>alert('Incorrect Username or Password!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>USER LOGIN</title>
  <link rel="icon" href="rent.ico">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body style="background-image: linear-gradient(#4e73df, white);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block">
                <img src="house.jpg" alt="Rental House" width="500" height="530" style="opacity:0.6;">
              </div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4"><b>GITANGA DUPLEXES</b><br/>User Login</h1>
                  </div>
                  <form class="user" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                      <input type="text" name="username" class="form-control form-control-user" placeholder="Username" value="<?php echo @$uname; ?>">
                    </div>
                    <div class="form-group">
                      <input type="password" name="password" class="form-control form-control-user" placeholder="Password">
                    </div>
                    <input type="submit" name="login" class="btn btn-primary btn-user btn-block" value="Login">
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="forgot-password.php">Forgot Password?</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="register.php">Create an Account!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
</body>
</html>

