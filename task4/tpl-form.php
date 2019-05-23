<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task 4</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <style>
        form {
            width: 300px;
            margin: 40px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <form id="login-form" action="task4.php" method="post" data-public="<?php echo $auth->getPublicKey(); ?>">
            <input id="form-encrypted" type="hidden" name="encrypted" value="">
            <div class="form-group">
                <label for="form-username">Username</label>
                <input id="form-username" type="text" name="username" placeholder="Username" class="form-control">
            </div>
            <div class="form-group">
                <label for="form-password">Password</label>
                <input id="form-password" type="password" name="password" placeholder="Password" class="form-control">
            </div>
            <input type="submit" value="Submit" class="btn btn-submit">
        </form>
    </div>
    <script>
        document.querySelector('#login-form').addEventListener('submit', function (e) {
            var formElement = e.target;
            var encryptedInputElement = formElement.querySelector('#form-encrypted]');
            var passwordElement = formElement.querySelector('#form-password');
            var publicKey = formElement.dataset.public;

            // Вот тут бросил
            // var encryptedPassword = window.crypto.subtle.encrypt(algorithm, key, data);

            encryptedInputElement.value = encryptedPassword;
        })
    </script>
</body>
</html>