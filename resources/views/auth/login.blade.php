
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inventory Management System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: #f3f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .login-logo {
            width: 65px;
            height: 65px;
            background: #e8efff;
            color: #2563eb;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 15px;
        }

        .login-title {
            color: #172033;
            font-weight: 700;
        }

        .form-control {
            padding: 12px;
            border-radius: 8px;
        }

        .btn-login {
            background: #2563eb;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #1d4ed8;
            color: white;
        }

        .login-footer {
            color: #7b8495;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="login-card">

        <div class="text-center mb-4">

            <div class="login-logo">
                &#128451;
            </div>

            <h4 class="login-title">
                Inventory Management System
            </h4>

            <p class="text-muted mb-0">
                Sign in to access your account
            </p>

        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.store') }}" method="POST">

            @csrf

            <div class="mb-3">

                <label for="username" class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    id="username"
                    class="form-control"
                    placeholder="Enter your username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                >

            </div>

            <div class="mb-3">

                <label for="password" class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <div class="mb-3 form-check">

                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    class="form-check-input"
                >

                <label for="remember" class="form-check-label">
                    Remember me
                </label>

            </div>

            <button type="submit" class="btn btn-login w-100">
                Sign In
            </button>

        </form>

        <div class="text-center mt-4 login-footer">
            Inventory Management System
            <br>
            Authorized Personnel Only
        </div>

    </div>

</div>

</body>
</html>
