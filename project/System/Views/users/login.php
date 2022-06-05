<?php isLogged() ? header('Location: ' . getenv('CMS_URL') . 'home') : ''?>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
?>
<div class="container mt-5" style="min-height: 70vh;">
        <div class="row justify-content-md-center">
            <div class="col-12">
            <div class="alert alert-danger d-none" id="msg-result"></div>
                <form method="post" id="login-form" onsubmit="loginU(event)">
                    <div class="my-3 justify-content-center d-flex flex-column">
                        <label class="form-label" for="login">Login</label>
                        <input class="form-control" type="text" id="login" name="login" maxlength="100">
                        <span id="login_err" class="error-message text-danger"></span>
                    </div>
                    
                    <div class="my-3">
                        <label class="form-label" for="password">Hasło</label>
                        <input class="form-control" type="password" id="password" name="password" maxlength="20", minlength="8">
                        <span id="password_err" class="error-message text-danger"></span>
                    </div>
                    <input class="btn btn-primary" id="login-button" type="submit" onclick="loginU(event)" value="Zaloguj się">
                </form>
            </div>
        </div>
    </div>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>