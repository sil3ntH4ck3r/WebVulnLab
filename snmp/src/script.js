// Función para obtener datos del servidor
async function fetchSystemInfo() {
    try {
        const response = await fetch('api.php');
        const data = await response.json();

        // Actualizar la información en la interfaz
        document.getElementById('system-description').innerText = data.description;
        document.getElementById('system-uptime').innerText = formatUptime(data.uptime);
        document.getElementById('cpu-load').innerText = `${data.cpuLoad}%`;

        // Calcular y actualizar el uso de RAM
        const ramUsage = calculateRamUsage(data.totalRAM, data.freeRAM);
        document.getElementById('ram-percentage').innerText = `${ramUsage}%`;
        drawRamUsage(ramUsage);

        // Actualizar las interfaces de red
        const interfaceList = document.getElementById('interface-list');
        interfaceList.innerHTML = ''; // Limpiar contenido anterior
        data.interfaces.forEach(interface => {
            const div = document.createElement('div');
            div.className = 'interface-item';
            div.innerHTML = `<h4>${interface}</h4>`;
            interfaceList.appendChild(div);
        });
    } catch (error) {
        console.error('Error fetching system info:', error);
    }
}

// Función para calcular el uso de RAM
function calculateRamUsage(totalRAM, freeRAM) {
    const total = parseInt(totalRAM);
    const free = parseInt(freeRAM);
    if (isNaN(total) || isNaN(free)) return 0;
    return Math.round(((total - free) / total) * 100);
}

// Función para dibujar el uso de RAM en el canvas
function drawRamUsage(percentage) {
    const canvas = document.getElementById('ram-usage-canvas');
    const ctx = canvas.getContext('2d');
    const startAngle = -0.5 * Math.PI;
    const endAngle = ((percentage / 100) * 2 * Math.PI) + startAngle;

    // Limpiar canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Círculo de fondo
    ctx.beginPath();
    ctx.arc(100, 100, 90, 0, 2 * Math.PI);
    ctx.fillStyle = '#262626';
    ctx.fill();

    // Arco de uso
    ctx.beginPath();
    ctx.arc(100, 100, 90, startAngle, endAngle);
    const gradient = ctx.createLinearGradient(0, 0, 200, 0);
    gradient.addColorStop(0, '#00d4ff');
    gradient.addColorStop(1, '#ff00c8');
    ctx.strokeStyle = gradient;
    ctx.lineWidth = 20;
    ctx.stroke();
}

// Función para formatear el tiempo de actividad
function formatUptime(uptime) {
    // Supongamos que uptime viene en centésimas de segundo
    let totalSeconds = parseInt(uptime) / 100;
    const days = Math.floor(totalSeconds / 86400);
    totalSeconds %= 86400;
    const hours = Math.floor(totalSeconds / 3600);
    totalSeconds %= 3600;
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = Math.floor(totalSeconds % 60);

    return `${days}d ${hours}h ${minutes}m ${seconds}s`;
}

// Llamar a la función al cargar la página
window.onload = fetchSystemInfo;
