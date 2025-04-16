<?php
ob_start();
include 'functions.php';
include 'header.php';
if (!isset($_GET['id'])) {
    echo '<div class="container">
            <div class="alert alert-error">
                <div>
                    <h4>Error</h4>
                    <p>No se ha especificado ningún ticket.</p>
                </div>
                <a href="index.php" class="btn btn-sm btn-outline">Volver al listado</a>
            </div>
          </div>';
    include 'footer.php';
    exit;
}
$ticketId = intval($_GET['id']);
$ticket = getTicketById($ticketId);
if (!$ticket) {
    echo '<div class="container">
            <div class="alert alert-error">
                <div>
                    <h4>Error</h4>
                    <p>El ticket no se ha encontrado.</p>
                </div>
                <a href="index.php" class="btn btn-sm btn-outline">Volver al listado</a>
            </div>
          </div>';
    include 'footer.php';
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $tickets = getTickets();
    foreach($tickets as &$t) {
        if($t['id'] == $ticketId) {
            $t['history'][] = [
                'message' => $message,
                'timestamp' => date("Y-m-d H:i:s"),
                'user' => 'Admin'
            ];
            if(isset($_POST['status']) && in_array($_POST['status'], ['Open', 'Closed'])) {
                $t['status'] = $_POST['status'];
            }
            break;
        }
    }
    saveTickets($tickets);
    header("Location: view_ticket.php?id=" . $ticketId);
    exit;
}

$createdDate = isset($ticket['created_at']) ? date("d M Y, H:i", strtotime($ticket['created_at'])) : 'N/A';

$statusClass = strtolower($ticket['status']);
$priorityClass = strtolower($ticket['priority']);
?>

<div class="container animate-in">
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="index.php" class="btn btn-outline">
                Volver
            </a>
            <h1 class="page-title">Detalles del Ticket</h1>
        </div>

        <div class="header-actions">
            <a href="edit_ticket.php?id=<?php echo $ticketId; ?>" class="btn btn-outline">
                Editar
            </a>
        </div>
    </div>

    <div class="ticket-detail-container">
        <div class="ticket-sidebar">
            <div class="ticket-list-panel">
                <div class="panel-header">
                    <h3 class="panel-title">Tickets recientes</h3>
                </div>
                <div class="ticket-list-sidebar">
                    <?php
                    $tickets = getTickets();
                    $recentTickets = array_slice($tickets, 0, 5);
                    foreach($recentTickets as $t) {
                        $isActive = $t['id'] == $ticketId ? 'active' : '';
                        $ticketStatusClass = strtolower($t['status']);
                        ?>
                        <a href="view_ticket.php?id=<?php echo $t['id']; ?>" class="sidebar-ticket-item <?php echo $isActive; ?>">
                            <div class="ticket-dot <?php echo $ticketStatusClass; ?>"></div>
                            <span class="sidebar-ticket-title"><?php echo htmlspecialchars($t['subject']); ?></span>
                        </a>
                        <?php
                    }
                    ?>
                    <a href="index.php" class="sidebar-view-all">Ver todos los tickets</a>
                </div>
            </div>

            <div class="ticket-actions-panel">
                <div class="panel-header">
                    <h3 class="panel-title">Acciones rápidas</h3>
                </div>
                <div class="quick-actions">
                    <button class="btn btn-outline btn-block mb-2" id="assignToMe">
                        Asignarme este ticket
                    </button>
                    
                    <div class="status-buttons">
                        <form method="POST" class="status-form">
                            <input type="hidden" name="message" value="Estado cambiado a Cerrado">
                            <input type="hidden" name="status" value="Closed">
                            <button type="submit" class="btn btn-success btn-block mb-2">
                                Cerrar ticket
                            </button>
                        </form>
                        
                        <form method="POST" class="status-form">
                            <input type="hidden" name="message" value="Estado cambiado a Abierto">
                            <input type="hidden" name="status" value="Open">
                            <button type="submit" class="btn btn-warning btn-block">
                                Reabrir ticket
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="ticket-content">
            <div class="ticket-header">
                <div class="ticket-header-content">
                    <div class="ticket-badges">
                        <span class="ticket-status <?php echo $statusClass; ?>">
                            <?php if ($statusClass == 'open'): ?>
                            <?php else: ?>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($ticket['status']); ?>
                        </span>
                        
                        <span class="ticket-priority priority-<?php echo $priorityClass; ?>">
                            <span class="priority-indicator"></span>
                            <?php echo htmlspecialchars($ticket['priority']); ?>
                        </span>
                    </div>
                    
                    <h2 class="ticket-title"><?php echo htmlspecialchars($ticket['subject']); ?></h2>
                    
                    <div class="ticket-meta">
                        <div class="ticket-meta-item">
                            <span>Creado el <?php echo $createdDate; ?></span>
                        </div>
                        
                        <div class="ticket-meta-item">
                            <span>Asignado a: <strong><?php echo htmlspecialchars($ticket['assignment']); ?></strong></span>
                        </div>
                        
                        <div class="ticket-meta-item">
                            <span>ID: #<?php echo $ticketId; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="ticket-description">
                <div class="description-header">
                    <h3>Descripción</h3>
                </div>
                <div class="description-content">
                    <?php echo nl2br($ticket['description']); ?>
                </div>
            </div>

            <div class="ticket-timeline">
                <div class="timeline-header">
                    <h3>Historial de comunicación</h3>
                </div>
                
                <div class="timeline-stream">
                    <div class="timeline-item system">
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-title">Ticket creado</span>
                                <span class="timeline-date"><?php echo $createdDate; ?></span>
                            </div>
                            <div class="timeline-body">
                                <p>Se ha creado el ticket con prioridad <?php echo htmlspecialchars($ticket['priority']); ?> y asignado a <?php echo htmlspecialchars($ticket['assignment']); ?>.</p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($ticket['history'])): ?>
                        <?php foreach($ticket['history'] as $entry): 
                            $timestamp = date("d M Y, H:i", strtotime($entry['timestamp']));
                            $user = isset($entry['user']) ? $entry['user'] : 'Admin';
                        ?>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-title"><?php echo htmlspecialchars($user); ?></span>
                                        <span class="timeline-date"><?php echo $timestamp; ?></span>
                                    </div>
                                    <div class="timeline-body">
                                        <?php echo nl2br(htmlspecialchars($entry['message'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="add-response">
                <h3>Añadir respuesta</h3>
                <form method="POST" class="response-form">
                    <div class="form-group">
                        <textarea name="message" rows="4" placeholder="Escribe tu respuesta aquí..." required class="response-textarea"></textarea>
                    </div>
                    
                    <div class="form-actions">                        
                        <button type="submit" class="btn btn-primary">
                            Enviar respuesta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('printTicket');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.print();
            });
        }
    });
