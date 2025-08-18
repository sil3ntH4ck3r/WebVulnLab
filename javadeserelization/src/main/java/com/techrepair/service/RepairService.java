package com.techrepair.service;

import com.techrepair.model.RepairRequest;
import org.springframework.stereotype.Service;

import java.util.*;
import java.util.concurrent.ConcurrentHashMap;
import java.util.stream.Collectors;

@Service
public class RepairService {
    
    private final Map<String, RepairRequest> repairRequests = new ConcurrentHashMap<>();
    private final Random random = new Random();
    
    public RepairService() {
        // Datos de prueba iniciales
        initializeSampleData();
    }
    
    private void initializeSampleData() {
        RepairRequest req1 = new RepairRequest("Aroa Campoy", "acampoy@javadeserelization.local", 
                "Laptop", "Dell", "Inspiron 15", "La pantalla parpadea");
        req1.setId("REP-165");
        req1.setPriority("HIGH");
        req1.setEstimatedCost(150.0);
        repairRequests.put(req1.getId(), req1);
        
        RepairRequest req2 = new RepairRequest("Abdellah Vega", "avega@javadeserelization.local", 
                "Desktop", "HP", "Pavilion", "No enciende");
        req2.setId("REP-489");
        req2.setPriority("MEDIUM");
        req2.setStatus("IN_PROGRESS");
        req2.setEstimatedCost(200.0);
        repairRequests.put(req2.getId(), req2);
        
        RepairRequest req3 = new RepairRequest("Gloria Jimenez", "gjimenez@javadeserelization.local", 
                "Tablet", "Samsung", "Galaxy Tab", "Pantalla rota");
        req3.setId("REP-223");
        req3.setPriority("LOW");
        req3.setStatus("COMPLETED");
        req3.setEstimatedCost(80.0);
        repairRequests.put(req3.getId(), req3);
    }
    
    public RepairRequest saveRepairRequest(RepairRequest request) {
        if (request.getId() == null || request.getId().isEmpty()) {
            request.setId(generateId());
        }
        
        // Asignar prioridad automáticamente si no está definida
        if (request.getPriority() == null) {
            request.setPriority(determinePriority(request));
        }
        
        repairRequests.put(request.getId(), request);
        return request;
    }
    
    public RepairRequest getRepairRequestById(String id) {
        return repairRequests.get(id);
    }
    
    public List<RepairRequest> getAllRepairRequests() {
        return new ArrayList<>(repairRequests.values());
    }
    
    public void updateStatus(String id, String status, Double estimatedCost) {
        RepairRequest request = repairRequests.get(id);
        if (request != null) {
            request.setStatus(status);
            if (estimatedCost != null) {
                request.setEstimatedCost(estimatedCost);
            }
        }
    }
    
    public List<RepairRequest> getRequestsByStatus(String status) {
        return repairRequests.values().stream()
                .filter(req -> status.equals(req.getStatus()))
                .collect(Collectors.toList());
    }
    
    public List<RepairRequest> getRequestsByCustomer(String customerEmail) {
        return repairRequests.values().stream()
                .filter(req -> customerEmail.equals(req.getCustomerEmail()))
                .collect(Collectors.toList());
    }
    
    private String generateId() {
        return "REP-" + String.format("%03d", random.nextInt(1000) + repairRequests.size());
    }
    
    private String determinePriority(RepairRequest request) {
        String problem = request.getProblemDescription().toLowerCase();
        
        if (problem.contains("no enciende") || problem.contains("pantalla rota") || 
            problem.contains("virus") || problem.contains("dato perdido")) {
            return "HIGH";
        } else if (problem.contains("lento") || problem.contains("ruido") || 
                   problem.contains("calentamiento")) {
            return "MEDIUM";
        } else {
            return "LOW";
        }
    }
    
    public Map<String, Integer> getStatistics() {
        Map<String, Integer> stats = new HashMap<>();
        stats.put("total", repairRequests.size());
        stats.put("pending", (int) repairRequests.values().stream()
                .filter(r -> "PENDING".equals(r.getStatus())).count());
        stats.put("in_progress", (int) repairRequests.values().stream()
                .filter(r -> "IN_PROGRESS".equals(r.getStatus())).count());
        stats.put("completed", (int) repairRequests.values().stream()
                .filter(r -> "COMPLETED".equals(r.getStatus())).count());
        return stats;
    }
}