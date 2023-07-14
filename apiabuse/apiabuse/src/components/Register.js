import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import './Register.css';

function Register() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [responseStatus, setResponseStatus] = useState(null);
  const [passwordError, setPasswordError] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  const navigate = useNavigate(); // Utiliza el hook useNavigate para la navegación

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      navigate('/dashboard'); // Redirige al usuario a la página de inicio
    }
  }, [navigate]);

  async function register() {
    try {
      if (password !== confirmPassword) {
        setPasswordError('Las contraseñas no coinciden. Por favor, inténtalo de nuevo.');
        return;
      }

      const response = await fetch('http://localhost:3001/api/v2/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });

      setResponseStatus(response.status);

      const data = await response.json();
      // Procesa la respuesta de registro aquí

      if (response.status >= 200 && response.status < 400) {
        setSuccessMessage('Registro exitoso');
      } else if (response.status >= 400 && response.status < 500) {
        setErrorMessage('Error en el registro');
      } else if (response.status >= 500) {
        setErrorMessage('Error en el servidor');
      }
    } catch (error) {
      console.error('Error al registrarse:', error);
    }
  }

  function handleEmailChange(event) {
    setEmail(event.target.value);
  }

  function handlePasswordChange(event) {
    setPassword(event.target.value);
  }

  function handleConfirmPasswordChange(event) {
    setConfirmPassword(event.target.value);
  }

  function handleOkClick() {
    setPasswordError('');
    setSuccessMessage('');
    setErrorMessage('');
  }

  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
  
  function handleSubmit(event) {
    event.preventDefault();
  
    if (!isValidEmail(email)) {
      setErrorMessage('Por favor, introduce un correo electrónico válido.');
      return;
    }
  
    register();
  }

  return (
    <div className="register-container">
      <h1>Registro de usuario</h1>
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label htmlFor="email">Email:</label>
          <input
            type="text"
            id="email"
            value={email}
            onChange={handleEmailChange}
          />
        </div>
        <div className="form-group">
          <label htmlFor="password">Contraseña:</label>
          <input
            type="password"
            id="password"
            value={password}
            onChange={handlePasswordChange}
          />
        </div>
        <div className="form-group">
          <label htmlFor="confirmPassword">Confirmar Contraseña:</label>
          <input
            type="password"
            id="confirmPassword"
            value={confirmPassword}
            onChange={handleConfirmPasswordChange}
          />
        </div>
        {passwordError && (
          <div className="error-dialog">
            <p>{passwordError}</p>
            <button onClick={handleOkClick}>OK</button>
          </div>
        )}
        {successMessage && (
          <div className="success-dialog">
            <p>{successMessage}</p>
            <button onClick={handleOkClick}>OK</button>
          </div>
        )}
        {errorMessage && (
          <div className="error-dialog">
            <p>{errorMessage}</p>
            <button onClick={handleOkClick}>OK</button>
          </div>
        )}
        <button type="submit">Registrarse</button>
      </form>
    </div>
  );
}

export default Register;