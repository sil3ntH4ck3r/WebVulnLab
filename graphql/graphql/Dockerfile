FROM node:latest

WORKDIR /var/www/html

# Instala las dependencias de tu servidor GraphQL y servidor web Express.js
COPY package*.json ./
RUN npm install

RUN npm install mysql2 cookie-parser

RUN apt update
RUN apt install default-mysql-server -y


# Copia el código de tu servidor
COPY index.js .

# Copia la carpeta "public"
COPY public/ ./public

# Expone el puerto
EXPOSE 3000

# Iniciar servidor
CMD ["npm", "start"]