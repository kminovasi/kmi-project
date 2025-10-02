<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Auth - KMI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  @laravelPWA
  <style>
    body{
      background-image: url('{{ asset("assets/dashboard-background/bg-200.png") }}');
      background-size: cover; background-position: center; background-repeat: no-repeat;
    }
    .bg-custom{
      background-image: url('{{ asset('assets/login-frame.jpg') }}');
      background-size: cover; background-position: center;
      min-height: 600px;
    }
    .auth-card{ max-height: 80vh; min-height: 600px; overflow:auto; }
    .form-help{ font-size:.9rem; color:#6c757d; }
    .link-toggle{ cursor:pointer; }
  </style>
</head>
<body>
  <div class="container-sm vh-100 d-flex align-items-center justify-content-center">
    <div class="row shadow-lg rounded mx-auto w-100 auth-card">
      <div class="col-lg-6 bg-custom d-none d-lg-block"><!-- visual --></div>

      <div class="col-lg-6 py-5 px-4 bg-white d-flex flex-column">
        <header class="text-center mb-4">
          <h3 class="text-danger fw-bold" style="font-size:1.8rem;">Welcome to Portal Innovasi</h3>
          <small class="text-muted" id="auth-subtitle" style="font-size:1.05rem;">Silakan login dulu</small>
        </header>

        {{-- Flash message --}}
        @if(Session::has('error'))
          <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        @if(Session::has('success'))
          <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif


        <div class="tab-content flex-grow-1 d-flex">
          {{-- ================= LOGIN ================= --}}
          <div class="tab-pane fade show active w-100 my-auto" id="pane-login" role="tabpanel" aria-labelledby="tab-login">
            <form action="{{ route('postLogin') }}" method="POST" class="w-100">
              @csrf
              <div class="mb-3">
                <label for="login_email" class="form-label">Email</label>
                <input type="email" class="form-control @error('username') is-invalid @enderror" id="login_email" name="username" value="{{ old('username') }}" required placeholder="Masukkan email terdaftar">
                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-2">
                <label for="login_password" class="form-label">Password</label>
                <div class="input-group">
                  <input type="password" class="form-control @error('password') is-invalid @enderror" id="login_password" name="password" required placeholder="Password">
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('login_password', this)">Show</button>
                </div>
                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>

              <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                  <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="small">Lupa password?</a>
              </div>

              <button type="submit" class="btn w-100" style="background-color:red; color:#fff;">Login</button>

              {{-- <p class="text-center mt-3 form-help">
                Belum memiliki akun?
                <span class="text-primary link-toggle" data-target="register">Register</span>
              </p> --}}
            </form>
          </div>

          {{-- ================= REGISTER ================= --}}
          <div class="tab-pane fade w-100 my-auto" id="pane-register" role="tabpanel" aria-labelledby="tab-register">
            <form action="{{ route('postRegister') }}" method="POST" class="w-100">
              @csrf
              <div class="row g-3">

                <div class="col-12">
                  <label for="reg_email" class="form-label">Email</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="reg_email" name="email" value="{{ old('email') }}" required placeholder="Masukkan email terdaftar">
                  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Opsional: unit/organisasi --}}
                {{-- <div class="col-12">
                  <label for="reg_org" class="form-label">Unit / Organisasi</label>
                  <input type="text" class="form-control" id="reg_org" name="organization" value="{{ old('organization') }}" placeholder="Contoh: Direktorat XYZ">
                </div> --}}

                <div class="col-12">
                  <label for="reg_password" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="reg_password" name="password" required placeholder="Minimal 8 karakter">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('reg_password', this)">Show</button>
                  </div>
                  @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                  <div class="form-help">Gunakan kombinasi huruf besar, kecil, angka, dan simbol.</div>
                </div>

                <div class="col-12">
                  <label for="reg_password_confirmation" class="form-label">Konfirmasi Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="reg_password_confirmation" name="password_confirmation" required placeholder="Ulangi password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('reg_password_confirmation', this)">Show</button>
                  </div>
                </div>

                <div class="col-12">
                  <button type="submit" class="btn w-100" style="background-color:red; color:#fff;">Buat Akun</button>
                </div>
              </div>

              <p class="text-center mt-3 form-help">
                Sudah punya akun?
                <span class="text-primary link-toggle" data-target="login">Login</span>
              </p>
            </form>
          </div>
        </div>

        <div class="text-center mt-4">
          <hr>
          <small>&copy; Copyright 2025 All rights reserved PT Semen Indonesia (Persero) Tbk</small>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePwd(id, btn){
      const input = document.getElementById(id);
      if(!input) return;
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      btn.textContent = isPwd ? 'Hide' : 'Show';
    }

    function switchPane(mode){
      const loginPane = document.getElementById('pane-login');
      const regPane   = document.getElementById('pane-register');
      const sub       = document.getElementById('auth-subtitle');
    
      if(mode === 'register'){
        loginPane.classList.remove('show','active');
        regPane.classList.add('show','active');
        if (sub) sub.textContent = 'Silakan buat akun baru';
      } else {
        regPane.classList.remove('show','active');
        loginPane.classList.add('show','active');
        if (sub) sub.textContent = 'Silakan login dulu';
      }
    }

    document.querySelectorAll('.link-toggle').forEach(el=>{
      el.addEventListener('click', () => switchPane(el.dataset.target));
      el.addEventListener('keydown', (e) => {
        if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); switchPane(el.dataset.target); }
      });
    });

    function syncSubtitle(){
      const sub = document.getElementById('auth-subtitle');
      const isLoginActive = document.getElementById('tab-login').classList.contains('active');
      sub.textContent = isLoginActive ? 'Silakan login dulu' : 'Silakan buat akun baru';
    }

    (function(){
      const params = new URLSearchParams(window.location.search);
      if(params.get('mode') === 'register'){
        new bootstrap.Tab(document.getElementById('tab-register')).show();
      }
      syncSubtitle();
      document.getElementById('authTabs').addEventListener('shown.bs.tab', syncSubtitle);
    })();

    (function(){
  const params = new URLSearchParams(window.location.search);
  switchPane(params.get('mode') === 'register' ? 'register' : 'login');
})();

  </script>
</body>
</html>
