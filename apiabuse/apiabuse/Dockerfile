# Utiliza una imagen base de Node.js
FROM node:14

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia los archivos del proyecto a la imagen del contenedor
COPY package.json package-lock.json /app/
COPY . /app/

# Instala las dependencias del proyecto
RUN npm install

RUN npm install jwt-decode

# Construye la aplicación React
RUN npm run build

# Expone el puerto en el que se ejecuta la aplicación web
EXPOSE 3000

# Comando para iniciar la aplicación web
CMD ["npm", "start"]