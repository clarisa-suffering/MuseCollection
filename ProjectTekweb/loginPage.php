<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .notification {
            display: none;
            background-color: #ffdddd;
            color: #d8000c;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d8000c;
            border-radius: 5px;
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
                alert('Login berhasil!');
                window.location.href = 'dashboard.php'; // Redirect ke halaman dashboard
            } else {
                // Reset fields
                usernameField.value = '';
                passwordField.value = '';

                // Tampilkan notifikasi error
                notification.innerText = result.message;
                notification.style.display = 'block';

                // Sembunyikan notifikasi setelah 3 detik
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>
