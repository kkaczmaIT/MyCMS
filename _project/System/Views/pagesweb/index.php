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
            <a href="<?php echo getenv("CMS_URL"); ?>pageswebs/addpage" class="btn btn-secondary">Dodaj stronę</a>
        </div>
        <div class="col-12">
            <header>
                <h2 class="mt-3 ml-3">Strony Witryny</h2>
                <div class="row">
                    <div class="col-12">
                        <div id="pages-list" class="list-group">

                        </div>
                    </div>
                </div>
            </header>
        </div>
    </div>
</div>
<?php elseif(is_numeric($data['ID'])) : ?>
    <div class="container" style="min-height: 70vh;">
    <div class="row">
        <div class="col-12 d-flex justify-content-start">
            <a href="<?php echo getenv("CMS_URL"); ?>pageswebs/pageslist" class="btn btn-secondary">Powrót</a>
        </div>
        <div class="col-12">
        <div class="alert alert-success my-2 d-none" id="msg-result"></div>
            <header>
                <h2 class="mt-3 ml-3">Strona witryny</h2>
                </div>
            </header>
            <article>
            <div id="page-details" class="list-group" >
                <div class="row my-3">
                    <div class="col-3"><a id="btn-menu" href="<?php echo getenv('CMS_URL') . "menus/menupanel/" ?>" class="btn btn-primary"> Menu</a></div>
                    <div class="col-3">
                        <a id="btn-delete-page" data-id="" class="btn btn-danger" onclick="deletePage(event)">Usuń stronę</a>
                        <!-- JS gdy is_active 0 btn-success aktywuj witrynę -->
                    </div>
                </div>
                <div class="row my-2">
                    <form>
                        <label for="title">Tytuł</label>
                        <input type="text" id="title" name="title" class="form-control">
                        <label for="keyphrases">Słowa kluczowe</label>
                        <input type="text" name="keyphrases" id="keyphrases" class="form-control">
                        <label for="description_meta">Opis strony</label>
                        <textarea class="form-control" name="description_meta" id="description_meta" ></textarea>
                        <label for="content">Treść główna</label>
                        <textarea class="form-control" name="content" id="content" cols="30" rows="20"></textarea>
                        <label for="footer_text">Tekst w stopce</label>
                        <input type="text" name="footer_text" id="footer_text" class="form-control">
                        <input class=" mt-4 btn w-100 btn-primary" id="btn-updatepage" onclick="updatePage(event)" type="submit" value="Zaktualizuj stronę">
                    </form>
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
    require_once 'pagesFooter.php';
    require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>