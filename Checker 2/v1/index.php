<!DOCTYPE html>
<html lang="en-gb" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login </title>
    <link rel="shortcut icon" type="image/png" href="https://via.placeholder.com/16x16">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="../css/hyper.css?v=1.2" />
    <script src="../js/uikit.js"></script>
    <?php
    include "../config.php";

    session_start();
    $userLogged = false;
    if (isset($_SESSION['username'])) {
        $userLogged = true;
    }
    if ($userLogged === true) {
        return header("location: ./dashboard");
    }
    if (isset($_POST['login'])) {
        $password = $_POST['access'];
        $sel = "SELECT * from users where password = '$password' ";
        $res = mysqli_query($connect, $sel);
        if (mysqli_num_rows($res) <= 0) {
            return header("location: ./?message=no_account&retry=true");
        }
        while ($user = mysqli_fetch_assoc($res)) {
            if ($user['status'] === "banned") {

                return header("location: ./?message=banned_account");
            }elseif($user['ip']!== "hyperip" && $hyper->sw_string($user['ip'], $hyper->ip_range($hyper->get_user_ip())) !== True ){
                return header("location: ./?message=unkown_ip&__reason=ip_address_not_matched&ip_rang=mismatched");
            } else {
                $_SESSION['username'] = $user['username'];
                $_SESSION['status'] = $user['status'];
                $_SESSION['access'] = $user['password'];
                $_SESSION['roles'] = $user['roles'];
                $connect->close();
                header("location: ./dashboard?success=user_logged&auth_token=tok_" . $hyper->toLowercase($hyper->gen_tok(30)) . "");
            }
        }
    }





    ?>
</head>

<body>

    <?php include "./include/header.php"; ?>

    <div class="uk-section uk-section-muted">
        <div class="uk-container">
            <div class="uk-background-default uk-border-rounded uk-box-shadow-small">
                <div class="uk-container uk-container-xsmall uk-padding-large">
                    <article class="uk-article">
                        <h1 class="uk-article-title">Flex Access</h1>
                        <div class="uk-article-content">
                            <?php

                            if ($hyper->get_parameter("message", "no_account")) {
                                echo $hyper->create_notice_bar(
                                    array(
                                        "text" => "No account found.",
                                        "css" => "text-warn",
                                        "ele"=>"p"
                                    )
                                );
                            } else if ($hyper->get_parameter("message", "banned_account")) {
                                echo $hyper->create_notice_bar(
                                    array(
                                        "text" => "Your account has been banned",
                                        "css" => "text-danger",
                                        "ele"=>"p"
                                    )
                                );
                            } else if ($hyper->get_parameter("message", "unkown_ip")) {
                                echo $hyper->create_notice_bar(
                                    array(
                                        "text" => "Unauthorized ip address (".$hyper->get_user_ip().")",
                                        "css" => "text-danger",
                                        "ele"=>"p"
                                    )
                                );
                            }

                            ?>
                            <p class="uk-text-lead uk-text-muted">dont have access code create <a href="./create" class="access_link">here</a></p>
                            <form class="uk-form-stacked uk-margin-medium-top" method="POST" action="">
                                <div class="uk-margin-bottom">
                                    <label class="uk-form-label" for="name">Secret Code</label>
                                    <div class=" hyper_login uk-form-controls">
                                        <input id="name" class="hyper_input uk-input uk-border-rounded" name="access" type="text" placeholder="Enter Secret Code Here" required>
                                    </div>
                                </div>

                                <div class="uk-text-center">
                                    <input class="uk-button uk-button-primary uk-border-rounded" name="login" type="submit" value="Get Access">
                                </div>
                            </form>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>

    <?php include './include/footer.php'; ?>

    <script src="../js/awesomplete.js"></script>
    <script src="../js/custom.js"></script>


</body>

</html>