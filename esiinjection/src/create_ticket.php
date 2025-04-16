<?php
ob_start();
include __DIR__ . '/functions.php';
include __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'Medium';
    $assignment = trim($_POST['assignment'] ?? '');
    
    $errors = [];
    if (empty($subject)) {
        $errors['subject'] = 'El asunto es obligatorio';
    }
    if (empty($description)) {
        $errors['description'] = 'La descripción es obligatoria';
    }
    
    if (empty($errors)) {
        $ticket = [
            'subject' => $subject,
            'description' => $description,
            'priority' => $priority,
            'assignment' => $assignment,
            'status' => 'Open'
        ];
        
        $ticketId = addTicket($ticket);
        header("Location: view_ticket.php?id=" . $ticketId);
        exit;
    }
}
?>

<div class="container animate-in">
    <div class="page-header">
        <h1 class="page-title">Crear Nuevo Ticket</h1>
        <div class="header-actions">
            <a href="index.php" class="btn btn-outline">
                Volver a Tickets
            </a>
        </div>
    </div>

    <div class="ticket-form-container" style="background-color: white; padding: var(--spacing-6); border-radius: var(--radius-lg); box-shadow: var(--shadow);">
        <form method="POST" action="">
            <div class="form-group">
                <label for="subject">Asunto:</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" placeholder="Ingrese el asunto del ticket" required>
                <?php if (!empty($errors['subject'])): ?>
                    <p class="error-message" style="color: var(--danger); font-size: 0.875rem; margin-top: var(--spacing-1);"><?php echo $errors['subject']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="description">Descripción:</label>
                <textarea id="description" name="description" rows="6" placeholder="Describa detalladamente el problema o solicitud" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <p class="error-message" style="color: var(--danger); font-size: 0.875rem; margin-top: var(--spacing-1);"><?php echo $errors['description']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Prioridad:</label>
                <div class="priority-options" style="display: flex; gap: var(--spacing-4); margin-top: var(--spacing-2);">
                    <label class="priority-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                        <input type="radio" name="priority" value="Low" <?php if(($priority ?? 'Medium') === 'Low') echo 'checked'; ?>>
                        <div class="priority-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--success);"></div>
                        <span>Baja</span>
                    </label>
                    
                    <label class="priority-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                        <input type="radio" name="priority" value="Medium" <?php if(($priority ?? 'Medium') === 'Medium') echo 'checked'; ?>>
                        <div class="priority-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--warning);"></div>
                        <span>Media</span>
                    </label>
                    
                    <label class="priority-option" style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                        <input type="radio" name="priority" value="High" <?php if(($priority ?? 'Medium') === 'High') echo 'checked'; ?>>
                        <div class="priority-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--danger);"></div>
                        <span>Alta</span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="assignment">Asignar a:</label>
                <input type="text" id="assignment" name="assignment" value="<?php echo htmlspecialchars($assignment ?? ''); ?>" placeholder="Nombre">
            </div>
            
            <div class="form-actions" style="display: flex; justify-content: flex-end; gap: var(--spacing-4); margin-top: var(--spacing-6);">
                <a href="index.php" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    Crear Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('subject').focus();
        
        const formElements = document.querySelectorAll('input, textarea, select');
        formElements.forEach((element, index) => {
            element.style.transition = 'all 0.3s';
            element.style.animationDelay = (index * 0.05) + 's';
            element.classList.add('animate-in');
        });
    });
</script>
<esi:include src="http://127.0.0.1:8080/snippet.php" />
<?php
include 'footer.php';
$html = ob_get_clean();
echo $html;
?>