import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import './Shop.css';

function Shop() {
  const [products, setProducts] = useState([]);
  const [alertMessage, setAlertMessage] = useState('');

  const navigate = useNavigate(); // Utilize the useNavigate hook for navigation

  useEffect(() => {
    async function fetchProducts() {
      try {
        const response = await fetch('http://localhost:3001/api/v1/products');
        const data = await response.json();
        setProducts(data);
      } catch (error) {
        console.error('Error al obtener los productos:', error);
      }
    }

    fetchProducts();
  }, []);

  const handleBuy = async (productId, price, quantity) => {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch('http://localhost:3001/api/v1/purchase', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
          'Cache-Control': 'no-cache',
        },
        body: JSON.stringify({ productId, price, quantity }),
      });
      const data = await response.json();
      if (response.ok) {
        setAlertMessage(data.message);
      } else {
        setAlertMessage(data.error);
      }
    } catch (error) {
      console.error('Error al realizar la compra:', error);
    }
  };

  return (
    <div className="shop-container">
      <h2>Tienda</h2>
      {alertMessage && (
        <div className={`alert ${alertMessage.includes('saldo insuficiente') ? 'alert-danger' : 'alert-warning'}`}>
          {alertMessage}
          <button onClick={() => setAlertMessage('')}>OK</button>
        </div>
      )}
      <div className="product-container">
        {products.map((product) => (
          <div key={product.id} className="product-card">
            <h3>{product.name}</h3>
            <p>{product.description}</p>
            <p>Precio: {product.price} €</p>
            <label htmlFor={`quantity-${product.id}`}>Cantidad:</label>
            <input type="number" id={`quantity-${product.id}`} defaultValue="1" min="1" max="10" />
            <button onClick={() => handleBuy(product.id, product.price, parseInt(document.getElementById(`quantity-${product.id}`).value))}>Comprar</button>
          </div>
        ))}
      </div>
    </div>
  );
}

export default Shop;