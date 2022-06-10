<?php //isLogged() ? header('Location: ' . getenv('CMS_URL') . 'home') : ''?>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
    if(isLogged()) :
?>
<div class="container" style="min-height: 70vh;">
    <div class="row">
        <div class="col-12 d-flex justify-content-start">
            <a href="<?php echo getenv("CMS_URL"); ?>websites/websitespanel" class="btn btn-secondary">Powrót</a>
        </div>
        <div class="col-12">
            <header>
                <h2 class="mt-3 ml-3">Nowa Witryna</h2>
                <div class="alert alert-success d-none" id="msg-result"></div>
                <form method="post">
                    <div class="my-3 justify-content-center d-flex flex-column">
                        <label class="form-label" for="title_website">Tytuł witryny</label>
                        <input class="form-control" type="text" name="title_website" id="title_website" maxlength="100">
                        <span id="title_website_err" class="text-danger"></span>
                    </div>
                    
                    <div class="my-3">
                        <label class="form-label" for="password">Ikona witryny (Upewnij się czy ją wgrałeś do systemu)</label>
                        <select class="form-select" name="shortcut_icon_path" id="shortcut_icon_path">
                            <option value="nan">Wybierz</option>
                            <?php 
                                foreach($data['list_files'] as $file)
                                {
                                    
                                    echo '<option value="' . $file . '">' . $file . '</option>';
                                }
                            ?>
                        </select>
                        <span id="shortcut_icon_path_err" class="text-danger"></span>
                    </div>

                    <div class="my-3 justify-content-center d-flex flex-column">
                        <label class="form-label" for="contact">Adres kontaktowy</label>
                        <input class="form-control" type="email" name="contact" id="contact" maxlength="100">
                        <span id="contact_err" class="text-danger"></span>
                    </div>

                    <input class="btn btn-primary" id="create-website-button" type="submit" onclick="createWebsite(event)" value="Stwórz witrynę">
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