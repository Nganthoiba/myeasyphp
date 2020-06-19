<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <a class="navbar-brand" href=javascript:void(0);">MyEasyPHP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item <?= isLinkActive("Default") ?>">
                <a class="nav-link" href="<?= getHtmlLink("Default") ?>">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item <?= isLinkActive(getHtmlLink("Default","About")) ?>">
                <a class="nav-link" href="<?= getHtmlLink("Default","About") ?>">About</a>
            </li>
            <li class="nav-item <?= isLinkActive(getHtmlLink("Contacts","index")) ?>">
                <a class="nav-link" href="<?= getHtmlLink("Contacts","index") ?>">Contact</a>
            </li>
            <li class="nav-item <?= isLinkActive(getHtmlLink("Default","Dashboard")) ?>">
                <a class="nav-link" href="<?= getHtmlLink("Default","Dashboard") ?>">Dashboard</a>
            </li>
        </ul>
      <form class="form-inline mt-2 mt-md-0">
        <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= getHtmlLink("Account","Signin") ?>">Sign In</a>
            </li>
        </ul>
    </div>
</nav>

