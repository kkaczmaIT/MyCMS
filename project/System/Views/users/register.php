<?php 
   require_once dirname(dirname(__FILE__)) . '/header.php';
?>
    <div class="container">
        <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
            <div class="form-field">
                <label for="login">Login</label>
                <input type="text" name="login" maxlength="100">
                <span class="error-message"><?php echo $data['login_err']; ?></span>
            </div>
            
            <div class="form-field">
                <label for="password">Hasło</label>
                <input type="password" name="password" maxlength="20", minlength="8">
                <span class="error-message"><?php echo $data['password_err']; ?></span>
            </div>
            <div class="form-field">
                <label for="password_confirm">Powtórz Hasło</label>
                <input type="password" name="password_confirm" maxlength="20", minlength="8">
                <span class="error-message"><?php echo $data['confirm_password_err']; ?></span>
            </div>
            <input type="submit" value="Zarejestruj się">
        </form>
    </div>
<?php
    require_once dirname(dirname(__FILE__)) . '/footer.php';
?>