<?php //isLogged() ? header('Location: ' . getenv('CMS_URL') . 'home') : ''?>
<?php
   require_once dirname(dirname(__FILE__)) . '/system/header.php';
   require_once dirname(dirname(__FILE__)) . '/system/navbar.php';
    if(isLogged()) :
?>
    <div class="container" style="min-height: 70vh;">
    <div class="row">
        <div class="col-12 d-flex justify-content-start">
            <a href="<?php echo getenv("CMS_URL"); ?>menus/menupanel/<?php echo $_SESSION['menu_id']; ?>" class="btn btn-secondary">Powrót</a>
        </div>
        <div class="col-12">
        <div class="alert alert-success my-2 d-none" id="msg-result"></div>
            <header>
                <h2 class="mt-3 ml-3">Element menu</h2>
                </div>
            </header>
            <article>
                <div class="row">
                    <div class="col-12">
                        <a class="btn btn-danger" id="btn-delete-listitem" onclick="deleteListItem(event)">Usuń element</a>
                    </div>
                </div>
            <div id="listitem-details" class="list-group" >
                <div class="row my-2">
                    <h4>Edytuj link</h4>
                    <form>
                        <label for="text_link">Tekst pozycji menu</label>
                        <input type="text" id="text_link" name="text_link" class="form-control">
                        <span class="w-100 d-block text-danger" id="text_link_err"></span>
                        <label for="href">Link</label>
                        <input type="text" id="href" name="href" class="form-control">
                        <span class="w-100 d-block text-danger" id="href_err"></span>
                        <label for="depth">Poziom menu</label>
                        <input type="number" id="depth" name="depth" value="1" class="form-control">
                        <span class="w-100 d-block text-danger" id="depth_err"></span>
                        <label for="order_item">pozycja linku</label>
                        <input type="number" id="order_item" name="order_item" class="form-control">
                        <span class="w-100 d-block text-danger" id="order_item_err"></span>
                        <input class=" mt-4 btn w-100 btn-primary" id="btn-edit-linkitem" onclick="editLink(event)" type="submit" value="Zaktualizuj link">
                    </form>
                    <div id="list-links" class="list-group my-3">

                    </div>
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
    require_once 'menuFooter.php';
    require_once dirname(dirname(__FILE__)) . '/system/footer.php';
?>