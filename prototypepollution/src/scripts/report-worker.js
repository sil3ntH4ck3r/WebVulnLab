#!/usr/bin/env node
// Worker temático: genera un "informe de seguridad" ficticio.
// Si el proceso hijo recibe execArgv contaminado con --eval, ese código se ejecutará
// antes de entrar aquí (demostrando RCE vía prototype pollution sobre options.execArgv).

const os = require('os');
const fs = require('fs');
const path = require('path');

function generateReport() {
  const report = {
    title: 'Informe de Seguridad',
    timestamp: new Date().toISOString(),
    hostname: os.hostname(),
    platform: process.platform,
    node: process.version,
    pid: process.pid,
    findings: [
      { id: 'PP-001', severity: 'high', description: 'Posible contaminación de prototipo detectada en punto de entrada JSON.' },
      { id: 'PP-002', severity: 'medium', description: 'Mezcla profunda sin validación de claves peligrosas.' }
    ]
  };
  // Simula trabajo de análisis
  console.log('[report-worker] Generando informe...');
  console.log('[report-worker] Metadatos:', JSON.stringify(report));
  return report;
}

(async () => {
  try {
    const report = generateReport();
    // Persistir informe en carpeta public para consulta web
    const outPath = path.join(__dirname, '..', 'public', 'report.json');
    await fs.promises.writeFile(outPath, JSON.stringify(report, null, 2), 'utf8');
    console.log('[report-worker] Informe guardado en', outPath);
    await new Promise(r => setTimeout(r, 100));
    console.log('[report-worker] Informe generado correctamente');
    process.exit(0);
  } catch (e) {
    console.error('[report-worker] Error:', e.message);
    process.exit(1);
  }
})();
