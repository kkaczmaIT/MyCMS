<?php
    require_once dirname(__FILE__) . '/system/header.php';
    require_once dirname(__FILE__) . '/system/navbar.php';
    if(isLogged()) :
?>
<div class="container mt-5"  style="min-height: 70vh;">
    <header>
        <h5 class="text-darken d-block">Witaj <?php echo $_SESSION['user_login']; ?></h5>
        <p>Podejmij działanie wybierając pozycję z menu.</p>
    </header>
    
</div>

<?php else : ?>
    <div style="min-height: 70vh;" class="row py-5 my-5 justify-content-center">
        <div class="col-11 ">
            <div class="alert alert-info text-center">Aby korzystać z systemu należy być zalogowany. <a href="<?php echo getenv('CMS_URL'); ?>users/login">Przejdź do strony logowania</a></div>
        </div>
    </div>
<?php endif; ?>

<?php
    require_once dirname(__FILE__) . '/system/footer.php';
?>