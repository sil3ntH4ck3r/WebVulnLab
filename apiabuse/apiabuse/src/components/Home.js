import React from 'react';
import './Home.css';
import Alert from './Alert';

function Home() {
  return (
    <div>
      <Alert message="¡Atención! Es importante verificar que el contenedor api_server_v2 esté encendido en tablero.local para que el laboratorio funcione correctamente. Una forma de comprobarlo es al momento de registrarse, verificar que aparezca una alerta en pantalla que indique si el registro ha sido exitoso o no." />
      <div className="home-container">
        <h1>Bienvenido a nuestra tienda online</h1>
        <p>
          Para celebrar el lanzamiento de nuestra tienda, estamos regalando un cupón de 100 a todos los usuarios que se registren. Este cupón solo se puede gastar en nuestra tienda.
        </p>
      </div>
    </div>
  );
}

export default Home;