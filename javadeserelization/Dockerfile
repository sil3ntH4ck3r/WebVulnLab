FROM openjdk:11-jdk-slim

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    wget \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY src/ ./src/
COPY pom.xml ./

# Instalar Maven
RUN apt-get update && apt-get install -y maven && rm -rf /var/lib/apt/lists/*

# Compilar la aplicación
RUN mvn clean package -DskipTests

# Crear directorio para datos
RUN mkdir -p /app/data

# Exponer puerto
EXPOSE 80

# Comando para ejecutar la aplicación
CMD ["java", "-jar", "target/repair-service-1.0-SNAPSHOT.jar"]