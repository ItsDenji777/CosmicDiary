// Wire up the login and registration forms once the DOM is ready.
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            try {
                const response = await fetch('/CosmicDiary/api/auth/login.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        showToast('Welcome back, Captain!', 'success');
                        setTimeout(() => window.location.href = '/CosmicDiary/pages/' + data.redirect, 1000);
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (e) {
                    showToast('Server error: ' + text, 'error');
                }
            } catch (error) {
                showToast('Connection error. Try again.', 'error');
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            try {
                const response = await fetch('/CosmicDiary/api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        showToast('Account created! Redirecting...', 'success');
                        setTimeout(() => window.location.href = '/CosmicDiary/pages/' + data.redirect, 1000);
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (e) {
                    showToast('Server error: ' + text, 'error');
                }
            } catch (error) {
                showToast('Connection error. Try again.', 'error');
            }
        });
    }
});