FROM node:14
WORKDIR /var/www/html
COPY src/package*.json /var/www/html/
RUN npm install
COPY src/ /var/www/html/
EXPOSE 80
CMD [ "sh", "-c", "cd /var/www/html && node app.js" ]