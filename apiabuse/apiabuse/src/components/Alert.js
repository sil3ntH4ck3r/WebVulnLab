import React from 'react';
import './Alert.css';

const Alert = ({ message }) => {
  return (
    <Alert message="¡Atención! Es importante verificar que el contenedor api_server_v2 esté encendido en tablero.local para que el laboratorio funcione correctamente. Una forma de comprobarlo es al momento de registrarse, verificar que aparezca una alerta en pantalla que indique si el registro ha sido exitoso o no." />,
    <div className="alert-container">
      <p>{message}</p>
    </div>
  );
};

export default Alert;