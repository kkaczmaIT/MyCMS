<?php
    require_once dirname(__FILE__) . '/system/header.php';
    require_once dirname(__FILE__) . '/system/navbar.php';
    if(isLogged()) :
?>
<div class="container mt-5"  style="min-height: 70vh;">
    <header>
        <h5 class="text-darken d-block">Witaj <?php echo $_SESSION['user_login']; ?></h5>
        <p>Poniżej znajdują się aktywne witryny.</p>
    </header>
    <section id="websites-list">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Witryna o żeglarstwie</h4>
                    </div>
                    <img class="w=100 card-img-top" src="<?php echo getenv('STORAGE_URL') . 'user457b774effe4a349c6dd82ad4f4f21d34c/shortcut_icon.jpg'; ?>" alt="ikona witryn">
                    
                    <div class="card-body">
                        <h5>Parametry witryny w skrócie</h5>
                        <ul class="list">
                            <li class="list-item">słowa kluczowe</li>
                            <li class="list-item">opis</li>
                            <li class="list-item">prowadzona od</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Witryna o zjawiskach w kosmosie</h4>
                    </div>
                    <img class="w=100 card-img-top" src="<?php echo getenv('STORAGE_URL') . 'user457b774effe4a349c6dd82ad4f4f21d34c/shortcut_icon.jpg'; ?>" alt="ikona witryn">
                    
                    <div class="card-body">
                        <h5>Parametry witryny w skrócie</h5>
                        <ul class="list">
                            <li class="list-item">słowa kluczowe</li>
                            <li class="list-item">opis</li>
                            <li class="list-item">prowadzona od</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php else : ?>
    <div class="row py-5 my-5 justify-content-center">
        <div class="col-11 ">
            <div class="alert alert-info text-center">Aby korzystać z systemu należy być zalogowany. <a href="<?php echo getenv('CMS_URL'); ?>users/login">Przejdź do strony logowania</a></div>
        </div>
    </div>
<?php endif; ?>

<?php
    require_once dirname(__FILE__) . '/system/footer.php';
?>