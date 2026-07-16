FROM node:18-alpine
WORKDIR /var/www/html
COPY src/package.json ./
RUN npm install && npm cache clean --force
COPY src/server.js .
COPY src/public ./public
EXPOSE 80
CMD sh -c "test -d node_modules/express || npm install && node server.js"
