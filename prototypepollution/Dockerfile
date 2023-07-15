FROM node:14

WORKDIR /var/www/html

COPY src/package.json /var/www/html

COPY src/package-lock.json /var/www/html

RUN npm install

COPY src/ /var/www/html

EXPOSE 3000

CMD ["npm", "start"]