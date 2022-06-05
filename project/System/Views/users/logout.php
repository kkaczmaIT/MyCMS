
<?php
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
?>
<div class="container mt-5"  style="min-height: 70vh;">
        <div class="row justify-content-md-center">
            <div class="col-12">
            <div class="alert alert-success">Wylogowano pomyślnie</div>
            <a href="<?php getenv('CMS_URL'); ?>login" class="btn btn-info">Wróć na stronę logowania</a>
            </div>
        </div>
    </div>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>