<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Atur Ulang Kata Sandi - Portal Inovasi</title>
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
          <h3 class="text-danger fw-bold" style="font-size:1.6rem;">Atur Ulang Kata Sandi</h3>
          <small class="text-muted">Buat kata sandi baru untuk akun Anda.</small>
        </header>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <form method="POST" action="{{ route('password.update') }}">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" required
                   value="{{ old('email', $email ?? '') }}"
                   class="form-control" placeholder="email@sig.id">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi Baru</label>
            <input id="password" name="password" type="password" required class="form-control" placeholder="Minimal 8 karakter">
          </div>

          <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="form-control">
          </div>

          <button type="submit" class="btn btn-danger w-100">Simpan Kata Sandi</button>

          <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-decoration-none">Kembali ke Login</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</body>
</html>
