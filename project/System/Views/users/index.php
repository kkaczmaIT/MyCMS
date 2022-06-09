<?php 
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
?>
    <?php if(!$data['ID']) :  ?>
    <div class="container mt-5" style="min-height: 70vh;">
        <header>
            <h1>Użytkownicy</h1>
            <p class="lead">Lista użytkowników w systemie. Możesz się z nimi skontaktować.</p>
        </header>
        <article>
        <div id="users-list" class="list-group">
        </div>
        </article>
    </div>
    <?php else : ?>

        <div class="container my-5" style="min-height: 70vh;">
        <article>
            <aside>
                <a href="<?php echo getenv('CMS_URL'); ?>users/userslist" class="btn btn-secondary my-3">Powróć do listy użytkowników</a>
            </aside>
            <header>
                <h1>Użytkownik</h1>
                <p class="lead">Dane użytkownika</p>
            </header>
            <section id="details">
                <div id="user-id" class="d-none"><?php echo $data['ID']; ?></div>
                    <div id="user-details" class="list-group">
                </div>
            </section>
    
            <section id="contact" class="my-5">
                <a href="#" class="btn btn-secondary">Napisz wiadomość</a>
            </section>
        </article>
    </div>
<?php
    endif;
    require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>
   