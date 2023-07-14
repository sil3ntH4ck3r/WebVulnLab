import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import './ChangePassword.css';

function StepOne({ onNext }) {
  const [email, setEmail] = useState('');
  const [emailError, setEmailError] = useState('');
  const [showAlert, setShowAlert] = useState(false);

  function handleSubmit(event) {
    event.preventDefault();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      setEmailError('Por favor, introduce un correo electrónico válido.');
      setShowAlert(true);
      return;
    }
    onNext(email);
  }

  function handleOkClick() {
    setShowAlert(false);
  }

  return (
    <form onSubmit={handleSubmit}>
      {showAlert && (
        <div className="alert-container">
            <div className="alert-content">
                <p>{emailError}</p>
                <button onClick={handleOkClick}>OK</button>
            </div>
        </div>


      )}
      <div className="form-group">
        <label htmlFor="email">Correo electrónico:</label>
        <input
          id="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
      </div>
      <button type="submit">Continuar</button>
    </form>
  );
}

function StepTwo({ onSubmit, email }) {
  const [pin, setPin] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmNewPassword, setConfirmNewPassword] = useState('');

  function handleSubmit(event) {
    event.preventDefault();
    onSubmit(pin, newPassword, confirmNewPassword, email);
  }

  return (
    <form onSubmit={handleSubmit}>
      <div className="form-group">
        <label htmlFor="pin">Pin de verificación:</label>
        <input
          type="text"
          id="pin"
          value={pin}
          onChange={(e) => setPin(e.target.value)}
        />
      </div>
      <div className="form-group">
        <label htmlFor="newPassword">Nueva contraseña:</label>
        <input
          type="password"
          id="newPassword"
          value={newPassword}
          onChange={(e) => setNewPassword(e.target.value)}
        />
      </div>
      <div className="form-group">
        <label htmlFor="confirmNewPassword">Confirmar nueva contraseña:</label>
        <input
          type="password"
          id="confirmNewPassword"
          value={confirmNewPassword}
          onChange={(e) => setConfirmNewPassword(e.target.value)}
        />
      </div>
      <button type="submit">Cambiar contraseña</button>
    </form>
  );
}

function ChangePassword() {
    const [step, setStep] = useState(1);
    const [email, setEmail] = useState('');
    const [statusMessage, setStatusMessage] = useState(null);
    const navigate = useNavigate(); // Utiliza el hook useNavigate para la navegación
  
    async function sendVerificationEmail(email) {
      try {
        setEmail(email);
        const response = await fetch('http://localhost:3001/api/v2/send-verification-email', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email }),
        });
        if (response.ok) {
          setStep(2);
        } else {
          const errorData = await response.json();
          setStatusMessage({ type: 'error', message: errorData.error });
        }
      } catch (error) {
        console.error('Error al enviar el correo electrónico de verificación:', error);
      }
    }
  
    async function changePassword(pin, newPassword, confirmNewPassword, email) {
      try {
        if (newPassword !== confirmNewPassword) {
          setStatusMessage({
            type: 'error',
            message: 'Las contraseñas no coinciden. Por favor, inténtalo de nuevo.',
          });
          return;
        }
  
        const response = await fetch('http://localhost:3001/api/v2/change-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ pin, newPassword, email }),
        });
  
        if (response.ok) {
          setStatusMessage({
            type: 'success',
            message: 'Contraseña cambiada exitosamente.',
          });
          navigate('/login'); // Redirige al usuario a la página de inicio de sesión
        } else {
          const errorData = await response.json();
          setStatusMessage({ type: 'error', message: errorData.error });
        }
      } catch (error) {
        console.error('Error al cambiar la contraseña:', error);
      }
    }
  
    function handleOkClick() {
      setStatusMessage(null);
    }
  
    return (
      <div>
        {statusMessage && (
        <div className="alert-container">
            <div className="alert-content">
            <p>{statusMessage.message}</p>
            <button onClick={handleOkClick}>OK</button>
            </div>
        </div>
        )}
        <div className="change-password-container">
          <div className="change-password-card">
            <h2>Cambiar contraseña</h2>
            {step === 1 && <StepOne onNext={sendVerificationEmail} />}
            {step === 2 && <StepTwo onSubmit={changePassword} email={email} />}
          </div>
        </div>
      </div>
    );
  }

export default ChangePassword;