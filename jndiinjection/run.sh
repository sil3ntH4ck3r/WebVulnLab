#!/bin/bash
set -e

# Verificar que el JAR existe
JAR_FILE="target/jndi-injection-lab-1.0-SNAPSHOT.jar"
if [ ! -f "$JAR_FILE" ]; then
    echo "Error: No se encontró el archivo JAR: $JAR_FILE"
    exit 1
fi

# Crear directorio de logs si no existe
mkdir -p logs

# CONFIGURACIÓN VULNERABLE PARA LOG4J JNDI INJECTION (SOLO LABORATORIO)
export JAVA_OPTS="$JAVA_OPTS -Dcom.sun.jndi.ldap.object.trustURLCodebase=true"
export JAVA_OPTS="$JAVA_OPTS -Dcom.sun.jndi.rmi.object.trustURLCodebase=true"  
export JAVA_OPTS="$JAVA_OPTS -Dcom.sun.jndi.ldap.object.trustSerialData=true"
export JAVA_OPTS="$JAVA_OPTS -Dcom.sun.jndi.rmi.object.trustSerialData=true"

# CRÍTICO: Deshabilitar las protecciones de Log4j2
export JAVA_OPTS="$JAVA_OPTS -Dlog4j2.formatMsgNoLookups=false"
export JAVA_OPTS="$JAVA_OPTS -Dlog4j.configurationFile=log4j2.xml"

# Configuración de JVM para Java 8 (vulnerabilidades necesarias)
export JAVA_OPTS="$JAVA_OPTS -Djdk.serialFilter="
export JAVA_OPTS="$JAVA_OPTS -Djava.rmi.server.useCodebaseOnly=false"

# Configuración adicional de JVM
export JAVA_OPTS="$JAVA_OPTS -Djava.security.egd=file:/dev/./urandom"
export JAVA_OPTS="$JAVA_OPTS -Djava.awt.headless=true"
export JAVA_OPTS="$JAVA_OPTS -Dserver.port=80"

# Configuración de Tomcat para caracteres especiales
export JAVA_OPTS="$JAVA_OPTS -Dtomcat.util.http.parser.HttpParser.requestTargetAllow=|{}"
export JAVA_OPTS="$JAVA_OPTS -Dtomcat.util.http.parser.HttpParser.queryStringAllow=|{}[]^'\`\"<>"

# Configuración de red para debugging
export JAVA_OPTS="$JAVA_OPTS -Djava.net.preferIPv4Stack=true"
export JAVA_OPTS="$JAVA_OPTS -Dsun.net.useExclusiveBind=false"

# Debugging JNDI (opcional - genera muchos logs)
# export JAVA_OPTS="$JAVA_OPTS -Dcom.sun.jndi.ldap.trace.ber.level=2"

# Función para manejo de señales
cleanup() {
    echo "Cerrando laboratorio de forma segura..."
    if [ ! -z "$APP_PID" ]; then
        kill $APP_PID 2>/dev/null || true
        wait $APP_PID 2>/dev/null || true
    fi
    echo "Laboratorio cerrado correctamente"
    exit 0
}

# Configurar trap para manejo de señales
trap cleanup SIGINT SIGTERM EXIT

# Iniciar la aplicación Spring Boot
echo "Iniciando aplicación Spring Boot..."
java $JAVA_OPTS -jar "$JAR_FILE" &
APP_PID=$!

for i in {1..30}; do
    if kill -0 $APP_PID 2>/dev/null; then
        # Verificar si el puerto está disponible
        if curl -s -f http://localhost:80/ > /dev/null 2>&1; then
            echo "Aplicación iniciada exitosamente"
            break
        fi
    else
        echo "Error: La aplicación se detuvo inesperadamente"
        exit 1
    fi
    
    echo "Intento $i/30 - Esperando respuesta..."
    sleep 2
done

# Verificación final
if ! kill -0 $APP_PID 2>/dev/null; then
    echo "Error: La aplicación no se pudo iniciar correctamente"
    exit 1
fi

# Mantener el script corriendo y mostrar logs básicos
wait $APP_PID