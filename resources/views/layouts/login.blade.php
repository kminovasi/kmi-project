<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    @laravelPWA
    <style>
        body {
            background-image: url('{{ asset("assets/dashboard-background/bg-200.png") }}'); /* Ganti dengan path gambar Anda */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .bg-custom {
            background-image: url('{{ asset('assets/login-frame.png') }}');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body>
    <div class="container-sm vh-100 d-flex align-items-center justify-content-center">

        <div class="row shadow-lg rounded mx-auto w-100" style="max-height: 80vh; min-height: 600px; overflow: auto;">
            <div class="col-lg-6 bg-custom">
                <!-- background full -->
            </div>
            <div class="col-lg-6 py-5 px-4 bg-white d-flex flex-column" >
                <header class="text-center mb-4">
                    <h3 class="text-danger fw-bold" style="font-size: 1.8rem;">Welcome to Portal Innovasi</h3>
                    <small class="text-muted" style="font-size: 1.1rem;">Silahkan login dulu</small>
                </header>
                @if(Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif
                <div class="flex-grow-1 d-flex align-items-center">
                    <form action="{{ route('postLogin') }}" method="POST" class="w-100">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="username" required
                                aria-describedby="emailHelp" placeholder="Email" />
                        </div>
                        <div class="mb-5">
                            <label for="exampleInputPassword1" class="form-label">Password</label>
                            <input type="password" class="form-control" id="exampleInputPassword1" name="password"
                            placeholder="Password" required />
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn w-100" style="background-color: red; color: white;">Login</button>
                        </div>
                    </form>
                </div>
                <div class="text-center mt-4">
                    <hr>
                    <small>&copy; Copyright 2025 All rights reserved. PT. Semen Indonesia (Persero) Tbk.</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
