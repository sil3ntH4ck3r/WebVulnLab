<?php
    // Mostrar errores para depuración
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Configurar el modo de recuperación de valores
    snmp_set_valueretrieval(SNMP_VALUE_PLAIN);

    $host = '127.0.0.1'; // O la IP del contenedor
    $community = 'public';

    // Descripción del Sistema
    $description = snmpget($host, $community, 'SNMPv2-MIB::sysDescr.0');
    $description = str_replace('"', '', $description);

    // Tiempo de Actividad del Sistema
    $uptime = snmpget($host, $community, 'DISMAN-EVENT-MIB::sysUpTimeInstance');
    $uptime = str_replace('"', '', $uptime);

    // Memoria Total y Libre
    $totalRAM = snmpget($host, $community, 'UCD-SNMP-MIB::memTotalReal.0');
    $freeRAM = snmpget($host, $community, 'UCD-SNMP-MIB::memAvailReal.0');

    // Carga de CPU
    $cpuLoad = snmpget($host, $community, 'UCD-SNMP-MIB::laLoad.1');
    $cpuLoad = str_replace('"', '', $cpuLoad);

    // Interfaces de Red
    $interfaces = snmpwalk($host, $community, 'IF-MIB::ifDescr');

    // Limpiar comillas en interfaces
    $interfaces = array_map(function($iface) {
        return str_replace('"', '', $iface);
    }, $interfaces);

    // Preparar respuesta JSON
    $response = [
        'description' => $description,
        'uptime' => $uptime,
        'totalRAM' => $totalRAM,
        'freeRAM' => $freeRAM,
        'cpuLoad' => $cpuLoad,
        'interfaces' => $interfaces
    ];

    // Enviar cabeceras y respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
?>
