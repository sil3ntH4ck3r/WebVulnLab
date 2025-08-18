package com.techrepair.controller;

import com.techrepair.model.RepairRequest;
import com.techrepair.service.RepairService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import javax.servlet.http.HttpServletRequest;
import java.util.List;

@Controller
@RequestMapping("/")
public class RepairController {
    
    @Autowired
    private RepairService repairService;
    
    @GetMapping("/")
    public String index(Model model) {
        model.addAttribute("repairRequest", new RepairRequest());
        return "index";
    }
    
    @PostMapping("/submit")
    public String submitRepair(@ModelAttribute RepairRequest request, Model model) {
        try {
            RepairRequest saved = repairService.saveRepairRequest(request);
            model.addAttribute("success", true);
            model.addAttribute("requestId", saved.getId());
            model.addAttribute("repairRequest", new RepairRequest());
        } catch (Exception e) {
            model.addAttribute("error", "Error al procesar la solicitud: " + e.getMessage());
            model.addAttribute("repairRequest", request);
        }
        return "index";
    }
    
    @GetMapping("/admin")
    public String admin(Model model) {
        List<RepairRequest> requests = repairService.getAllRepairRequests();
        model.addAttribute("requests", requests);
        return "admin";
    }
    
    @GetMapping("/status/{id}")
    public String checkStatus(@PathVariable String id, Model model) {
        RepairRequest request = repairService.getRepairRequestById(id);
        if (request != null) {
            model.addAttribute("request", request);
        } else {
            model.addAttribute("error", "Solicitud no encontrada");
        }
        return "status";
    }
    
    @PostMapping("/admin/update/{id}")
    public String updateStatus(@PathVariable String id, 
                             @RequestParam String status,
                             @RequestParam(required = false) Double estimatedCost,
                             Model model) {
        try {
            repairService.updateStatus(id, status, estimatedCost);
            model.addAttribute("success", "Estado actualizado correctamente");
        } catch (Exception e) {
            model.addAttribute("error", "Error al actualizar: " + e.getMessage());
        }
        return "redirect:/admin";
    }
}