<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lupa Kata Sandi - Portal Inovasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background-image: url('{{ asset("assets/dashboard-background/bg-200.png") }}');
      background-size: cover; background-position: center; background-repeat: no-repeat;
    }
    .auth-card{ max-width: 520px; }
  </style>
</head>
<body>
  <div class="container-sm vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-lg auth-card w-100">
      <div class="card-body p-4 p-md-5">

        <header class="text-center mb-4">
          <h3 class="text-danger fw-bold" style="font-size:1.6rem;">Lupa Kata Sandi</h3>
          <small class="text-muted">Masukkan email akun Anda.</small>
        </header>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

        <form method="POST" action="{{ route('password.email') }}">
          @csrf

          <div class="mb-3">
            <label for="identifier" class="form-label">Email </label>
            <input type="text" id="identifier" name="identifier" class="form-control" required autofocus
                   value="{{ old('identifier') }}" placeholder="masukkan email terdaftar">
          </div>

          <button type="submit" class="btn btn-danger w-100">Kirim Link Reset</button>

          <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-decoration-none">Kembali ke Login</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</body>
</html>
