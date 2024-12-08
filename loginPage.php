<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
     body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .login-container {
            background-color: #fff;
            padding: 40px; 
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px; 
        }

        .login-container h2 {
            margin-bottom: 30px; 
            text-align: center;
            font-weight: bold;
            color: #333;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px; 
        }

        .form-group label {
            font-weight: bold;
            color: #555;
            font-size: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 12px; 
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 18px; 
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .notification {
            display: none;
            background-color: #ffdddd;
            color: #d8000c;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d8000c;
            border-radius: 5px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #aaa;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login Karyawan</h2>
        <form id="login-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <div id="notification" class="notification"></div>
        </form>
        <div class="footer">&copy; 2024 Muse Collection</div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');
            const notification = document.getElementById('notification');

            const username = usernameField.value;
            const password = passwordField.value;

            const response = await fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }),
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: 'Login berhasil.',
                }).then(() => {
                    // Redirect after the modal is closed
                    window.location.href = 'dashboard.php'; // Redirect to dashboard
                });
            } else {
                // Reset fields
                usernameField.value = '';
                passwordField.value = '';

                Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Username atau password salah.',
                });
            }
        });
    </script>
</body>
</html>