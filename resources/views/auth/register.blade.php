<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Focus - Bootstrap Admin Dashboard</title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{asset('backend/images/favicon.png')}}">
  <link href="{{asset('backend/css/style.css')}}" rel="stylesheet">
</head>

<body class="h-100">
  <div class="authincation h-100">
    <div class="container-fluid h-100">
      <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-6">
          <div class="authincation-content">
            <div class="row no-gutters">
              <div class="col-xl-12">
                <div class="auth-form">
                  <h4 class="text-center mb-4">Sign up your account</h4>

                  {{-- Overall error message --}}
                  @if ($errors->any())
                  <div class="alert alert-danger">
                    <strong>Oops! Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                      @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                  @endif

                  {{-- Success message --}}
                  @if (session('success'))
                  <div class="alert alert-success">
                    {{ session('success') }}
                  </div>
                  @endif

                  <form action="{{route('register')}}" method="POST">
                    @csrf

                    <div class="form-group">
                      <label><strong>Username</strong></label>
                      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        placeholder="username" value="{{ old('name') }}">
                      @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label><strong>Email</strong></label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="hello@example.com" value="{{ old('email') }}">
                      @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label><strong>Password</strong></label>
                      <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" placeholder="Enter password">
                      @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label><strong>Confirm Password</strong></label>
                      <input type="password" name="password_confirmation"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        placeholder="Confirm password">
                      @error('password_confirmation')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="text-center mt-4">
                      <button type="submit" name="submit" class="btn btn-primary btn-block">Sign me up</button>
                    </div>
                  </form>

                  <div class="new-account mt-3">
                    <p>Already have an account? <a class="text-primary" href="{{ route('login') }}">Sign in</a></p>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{asset('backend/vendor/global/global.min.js')}}"></script>
  <script src="{{asset('backend/js/quixnav-init.js')}}"></script>
</body>

</html>