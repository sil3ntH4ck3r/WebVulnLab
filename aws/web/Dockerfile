FROM php:8.1-apache

# Instalar dependencias: awscli, zip, unzip
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Instalar AWS CLI
RUN curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip" \
    && unzip awscliv2.zip \
    && ./aws/install \
    && rm -rf awscliv2.zip ./aws

WORKDIR /var/www/html
COPY index.php ./
COPY invoke.php ./
COPY init.sh ./
COPY function_source/ ./function_source/

RUN chmod +x init.sh

EXPOSE 80

CMD ["/bin/bash", "-c", "./init.sh && apache2-foreground"]
