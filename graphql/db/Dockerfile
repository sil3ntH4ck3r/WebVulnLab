FROM mysql:8.0

ENV MYSQL_DATABASE=mydatabase
ENV MYSQL_ROOT_PASSWORD=mysecretpassword

COPY schema.sql /docker-entrypoint-initdb.d/