</script>

<style>
.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: var(--spacing-6);
}

.ticket-detail-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--spacing-6);
    margin-top: var(--spacing-6);
}

.ticket-sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-6);
}

.ticket-list-panel,
.ticket-actions-panel {
    background-color: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.panel-header {
    padding: var(--spacing-4);
    border-bottom: 1px solid var(--gray-200);
    background-color: var(--gray-100);
}

.panel-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0;
}

.ticket-list-sidebar {
    display: flex;
    flex-direction: column;
}

.sidebar-ticket-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    padding: var(--spacing-3) var(--spacing-4);
    border-bottom: 1px solid var(--gray-200);
    text-decoration: none;
    color: var(--gray-700);
    transition: var(--transition);
}

.sidebar-ticket-item:hover {
    background-color: var(--gray-100);
}

.sidebar-ticket-item.active {
    background-color: var(--primary-bg);
    color: var(--primary);
    font-weight: 500;
}

.ticket-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--gray-400);
    flex-shrink: 0;
}

.ticket-dot.open {
    background-color: var(--warning);
}

.ticket-dot.closed {
    background-color: var(--success);
}

.sidebar-ticket-title {
    font-size: 0.875rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-view-all {
    padding: var(--spacing-3) var(--spacing-4);
    text-align: center;
    color: var(--primary);
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    background-color: var(--primary-bg);
}

.quick-actions {
    padding: var(--spacing-4);
}

.btn-block {
    width: 100%;
    justify-content: center;
}

.btn-success {
    background-color: var(--success);
    color: white;
}

.btn-success:hover {
    background-color: #0e9f6e;
}

.btn-warning {
    background-color: var(--warning);
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
}

.ticket-content {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-6);
}

.ticket-header {
    background-color: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-6);
    box-shadow: var(--shadow);
}

.ticket-badges {
    display: flex;
    gap: var(--spacing-3);
    margin-bottom: var(--spacing-3);
}

.badge-icon {
    margin-right: var(--spacing-1);
}

.ticket-header .ticket-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-4);
}

.ticket-description,
.ticket-timeline,
.add-response {
    background-color: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.description-header,
.timeline-header {
    padding: var(--spacing-4) var(--spacing-6);
    border-bottom: 1px solid var(--gray-200);
}

.description-header h3,
.timeline-header h3,
.add-response h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0;
}

.description-content {
    padding: var(--spacing-6);
    font-size: 0.9375rem;
    line-height: 1.6;
}

.timeline-stream {
    padding: var(--spacing-6);
}

.timeline-item {
    display: flex;
    gap: var(--spacing-4);
    padding-bottom: var(--spacing-6);
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 30px;
    left: 8px;
    bottom: 0;
    width: 2px;
    background-color: var(--gray-200);
}

.timeline-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary-bg);
    border-radius: 50%;
    padding: 6px;
    z-index: 1;
}

.timeline-item.system .timeline-icon {
    color: var(--primary);
    background-color: var(--primary-bg);
}

.timeline-content {
    flex: 1;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    padding: 0;
    border: none;
    margin-bottom: var(--spacing-2);
}

.timeline-title {
    font-weight: 600;
    color: var(--gray-900);
}

.timeline-date {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.timeline-body {
    background-color: var(--gray-100);
    padding: var(--spacing-4);
    border-radius: var(--radius);
    font-size: 0.9375rem;
}

.timeline-item.system .timeline-body {
    background-color: var(--primary-bg);
    color: var(--gray-700);
}

.add-response {
    padding: var(--spacing-6);
}

.response-textarea {
    width: 100%;
    padding: var(--spacing-4);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-family: inherit;
    resize: vertical;
    min-height: 120px;
    transition: var(--transition);
}

.response-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-light);
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: var(--spacing-4);
}

.status-select {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
}

.alert {
    display: flex;
    padding: var(--spacing-4);
    border-radius: var(--radius);
    margin-bottom: var(--spacing-6);
    align-items: flex-start;
    gap: var(--spacing-4);
}

.alert-error {
    background-color: var(--danger-light);
    color: var(--danger);
}

.alert h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 var(--spacing-1) 0;
}

.alert p {
    margin: 0;
}

@media (max-width: 960px) {
    .ticket-detail-container {
        grid-template-columns: 1fr;
    }
    
    .ticket-sidebar {
        order: 2;
    }
    
    .ticket-content {
        order: 1;
    }
}
</style>
<esi:include src="http://127.0.0.1:8080/snippet.php" />
<?php
include 'footer.php';
$html = ob_get_clean();
echo $html;
?>