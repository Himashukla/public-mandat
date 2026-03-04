<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Polls') — PollApp</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @vite(['resources/js/app.js'])

  <style>
    * {
      font-family: 'Inter', sans-serif;
    }

    body {
      background: #f0f2f8;
      min-height: 100vh;
    }

    .navbar-frontend {
      background: #ffffff;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
      padding: 14px 0;
    }

    .navbar-brand-text {
      font-size: 22px;
      font-weight: 700;
      color: #6259ca;
      letter-spacing: -0.5px;
    }

    .navbar-brand-text span {
      color: #2BC155;
    }

    .hero-section {
      background: linear-gradient(135deg, #6259ca 0%, #9b8ff7 100%);
      padding: 60px 0 80px;
      color: white;
      text-align: center;
      clip-path: ellipse(100% 85% at 50% 15%);
      margin-bottom: -30px;
    }

    .hero-section h1 {
      font-size: 38px;
      font-weight: 700;
      margin-bottom: 12px;
    }

    .hero-section p {
      font-size: 16px;
      opacity: 0.85;
      max-width: 500px;
      margin: 0 auto;
    }

    .poll-card {
      background: #ffffff;
      border-radius: 16px;
      border: none;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      overflow: hidden;
      height: 100%;
    }

    .poll-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .poll-card-header {
      background: linear-gradient(135deg, #6259ca, #9b8ff7);
      padding: 20px;
      color: white;
    }

    .poll-card-header h5 {
      font-size: 16px;
      font-weight: 600;
      margin: 0;
      line-height: 1.4;
    }

    .poll-card-body {
      padding: 20px;
    }

    .poll-votes-badge {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      font-size: 12px;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 20px;
    }

    .poll-date {
      font-size: 12px;
      color: #aaa;
    }

    .admin-by {
      font-size: 12px;
      color: #aaa;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .admin-by i {
      color: #9b8ff7;
    }

    .status-pill {
      font-size: 11px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 20px;
    }

    .status-pill.active {
      background: rgba(255, 255, 255, 0.2);
      color: white;
    }

    .status-pill.closed {
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.7);
    }

    .btn-vote {
      background: linear-gradient(135deg, #6259ca, #9b8ff7);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 9px 20px;
      font-size: 14px;
      font-weight: 600;
      width: 100%;
      transition: opacity 0.2s;
    }

    .btn-vote:hover {
      opacity: 0.9;
      color: white;
    }

    .vote-option-item .d-flex:hover {
      border-color: #6259ca !important;
      background: #f5f4ff !important;
      cursor: pointer;
    }

    .empty-state {
      text-align: center;
      padding: 80px 20px;
    }

    .empty-state i {
      font-size: 60px;
      color: #d0d0e8;
      margin-bottom: 20px;
    }

    .empty-state h5 {
      color: #555;
      font-weight: 600;
    }

    .empty-state p {
      color: #aaa;
      font-size: 14px;
    }

    .footer-frontend {
      background: #ffffff;
      border-top: 1px solid #eee;
      padding: 20px 0;
      text-align: center;
      color: #aaa;
      font-size: 13px;
      margin-top: 60px;
    }

    .pagination .page-link {
      color: #6259ca;
      border-radius: 8px;
      margin: 0 2px;
      border: none;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .pagination .page-item.active .page-link {
      background: #6259ca;
      border-color: #6259ca;
    }
  </style>

  @stack('styles')
</head>

<body>

  <nav class="navbar navbar-frontend navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand-text" href="{{ route('frontend.polls.index') }}">
        Poll<span>App</span>
      </a>
      <div class="ml-auto d-flex align-items-center" style="gap: 10px;">
        @auth
        <span class="text-muted" style="font-size: 14px;">
          Hi, {{ auth()->user()->name }}
        </span>
        @if(auth()->user()->is_admin)
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
          <i class="fa fa-cog"></i> Admin
        </a>
        @endif
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="fa fa-sign-out-alt"></i> Logout
          </button>
        </form>
        @else
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Login</a>
        <a href="{{ route('register') }}" class="btn btn-sm btn-primary"
          style="background:#6259ca; border-color:#6259ca;">Register</a>
        @endauth
      </div>
    </div>
  </nav>

  @yield('content')

  <div class="footer-frontend">
    <p class="mb-0">&copy; {{ date('Y') }} PollApp. All rights reserved.</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @if(session('success'))
  <script>
    $(document).ready(function() {
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        });
  </script>
  @endif

  @if(session('error'))
  <script>
    $(document).ready(function() {
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#FF2E2E'
            });
        });
  </script>
  @endif

  @stack('scripts')

</body>

</html>