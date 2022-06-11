<?php //isLogged() ? header('Location: ' . getenv('CMS_URL') . 'home') : ''?>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
    if(isLogged()) :
?>
    <div class="container" style="min-height: 70vh;">
    <div class="row">
        <div class="col-12 d-flex justify-content-start">
            <a href="<?php echo getenv("CMS_URL"); ?>pageswebs/pageslist" class="btn btn-secondary">Powrót</a>
        </div>
        <div class="col-12">
        <div class="alert alert-success my-2 d-none" id="msg-result"></div>
            <header>
                <h2 class="mt-3 ml-3">Nowa strona</h2>
                </div>
            </header>
            <article>
            <div id="page-details" class="list-group" >
                <div class="row my-2">
                    <form>
                        <label for="title">Tytuł</label>
                        <input type="text" id="title" name="title" class="form-control">
                        <span class="text-danger" id="title_err"></span>
                        <label for="keyphrases">Słowa kluczowe</label>
                        <input type="text" name="keyphrases" id="keyphrases" class="form-control">
                        <span class="text-danger" id="keyphrases_err"></span>
                        <label for="description_meta">Opis strony</label>
                        <textarea class="form-control" name="description_meta" id="description_meta" ></textarea>
                        <span class="text-danger" id="description_meta_err"></span>
                        <label for="content">Treść główna</label>
                        <textarea class="form-control" name="content" id="content" cols="30" rows="20"></textarea>
                        <span class="text-danger" id="content_err"></span>
                        <label for="footer_text">Tekst w stopce</label>
                        <input type="text" name="footer_text" id="footer_text" class="form-control">
                        <span class="text-danger" id="footer_text_err"></span>
                        <input class=" mt-4 btn w-100 btn-primary" id="btn-updatepage" onclick="createPage(event)" type="submit" value="Stwórz stronę">
                    </form>
                </div>
            </article>
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
    require_once 'pagesFooter.php';
    require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>