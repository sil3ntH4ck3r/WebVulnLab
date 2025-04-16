<?php
function getTickets() {
    $file = __DIR__ . '/tickets.json';
    if(!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $data = file_get_contents($file);
    $tickets = json_decode($data, true);
    return is_array($tickets) ? $tickets : [];
}

function saveTickets($tickets) {
    $file = __DIR__ . '/tickets.json';
    file_put_contents($file, json_encode($tickets, JSON_PRETTY_PRINT));
}

function addTicket($ticket) {
    $tickets = getTickets();
    $ticket['id'] = count($tickets) > 0 ? end($tickets)['id'] + 1 : 1;
    $ticket['created_at'] = date("Y-m-d H:i:s");
    $ticket['updated_at'] = date("Y-m-d H:i:s");
    $ticket['history'] = [];
    $tickets[] = $ticket;
    saveTickets($tickets);
    return $ticket['id'];
}

function getTicketById($id) {
    $tickets = getTickets();
    foreach($tickets as $ticket) {
        if($ticket['id'] == $id) {
            return $ticket;
        }
    }
    return null;
}

function updateTicket($id, $newData) {
    $tickets = getTickets();
    foreach($tickets as &$ticket) {
        if($ticket['id'] == $id) {
            $ticket = array_merge($ticket, $newData);
            saveTickets($tickets);
            return true;
        }
    }
    return false;
}

function deleteTicket($id) {
    $file = __DIR__ . '/tickets.json';
    if (!file_exists($file)) {
        return false;
    }
    $tickets = json_decode(file_get_contents($file), true);
    if (!$tickets) {
        return false;
    }
    $tickets = array_filter($tickets, function($ticket) use ($id) {
        return $ticket['id'] != $id;
    });
    $tickets = array_values($tickets);
    file_put_contents($file, json_encode($tickets, JSON_PRETTY_PRINT));
    return true;
}
?>
