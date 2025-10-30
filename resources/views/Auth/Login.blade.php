<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Laravel') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- SweetAlert2 for AdminLTE-style popups -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            outline: none;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 500;
            padding: 0.5rem;
            border-radius: 0.375rem;
            width: 100%;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .form-label {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 500;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="login-container">
        <h2 class="login-title">Sign In</h2>

        <form id="loginForm" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="form-label">Email Address</label>
                <div class="mt-1 relative">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </span>
                </div>
                <div class="error" id="email_error"></div>
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <div class="mt-1 relative">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-lock text-gray-400"></i>
                    </span>
                </div>
                <div class="error" id="password_error"></div>
            </div>

            <div class="flex justify-end">
                <a href="#" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
            </div>

            <button type="submit" id="signInButton" class="btn-primary flex items-center justify-center">
                <span id="buttonText">Sign In</span>
                <i class="fas fa-spinner fa-spin hidden ml-2" id="buttonSpinner"></i>
            </button>
        </form>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#loginForm').on('submit', function (e) {
                e.preventDefault();

                let email = $('#email').val().trim();
                let password = $('#password').val().trim();
                let isValid = true;
                $('.error').text('');

                if (!email) {
                    $('#email_error').text('Email is required.');
                    isValid = false;
                } else if (!/\S+@\S+\.\S+/.test(email)) {
                    $('#email_error').text('Please enter a valid email address.');
                    isValid = false;
                }

                if (!password) {
                    $('#password_error').text('Password is required.');
                    isValid = false;
                }

                if (!isValid) return;

                $('#signInButton').prop('disabled', true);
                $('#buttonText').text('Signing...');
                $('#buttonSpinner').removeClass('hidden');

                let formData = {
                    email: email,
                    password: password
                };

                $.ajax({
                    url: '{{ route("login.post") }}',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                confirmButtonColor: '#2563eb',
                                confirmButtonText: 'OK',
                                timer: 1500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                $('#loginForm')[0].reset();
                                $('.error').text('');
                                window.location.href = response.redirect;
                            });
                        } else {
                            let errorText = '';
                            $.each(response.errors, function (key, value) {
                                errorText += value[0] + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errorText,
                                confirmButtonColor: '#dc2626',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors || { general: ['An error occurred.'] };
                        let errorText = '';
                        $.each(errors, function (key, value) {
                            errorText += value[0] + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorText,
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function () {
                        $('#signInButton').prop('disabled', false);
                        $('#buttonText').text('Sign In');
                        $('#buttonSpinner').addClass('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>