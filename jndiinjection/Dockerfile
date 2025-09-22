# Usar una imagen base más robusta
FROM openjdk:8-jdk-slim

# Instalar dependencias y crear usuario no-root
RUN apt-get update && \
    apt-get install -y curl wget maven && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    useradd -m -u 1000 appuser

# Configurar límites del sistema
RUN echo "* soft nofile 65536" >> /etc/security/limits.conf && \
    echo "* hard nofile 65536" >> /etc/security/limits.conf && \
    echo "* soft nproc 32768" >> /etc/security/limits.conf && \
    echo "* hard nproc 32768" >> /etc/security/limits.conf

# Crear directorio de trabajo
WORKDIR /app

# Cambiar propietario del directorio
RUN chown -R appuser:appuser /app

# Cambiar al usuario no-root
USER appuser

# Copiar archivos de la aplicación
COPY --chown=appuser:appuser . /app

# Compilar la aplicación
RUN mvn clean package -DskipTests

# Hacer ejecutable el script
RUN chmod +x run.sh

# Exponer puertos
EXPOSE 80

# Variables de entorno para optimizar JVM
ENV JAVA_OPTS="-Xms256m -Xmx1g -XX:+UseG1GC -XX:MaxGCPauseMillis=200"

# Comando por defecto
ENTRYPOINT ["./run.sh"]