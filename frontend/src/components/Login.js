import React, { useState } from 'react';
import './Login.css';

function Login({ onLogin }) {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await fetch('http://localhost/CTR_Survey/backend/api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password }),
            });
            const result = await response.json();
            if (result.success) {
                onLogin(result.is_admin);  // ログイン成功時に管理者フラグを渡す
            } else {
                setError(result.message);
            }
        } catch (error) {
            setError('ログインに失敗しました');
        }
    };

    return (
        <div className="login-container">
            <h2>ログイン</h2>
            <form onSubmit={handleSubmit} className="login-form">
                <div className="form-group">
                    <label>ユーザー名:</label>
                    <input type="text" value={username} onChange={(e) => setUsername(e.target.value)} required className="login-input" />
                </div>
                <div className="form-group">
                    <label>パスワード:</label>
                    <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} required className="login-input" />
                </div>
                <button type="submit" className="login-button">ログイン</button>
            </form>
            {error && <p className="error-message">{error}</p>}
        </div>
    );
}

export default Login;
