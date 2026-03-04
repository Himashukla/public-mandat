{{-- resources/views/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Poll Admin - @yield('title', 'Dashboard')</title>

  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/images/favicon.png') }}">
  <link rel="stylesheet" href="{{ asset('backend/vendor/owl-carousel/css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/vendor/owl-carousel/css/owl.theme.default.min.css') }}">
  <link href="{{ asset('backend/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">

  @stack('styles')
  @livewireStyles
</head>

<body>

  {{-- Preloader --}}
  <div id="preloader">
    <div class="sk-three-bounce">
      <div class="sk-child sk-bounce1"></div>
      <div class="sk-child sk-bounce2"></div>
      <div class="sk-child sk-bounce3"></div>
    </div>
  </div>

  <div id="main-wrapper">

    {{-- Nav Header --}}
    <div class="nav-header">
      <a href="{{ route('admin.dashboard') }}" class="brand-logo">
        <img class="logo-abbr" src="{{ asset('backend/images/logo.png') }}" alt="Logo">
        <img class="logo-compact" src="{{ asset('backend/images/logo-text.png') }}" alt="Logo">
        <img class="brand-title" src="{{ asset('backend/images/logo-text.png') }}" alt="Logo">
      </a>
      <div class="nav-control">
        <div class="hamburger">
          <span class="line"></span>
          <span class="line"></span>
          <span class="line"></span>
        </div>
      </div>
    </div>

    {{-- Header --}}
    <div class="header">
      <div class="header-content">
        <nav class="navbar navbar-expand">
          <div class="collapse navbar-collapse justify-content-between">

            <div class="header-left">
              <div class="search_bar dropdown">
                <span class="search_icon p-3 c-pointer" data-toggle="dropdown">
                  <i class="mdi mdi-magnify"></i>
                </span>
                <div class="dropdown-menu p-0 m-0">
                  <form>
                    <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                  </form>
                </div>
              </div>
            </div>

            <ul class="navbar-nav header-right">

              {{-- Notifications --}}
              <li class="nav-item dropdown notification_dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                  <i class="mdi mdi-bell"></i>
                  <div class="pulse-css"></div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                  <ul class="list-unstyled">
                    <li class="media dropdown-item">
                      <span class="success"><i class="ti-bar-chart"></i></span>
                      <div class="media-body">
                        <a href="{{ route('admin.polls.index') }}">
                          <p>You have <strong>{{ \App\Models\Poll::count() }}</strong> polls created.</p>
                        </a>
                      </div>
                      <span class="notify-time">Now</span>
                    </li>
                    <li class="media dropdown-item">
                      <span class="primary"><i class="ti-check-box"></i></span>
                      <div class="media-body">
                        <a href="{{ route('admin.polls.index') }}">
                          <p><strong>{{ \App\Models\Vote::count() }}</strong> total votes recorded.</p>
                        </a>
                      </div>
                      <span class="notify-time">Now</span>
                    </li>
                  </ul>
                  <a class="all-notification" href="{{ route('admin.polls.index') }}">
                    See all polls <i class="ti-arrow-right"></i>
                  </a>
                </div>
              </li>

              {{-- Profile --}}
              <li class="nav-item dropdown header-profile">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                  <i class="mdi mdi-account"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                  <div class="dropdown-item" style="cursor:default;">
                    <i class="icon-user"></i>
                    <span class="ml-2"><strong>{{ auth()->user()->name }}</strong></span>
                  </div>
                  <div class="dropdown-divider"></div>
                  <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">
                      <i class="icon-key"></i>
                      <span class="ml-2">Logout</span>
                    </button>
                  </form>
                </div>
              </li>

            </ul>
          </div>
        </nav>
      </div>
    </div>

    {{-- Sidebar --}}
    <div class="quixnav">
      <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">

          <li class="nav-label first">Main Menu</li>

          <li class="{{ request()->routeIs('admin.dashboard') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
              <i class="icon icon-single-04"></i>
              <span class="nav-text">Dashboard</span>
            </a>
          </li>

          <li class="nav-label">Poll Management</li>

          <li class="{{ request()->routeIs('admin.polls.*') ? 'mm-active' : '' }}">
            <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
              <i class="icon icon-bar-chart-2"></i>
              <span class="nav-text">Polls</span>
            </a>
            <ul aria-expanded="false">
              <li>
                <a href="{{ route('admin.polls.index') }}"
                  class="{{ request()->routeIs('admin.polls.index') ? 'mm-active' : '' }}">
                  All Polls
                </a>
              </li>
              <li>
                <a href="{{ route('admin.polls.create') }}"
                  class="{{ request()->routeIs('admin.polls.create') ? 'mm-active' : '' }}">
                  Create Poll
                </a>
              </li>
            </ul>
          </li>

          <li>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" style="background:none; border:none; width:100%; text-align:left; padding:0;">
                <a href="javascript:void(0)" onclick="this.closest('form').submit()">
                  <i class="icon icon-power"></i>
                  <span class="nav-text">Logout</span>
                </a>
              </button>
            </form>
          </li>

        </ul>
      </div>
    </div>

    {{-- Content --}}
    <div class="content-body">

      {{-- Flash Messages --}}
      <div class="container-fluid pt-3">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
          </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fa fa-exclamation-circle mr-2"></i> {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
          </button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Please fix the following errors:</strong>
          <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
          </button>
        </div>
        @endif
      </div>

      @yield('content')

    </div>

    {{-- Footer --}}
    <div class="footer">
      <div class="copyright">
        <p>Copyright &copy; {{ date('Y') }} Poll Admin. All rights reserved.</p>
      </div>
    </div>

  </div>

  {{-- Scripts --}}
  <script src="{{ asset('backend/vendor/global/global.min.js') }}"></script>
  <script src="{{ asset('backend/js/quixnav-init.js') }}"></script>
  <script src="{{ asset('backend/js/custom.min.js') }}"></script>
  <script src="{{ asset('backend/vendor/chart.js/Chart.bundle.min.js') }}"></script>
  <script src="{{ asset('backend/vendor/owl-carousel/js/owl.carousel.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('scripts')
  @livewireScripts

</body>

</html>