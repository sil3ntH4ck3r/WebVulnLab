<?php
ob_start();
include __DIR__ . '/functions.php';
include __DIR__ . '/header.php';
$tickets = getTickets();
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$priorityFilter = isset($_GET['priority']) ? $_GET['priority'] : '';
$assignmentFilter = isset($_GET['assignment']) ? $_GET['assignment'] : '';
if($statusFilter || $priorityFilter || $assignmentFilter) {
    $tickets = array_filter($tickets, function($ticket) use ($statusFilter, $priorityFilter, $assignmentFilter) {
        $statusMatch = $statusFilter ? strtolower($ticket['status']) === strtolower($statusFilter) : true;
        $priorityMatch = $priorityFilter ? strtolower($ticket['priority']) === strtolower($priorityFilter) : true;
        $assignmentMatch = $assignmentFilter ? strtolower($ticket['assignment']) === strtolower($assignmentFilter) : true;
        return $statusMatch && $priorityMatch && $assignmentMatch;
    });
}
?>

<div class="container animate-in">
    <div class="page-header">
        <h1 class="page-title">Tickets</h1>
        <div class="header-actions">
            <a href="create_ticket.php" class="btn btn-primary">
                Nuevo Ticket
            </a>
        </div>
    </div>

    <form method="GET" class="ticket-filters mb-8">
        <div class="filter-group">
            <label class="filter-label">Estado</label>
            <select name="status" class="filter-select">
                <option value="">Todos</option>
                <option value="Open" <?php if($statusFilter=='Open') echo 'selected'; ?>>Abierto</option>
                <option value="Closed" <?php if($statusFilter=='Closed') echo 'selected'; ?>>Cerrado</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="filter-label">Prioridad</label>
            <select name="priority" class="filter-select">
                <option value="">Todas</option>
                <option value="Low" <?php if($priorityFilter=='Low') echo 'selected'; ?>>Baja</option>
                <option value="Medium" <?php if($priorityFilter=='Medium') echo 'selected'; ?>>Media</option>
                <option value="High" <?php if($priorityFilter=='High') echo 'selected'; ?>>Alta</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="filter-label">Asignado a</label>
            <input type="text" name="assignment" value="<?php echo htmlspecialchars($assignmentFilter); ?>" placeholder="Nombre">
        </div>
        
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php" class="btn btn-outline">Limpiar</a>
        </div>
    </form>

    <div class="ticket-list">
        <?php if(empty($tickets)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    No hay contenido
                </div>
                <h3 class="empty-title">No se encontraron tickets</h3>
                <p class="empty-description">No hay tickets que coincidan con los criterios de búsqueda.</p>
                <a href="create_ticket.php" class="btn btn-primary">Crear nuevo ticket</a>
            </div>
        <?php else: ?>
            <?php foreach($tickets as $ticket): ?>
                <div class="ticket-item priority-<?php echo strtolower($ticket['priority']); ?>">
                    <div class="ticket-info">
                        <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="ticket-title">
                            <?php echo htmlspecialchars($ticket['subject']); ?>
                        </a>
                        <div class="ticket-meta">
                            <div class="ticket-meta-item priority-<?php echo strtolower($ticket['priority']); ?>">
                                <span class="priority-indicator"></span>
                                <span>Prioridad: <?php echo htmlspecialchars($ticket['priority']); ?></span>
                            </div>
                            <div class="ticket-meta-item">
                                <span>Asignado a: <?php echo htmlspecialchars($ticket['assignment']); ?></span>
                            </div>
                            <div class="ticket-meta-item">
                                <span>Actualizado: <?php echo date('d/m/Y H:i', strtotime($ticket['updated_at'] ?? $ticket['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="ticket-status <?php echo strtolower($ticket['status']); ?>">
                        <?php echo htmlspecialchars($ticket['status']); ?>
                    </div>
                    <div class="ticket-actions" style="margin-top: 8px;">
                        <a href="delete_ticket.php?id=<?php echo $ticket['id']; ?>" 
                        class="btn btn-danger" 
                        onclick="return confirm('¿Estás seguro de eliminar este ticket?');">
                            Eliminar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.ticket-item').forEach(item => {
            const priorityText = item.querySelector('.ticket-meta-item:first-child').textContent;
            if (priorityText.includes('High')) {
                item.classList.add('priority-high');
            } else if (priorityText.includes('Medium')) {
                item.classList.add('priority-medium');
            } else if (priorityText.includes('Low')) {
                item.classList.add('priority-low');
            }
        });
        
        const refreshBtn = document.getElementById('refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                this.classList.add('rotating');
                window.location.reload();
            });
        }
    });
</script>

<style>
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .rotating {
        animation: rotate 1s linear;
    }
</style>
<esi:include src="http://127.0.0.1:8080/snippet.php" />
<?php
include 'footer.php';
$html = ob_get_clean();
echo $html;
?>