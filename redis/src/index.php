<?php

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
} catch (Exception $e) {
    die("No se pudo conectar a Redis: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
    $task = $_POST['task'];
    $priority = isset($_POST['priority']) ? $_POST['priority'] : 'normal';
    $task_data = json_encode(['task' => $task, 'priority' => $priority, 'created' => date('Y-m-d H:i:s')]);
    $redis->rPush('tasks', $task_data);
}


if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $index = $_GET['delete'];
    $tasks = $redis->lRange('tasks', 0, -1);
    if (isset($tasks[$index])) {
        $redis->lRem('tasks', $tasks[$index], 1);
    }
    header("Location: index.php"); 
    exit(); 
}


$tasks_json = $redis->lRange('tasks', 0, -1);
$tasks = [];
foreach ($tasks_json as $task_json) {
    $tasks[] = json_decode($task_json, true);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redis</title>
    <style>
        :root {
            --yellow-postit: #fff9c4;
            --green-postit: #e8f5e9;
            --blue-postit: #e3f2fd;
            --pink-postit: #fce4ec;
            --orange-postit: #fff3e0;
            --dark-color: #37474f;
            --gray-color: #78909c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Comic Sans MS', 'Marker Felt', cursive;
        }
        
        body {
            background-color: #f5f5f5;
            color: var(--dark-color);
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        h1 {
            color: var(--dark-color);
            font-size: 2.8rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 0px rgba(0,0,0,0.1);
        }
        
        p.subtitle {
            color: var(--gray-color);
            font-size: 1.2rem;
        }
        
        .task-form {
            padding: 20px;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .task-form::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            height: 20px;
            background-color: rgba(0, 0, 0, 0.03);
            border-radius: 50%;
            z-index: -1;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark-color);
            font-size: 1.1rem;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px dashed #ccc;
            border-radius: 5px;
            font-size: 1.1rem;
            font-family: 'Comic Sans MS', 'Marker Felt', cursive;
            transition: border-color 0.3s;
            background-color: #fffde7;
        }
        
        input[type="text"]:focus {
            border-color: #9e9e9e;
            outline: none;
        }
        
        .priority-selector {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .priority-option {
            flex: 1;
        }
        
        .priority-option input[type="radio"] {
            display: none;
        }
        
        .priority-option label {
            display: block;
            padding: 10px;
            text-align: center;
            background-color: #f5f5f5;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px dashed transparent;
        }
        
        .priority-option input[type="radio"]:checked + label.low {
            background-color: var(--green-postit);
            border-color: #81c784;
        }
        
        .priority-option input[type="radio"]:checked + label.normal {
            background-color: var(--yellow-postit);
            border-color: #fff176;
        }
        
        .priority-option input[type="radio"]:checked + label.high {
            background-color: var(--pink-postit);
            border-color: #f48fb1;
        }
        
        button {
            background-color: #4db6ac;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
            font-family: 'Comic Sans MS', 'Marker Felt', cursive;
        }
        
        button:hover {
            background-color: #26a69a;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.15);
        }
        
        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .tasks-section h2 {
            color: var(--dark-color);
            margin-bottom: 30px;
            font-size: 2rem;
            text-align: center;
            position: relative;
        }
        
        .tasks-section h2::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background-color: #26a69a;
            margin: 10px auto 0;
            border-radius: 3px;
        }
        
        .task-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 10px;
        }
        
        .task-item {
            position: relative;
            padding: 20px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            border-radius: 2px;
            transform: rotate(var(--rotation));
            --rotation: 0deg;
        }
        
        .task-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 20px;
            background-color: rgba(0,0,0,0.1);
            border-radius: 2px 2px 0 0;
        }
        
        .task-item::after {
            content: '';
            position: absolute;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 30px;
            background-color: rgba(0,0,0,0.05);
            border-radius: 50%;
            box-shadow: 0 0 0 7px rgba(255,255,255,0.7);
            z-index: 1;
        }
        
        .task-item:nth-child(odd) {
            --rotation: -1deg;
        }
        
        .task-item:nth-child(even) {
            --rotation: 1deg;
        }
        
        .task-item:nth-child(3n) {
            --rotation: -2deg;
        }
        
        .task-item:nth-child(5n) {
            --rotation: 2deg;
        }
        
        .task-item:hover {
            transform: scale(1.05) rotate(0);
            z-index: 5;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .task-item.low-priority {
            background-color: var(--green-postit);
        }
        
        .task-item.normal-priority {
            background-color: var(--yellow-postit);
        }
        
        .task-item.high-priority {
            background-color: var(--pink-postit);
        }
        
        .task-content {
            flex: 1;
            margin-top: 10px;
            z-index: 2;
        }
        
        .task-name {
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 15px;
            line-height: 1.4;
            color: var(--dark-color);
            word-break: break-word;
        }
        
        .task-meta {
            margin-top: auto;
            font-size: 0.9rem;
            color: var(--gray-color);
        }
        
        .task-priority {
            font-size: 0.9rem;
            display: inline-block;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .task-actions {
            text-align: right;
            margin-top: 15px;
        }
        
        .task-actions a {
            color: var(--dark-color);
            text-decoration: none;
            padding: 5px 10px;
            background-color: rgba(255,255,255,0.6);
            border-radius: 20px;
            transition: all 0.3s;
            font-size: 0.9rem;
            border: 1px dashed rgba(0,0,0,0.2);
        }
        
        .task-actions a:hover {
            background-color: rgba(255,255,255,0.9);
            color: #e53935;
            border-color: #e53935;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-color);
            background-color: rgba(255,255,255,0.8);
            border-radius: 10px;
            margin: 0 auto;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .empty-state::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 30px;
            background-color: #4db6ac;
            border-radius: 3px;
            opacity: 0.2;
        }
        
        .empty-state svg {
            margin-bottom: 20px;
            fill: #bdbdbd;
            width: 100px;
            height: 100px;
        }
        
        .empty-state p {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
            
            h1 {
                font-size: 2.2rem;
            }
            
            .task-list {
                grid-template-columns: 1fr;
            }
            
            .task-item {
                min-height: 100px;
            }
        }
        footer {
            text-align: center;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Tablón de Notas</h1>
            <p class="subtitle">Organiza tus ideas con notas adhesivas</p>
        </header>
        
        <section class="task-form">
            <form method="post">
                <div class="form-group">
                    <label for="task">Nueva Tarea</label>
                    <input type="text" id="task" name="task" placeholder="¿Qué necesitas hacer?" required>
                </div>
                
                <div class="form-group">
                    <label>Prioridad</label>
                    <div class="priority-selector">
                        <div class="priority-option">
                            <input type="radio" id="low" name="priority" value="low">
                            <label for="low" class="low">Baja</label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" id="normal" name="priority" value="normal" checked>
                            <label for="normal" class="normal">Normal</label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" id="high" name="priority" value="high">
                            <label for="high" class="high">Alta</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit">Añadir Tarea</button>
            </form>
        </section>
        
        <section class="tasks-section">
            <h2>Mis Notas</h2>
            
            <?php if (empty($tasks)): ?>
                <div class="empty-state">
                    <p>¡No hay notas adhesivas!</p>
                    <p>Añade una nueva nota para comenzar</p>
                </div>
            <?php else: ?>
                <ul class="task-list">
                    <?php foreach ($tasks as $index => $task): ?>
                    <li class="task-item <?php echo $task['priority']; ?>-priority">
                        <div class="task-content">
                            <div class="task-name"><?php echo htmlspecialchars($task['task']); ?></div>
                            <div class="task-meta">
                                <span class="task-priority">
                                    <?php 
                                    $priority_text = '';
                                    switch($task['priority']) {
                                        case 'low': $priority_text = 'Prioridad: Baja'; break;
                                        case 'normal': $priority_text = 'Prioridad: Normal'; break;
                                        case 'high': $priority_text = 'Prioridad: Alta'; break;
                                    }
                                    echo $priority_text;
                                    ?>
                                </span>
                                <br>
                                <span class="task-date"><?php echo htmlspecialchars($task['created']); ?></span>
                            </div>
                        </div>
                        <div class="task-actions">
                            <a href="?delete=<?php echo $index; ?>" onclick="return confirm('¿Quieres quitar esta nota?')">Quitar</a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</body>
<footer>
    <div class="container">
        <p>&copy; <a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1">CC BY-NC-SA 4.0
    </div>
</footer>
</html>