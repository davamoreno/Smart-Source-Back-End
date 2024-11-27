<nav class="navbar navbar-expand-lg">
    <div class="navbar-container container-fluid">
        <a class="navbar-brand">
            <img src="{{ asset('svg/logo.svg') }}" alt="" class="navbar-icon">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-list collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="#">Category</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="#">Repository</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="#">About Us</a>
              </li>
              {{-- <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Dropdown
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Action</a></li>
                  <li><a class="dropdown-item" href="#">Another action</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
              </li> --}}
            </ul>
            <div class="d-flex ms-5 navbar-btn">
                <button class="btn  me-auto btn-primary">
                    <a href="" style="color: #FFFFFF">Sign Up</a>
                </button>
                <button class="btn ms-4 btn-outline-primary">
                    <a href="">Login</a>
                </button>
            </div>
        </div>
    </div>
</nav>