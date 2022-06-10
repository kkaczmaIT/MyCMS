<?php //isLogged() ? header('Location: ' . getenv('CMS_URL') . 'home') : ''?>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
    if(isLogged()) :
?>
<div class="container" style="min-height: 70vh;">
    <div class="row">
        <div class="col-12 d-flex justify-content-start">
            <a href="<?php echo getenv("CMS_URL"); ?>websites/websitespanel/<?php echo $_SESSION['website_id']; ?>" class="btn btn-secondary">Powrót do witryny</a>
        </div>
        <div class="col-12">
            <header>
                <h2 class="mt-3 ml-3">Ustawienia witryny</h2>
                <div class="alert alert-success d-none" id="msg-result"></div>
                <form method="post">
                    <div class="my-3 justify-content-center d-flex flex-column">
                        <label class="form-label" for="PHP_version">Wersja PHP</label>
                        <input class="form-control" type="text" name="PHP_version" id="PHP_version" maxlength="100" read-only disabled>
                    </div>
                    
                    <div class="my-3">
                        <label class="form-label" for="limit_upload_file_size">Dozwolona wielkość plików wgrywanych do systemu</label>
                        <input type="number" class="form-control" name="limit_upload_file_size" id="limit_upload_file_size">
                        <span id="limit_upload_file_size_err" class="text-danger"></span>
                    </div>

                    <div class="my-3">
                        <label class="form-label" for="contact">Dozwolona wielkość plików wgrywanych do systemu</label>
                        <input type="text" class="form-control" name="contact" id="contact">
                        <span id="contact_err" class="text-danger"></span>
                    </div>

                    <input class="btn btn-primary" id="updated-settings-button" type="submit" onclick="updateWebsiteSettings(event)" value="Zaktualizuj ustawienia">
                </form>
        </div>
            </header>
        </div>
    </div>
</div>

<?php else :?>
    <div class="container">
    <div class="row">
        <div class="col">
            Nie jesteś zalogowany
        </div>
    </div>
</div>
<?php
    endif;
    require_once 'websiteFooter.php';
    require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>