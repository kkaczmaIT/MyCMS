<?php 
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
?>
    <div class="container mt-5"  style="min-height: 70vh;">
        <div class="row justify-content-md-center">
            <div class="col-12">
                <div class="alert alert-success d-none" id="msg-result"></div>
                <form method="post">
                    <div class="my-3 justify-content-center d-flex flex-column">
                        <label class="form-label" for="login">Login</label>
                        <input class="form-control" type="text" name="login" id="login" maxlength="100">
                        <span id="login_err" class="text-danger"><?php echo $data['login_err']; ?></span>
                    </div>
                    
                    <div class="my-3">
                        <label class="form-label" for="password">Hasło</label>
                        <input class="form-control" type="password" name="password" id="password" maxlength="20", minlength="8">
                        <span id="password_err" class="text-danger"><?php echo $data['password_err']; ?></span>
                    </div>
                    <div class="my-3">
                        <label class="form-label" for="password_confirm">Powtórz Hasło</label>
                        <input class="form-control" type="password" name="password_confirm" id="password_confirm" maxlength="20", minlength="8">
                        <span id="password_confirm_err" class="error-message text-danger"><?php echo $data['confirm_password_err']; ?></span>
                    </div>
                    <input class="btn btn-primary" id="registration-button" type="submit" onclick="registration(event)" value="Zarejestruj się">
                </form>
            </div>
        </div>
        
    </div>
<?php
    require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>