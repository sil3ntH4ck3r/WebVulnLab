FROM python:3.9-slim

WORKDIR /app

# Instalar dependencias del sistema para decodificar códigos QR
RUN apt-get update && apt-get install -y libzbar0 && rm -rf /var/lib/apt/lists/*

# Copiar archivo de requerimientos e instalar dependencias de Python
COPY src/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copiar el código fuente
COPY src/ .

EXPOSE 5000

CMD ["python", "app.py"]
