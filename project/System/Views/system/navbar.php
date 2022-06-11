<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo getenv('CMS_URL');?>home"><?php echo getenv('APPNAME'); ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#system-nav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-md-end justify-content-sm-start" id="system-nav">
      <ul class="navbar-nav justify-content-end ml-3">
          <?php if(isLogged()) : ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo getenv('CMS_URL'); ?>home">Panel</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo getenv('CMS_URL'); ?>users/userslist">Inni użytkownicy</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo getenv('CMS_URL'); ?>websites/websitespanel">Witryny</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo getenv('CMS_URL'); ?>users/settings">Ustawienia</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo getenv('CMS_URL'); ?>users/logout">Wyloguj</a>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo getenv('CMS_URL'); ?>users/login">Zaloguj</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo getenv('CMS_URL'); ?>users/registerUser">Zarejestruj się</a>
            </li>
          <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>