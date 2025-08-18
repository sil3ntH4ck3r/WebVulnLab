package com.techrepair.controller;

import com.techrepair.model.RepairRequest;
import com.techrepair.service.RepairService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.apache.commons.codec.binary.Base64;

import java.io.*;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Controlador para funciones de importación y exportación de datos
 * VULNERABLE: Este controlador contiene una vulnerabilidad de deserialización insegura
 */
@RestController
@RequestMapping("/api")
public class ImportExportController {
    
    @Autowired
    private RepairService repairService;
    
    /**
     * Endpoint para exportar solicitudes de reparación en formato serializado
     */
    @GetMapping("/export")
    public ResponseEntity<Map<String, String>> exportRequests() {
        try {
            List<RepairRequest> requests = repairService.getAllRepairRequests();
            
            // Serializar la lista de requests
            ByteArrayOutputStream baos = new ByteArrayOutputStream();
            ObjectOutputStream oos = new ObjectOutputStream(baos);
            oos.writeObject(requests);
            oos.close();
            
            String encodedData = Base64.encodeBase64String(baos.toByteArray());
            
            Map<String, String> response = new HashMap<>();
            response.put("data", encodedData);
            response.put("message", "Datos exportados correctamente");
            response.put("count", String.valueOf(requests.size()));
            
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            Map<String, String> error = new HashMap<>();
            error.put("error", "Error al exportar datos: " + e.getMessage());
            return ResponseEntity.internalServerError().body(error);
        }
    }
    
    /**
     * VULNERABLE ENDPOINT: Deserialización insegura
     * Este endpoint acepta datos serializados y los deserializa sin validación
     * Esto puede ser explotado para ejecutar código arbitrario
     */
    @PostMapping("/import")
    public ResponseEntity<Map<String, String>> importRequests(@RequestParam String data) {
        try {
            // Decodificar los datos Base64
            byte[] decodedData = Base64.decodeBase64(data);
            
            // VULNERABLE: Deserialización directa sin validación
            ByteArrayInputStream bais = new ByteArrayInputStream(decodedData);
            ObjectInputStream ois = new ObjectInputStream(bais);
            
            // Esta línea es vulnerable - puede ejecutar código malicioso
            Object deserializedObject = ois.readObject();
            ois.close();
            
            Map<String, String> response = new HashMap<>();
            
            if (deserializedObject instanceof List) {
                @SuppressWarnings("unchecked")
                List<RepairRequest> requests = (List<RepairRequest>) deserializedObject;
                
                // Procesar las solicitudes importadas
                for (RepairRequest request : requests) {
                    repairService.saveRepairRequest(request);
                }
                
                response.put("message", "Datos importados correctamente");
                response.put("count", String.valueOf(requests.size()));
                return ResponseEntity.ok(response);
            } else {
                response.put("message", "Procesamiento completado");
                response.put("type", deserializedObject.getClass().getSimpleName());
                return ResponseEntity.ok(response);
            }
            
        } catch (Exception e) {
            Map<String, String> error = new HashMap<>();
            error.put("error", "Error al importar datos: " + e.getMessage());
            error.put("details", e.getClass().getSimpleName());
            return ResponseEntity.badRequest().body(error);
        }
    }
    
    /**
     * Endpoint para obtener estadísticas (funcionalidad legítima)
     */
    @GetMapping("/stats")
    public ResponseEntity<Map<String, Object>> getStats() {
        try {
            List<RepairRequest> requests = repairService.getAllRepairRequests();
            
            Map<String, Object> stats = new HashMap<>();
            stats.put("total", requests.size());
            
            long pending = requests.stream().filter(r -> "PENDING".equals(r.getStatus())).count();
            long inProgress = requests.stream().filter(r -> "IN_PROGRESS".equals(r.getStatus())).count();
            long completed = requests.stream().filter(r -> "COMPLETED".equals(r.getStatus())).count();
            
            Map<String, Long> statusCount = new HashMap<>();
            statusCount.put("pending", pending);
            statusCount.put("in_progress", inProgress);
            statusCount.put("completed", completed);
            
            stats.put("by_status", statusCount);
            
            return ResponseEntity.ok(stats);
        } catch (Exception e) {
            Map<String, Object> error = new HashMap<>();
            error.put("error", "Error al obtener estadísticas");
            return ResponseEntity.internalServerError().body(error);
        }
    }
}