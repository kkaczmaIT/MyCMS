<?php //isLogged() ? header('Location: ' . getenv('CMS_URL') . 'home') : ''?>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
    if(isLogged()) :
?>
<?php if(!isset($data['ID'])) : ?>
<div class="container" style="min-height: 70vh;">
    <div class="row">
        <div class="col-12 d-flex justify-content-start">
            <a href="<?php echo getenv("CMS_URL"); ?>websites/addwebsite" class="btn btn-secondary">Dodaj witrynę</a>
        </div>
        <div class="col-12">
            <header>
                <h2 class="mt-3 ml-3">Witryny</h2>
                <div id="websites-gallery" class="row"></div>
            </header>
        </div>
    </div>
</div>
<?php elseif(is_numeric($data['ID'])) : ?>
    <div class="container" style="min-height: 70vh;">
    <?php  $_SESSION['website_id'] = $data['ID']; ?>
    <div class="d-none" id="website-id"><?php echo $data['ID']; ?></div>
    <div class="row">
        <div class="col-12 d-flex justify-content-start">
            <a href="<?php echo getenv("CMS_URL"); ?>websites/websitespanel" class="btn btn-secondary">Powrót</a>
        </div>
        <div class="col-12">
            <header>
                <h2 class="mt-3 ml-3">Witryna</h2>
                </div>
            </header>
            <article>
            <div id="website-details" class="list-group" >
                <div class="row my-3">
                    <div class="col-3"><a href="<?php echo getenv('CMS_URL') . "websites/editwebsite/" . $data['ID']; ?>" class="btn btn-primary"> Edytuj dane</a></div>
                    <div class="col-3"><a id="btn-settings" href="<?php echo getenv('CMS_URL') . "websites/settings/"; ?>" class="btn btn-info">Ustawienia</a></div>
                    <div class="col-3"><a href="<?php echo getenv('CMS_URL') . "pageswebs/pageslist"; ?>" class="btn btn-warning">Strony</a></div>
                    <div class="col-3">
                        <a id="change-status-website" data-status="" class="btn btn-danger" onclick="changeStatusWebsite(event)">Zablokuj Witrynę</a>
                        <!-- JS gdy is_active 0 btn-success aktywuj witrynę -->
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
<?php endif; ?>
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