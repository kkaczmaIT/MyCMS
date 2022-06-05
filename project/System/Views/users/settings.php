<?php 
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
?>
    <div class="container mt-5" style="min-height: 70vh;">
        <div class="row justify-content-md-center">
            <h2>Zmiana hasła</h2>
            <div class="col-12">
                <div class="alert alert-success d-none" id="msg-result"></div>
                <form>
                    <div class="my-3">
                        <label class="form-label" for="password">Nowe Hasło</label>
                        <input class="form-control" type="password" name="password" id="password" maxlength="20", minlength="8">
                        <span id="password_err" class="text-danger"></span>
                    </div>
                    <div class="my-3">
                        <label class="form-label" for="password_confirm">Powtórz Hasło</label>
                        <input class="form-control" type="password" name="password_confirm" id="password_confirm" maxlength="20", minlength="8">
                        <span id="password_confirm_err" class="error-message text-danger"></span>
                    </div>
                    <input class="btn btn-primary" id="change-password-button" type="submit" onclick="updateUser(event)" value="Zmień hasło">
                </form>
            </div>
        </div>

        <div class="row my-5">
            <div class="col-12">
                <h2>Zablokuj konto</h2>
                <p>Gdy zdecydujesz się zakończyć działalność w serwisie możesz zablokować swoje konto. Uwaga: gdy zablokujesz konto jedyną możliwością odblokowania go jest napisanie wiadomości do centrum obsługi klienta.</p>
                <form>
                    <input type="submit" id="delete-account-button" class="btn btn-danger" onclick="deleteUser(event)" value="Zablokuj konto">
                </form>
            </div>
        </div>
    </div>
<?php
    require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>