<?php
ob_start();
include __DIR__ . '/functions.php';
include __DIR__ . '/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$ticketId = (int)$_GET['id'];
$ticket = getTicketById($ticketId);

if (!$ticket) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'Medium';
    $assignment = trim($_POST['assignment'] ?? '');
    $status = $_POST['status'] ?? 'Open';
    
    $errors = [];
    if (empty($subject)) {
        $errors['subject'] = 'El asunto es obligatorio';
    }
    if (empty($description)) {
        $errors['description'] = 'La descripción es obligatoria';
    }
    
    if (empty($errors)) {
        $updatedTicket = [
            'id' => $ticketId,
            'subject' => $subject,
            'description' => $description,
            'priority' => $priority,
            'assignment' => $assignment,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        updateTicket($ticketId, $updatedTicket);
        header("Location: view_ticket.php?id=" . $ticketId);
        exit;
    }
} else {
    $subject = $ticket['subject'];
    $description = $ticket['description'];
    $priority = $ticket['priority'];
    $assignment = $ticket['assignment'];
    $status = $ticket['status'];
}
?>

<div class="container animate-in">
    <div class="page-header">
        <h1 class="page-title">Editar Ticket #<?php echo $ticketId; ?></h1>
        <div class="header-actions">
            <a href="view_ticket.php?id=<?php echo $ticketId; ?>" class="btn btn-outline">
                Volver al Ticket
            </a>
        </div>
    </div>

    <div class="ticket-form-container" style="background-color: white; padding: var(--spacing-6); border-radius: var(--radius-lg); box-shadow: var(--shadow);">
        <form method="POST" action="">
            <div class="form-group">
                <label for="subject">Asunto:</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" placeholder="Ingrese el asunto del ticket" required>
                <?php if (!empty($errors['subject'])): ?>
                    <p class="error-message" style="color: var(--danger); font-size: 0.875rem; margin-top: var(--spacing-1);"><?php echo $errors['subject']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="description">Descripción:</label>
                <textarea id="description" name="description" rows="6" placeholder="Describa detalladamente el problema o solicitud" required><?php echo htmlspecialchars($description); ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <p class="error-message" style="color: var(--danger); font-size: 0.875rem; margin-top: var(--spacing-1);"><?php echo $errors['description']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-6);">
                <div class="form-group">
                    <label>Prioridad:</label>
                    <div class="priority-options" style="display: flex; gap: var(--spacing-4); margin-top: var(--spacing-2);">
                        <label class="priority-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                            <input type="radio" name="priority" value="Low" <?php if($priority === 'Low') echo 'checked'; ?>>
                            <div class="priority-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--success);"></div>
                            <span>Baja</span>
                        </label>
                        
                        <label class="priority-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                            <input type="radio" name="priority" value="Medium" <?php if($priority === 'Medium') echo 'checked'; ?>>
                            <div class="priority-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--warning);"></div>
                            <span>Media</span>
                        </label>
                        
                        <label class="priority-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                            <input type="radio" name="priority" value="High" <?php if($priority === 'High') echo 'checked'; ?>>
                            <div class="priority-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--danger);"></div>
                            <span>Alta</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Estado:</label>
                    <div class="status-options" style="display: flex; gap: var(--spacing-4); margin-top: var(--spacing-2);">
                        <label class="status-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                            <input type="radio" name="status" value="Open" <?php if($status === 'Open') echo 'checked'; ?>>
                            <div class="status-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--warning);"></div>
                            <span>Abierto</span>
                        </label>
                        
                        <label class="status-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                            <input type="radio" name="status" value="Closed" <?php if($status === 'Closed') echo 'checked'; ?>>
                            <div class="status-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--success);"></div>
                            <span>Cerrado</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="assignment">Asignar a:</label>
                <input type="text" id="assignment" name="assignment" value="<?php echo htmlspecialchars($assignment); ?>" placeholder="Nombre del agente encargado">
            </div>
            
            <div class="form-group">
                <label for="lastUpdate" style="display: block; margin-bottom: var(--spacing-2);">Última actualización:</label>
                <div class="last-update-info" style="font-size: 0.875rem; color: var(--gray-600); background-color: var(--gray-100); padding: var(--spacing-3); border-radius: var(--radius); display: inline-block;">
                    <?php echo date('d/m/Y H:i', strtotime($ticket['updated_at'] ?? $ticket['created_at'])); ?>
                </div>
            </div>
            
            <div class="form-actions" style="display: flex; justify-content: space-between; margin-top: var(--spacing-6);">
                <a href="view_ticket.php?id=<?php echo $ticketId; ?>" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    
        const formElements = document.querySelectorAll('input, textarea, select');
        formElements.forEach((element, index) => {
            element.style.transition = 'all 0.3s';
            element.style.animationDelay = (index * 0.05) + 's';
            element.classList.add('animate-in');
        });
        
        const trackChanges = (element) => {
            const originalValue = element.value;
            element.addEventListener('change', function() {
                if (this.value !== originalValue) {
                    this.style.borderColor = 'var(--primary)';
                    this.style.backgroundColor = 'var(--primary-bg)';
                } else {
                    this.style.borderColor = 'var(--gray-300)';
                    this.style.backgroundColor = 'white';
                }
            });
        };
        
        document.querySelectorAll('input[type="text"], textarea').forEach(element => {
            trackChanges(element);
        });
    });
</script>
<esi:include src="http://127.0.0.1:8080/snippet.php" />
<?php
include 'footer.php';
$html = ob_get_clean();
echo $html;
?>