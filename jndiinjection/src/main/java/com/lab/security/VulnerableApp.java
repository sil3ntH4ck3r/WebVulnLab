package com.lab.security;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.web.embedded.tomcat.TomcatServletWebServerFactory;
import org.springframework.boot.web.server.WebServerFactoryCustomizer;
import org.springframework.context.annotation.Bean;
import org.springframework.web.bind.annotation.*;
import org.springframework.http.ResponseEntity;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.beans.factory.annotation.Autowired;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpSession;
import java.util.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

@SpringBootApplication
@RestController
public class VulnerableApp {
    
    private static final Logger logger = LogManager.getLogger(VulnerableApp.class);
    
    @Autowired
    private CustomerService customerService;
    
    @Autowired
    private AuditService auditService;
    
    public static void main(String[] args) {        
        SpringApplication.run(VulnerableApp.class, args);
    }
    
    @Bean
    public WebServerFactoryCustomizer<TomcatServletWebServerFactory> tomcatCustomizer() {
        return factory -> {
            factory.addConnectorCustomizers(connector -> {
                connector.setProperty("relaxedPathChars", "|{}[]");
                connector.setProperty("relaxedQueryChars", "|{}[]^`\"<>");
            });
        };
    }
    
    @GetMapping("/")
    public String home() {
        return "<!DOCTYPE html>" +
               "<html><head><title>JNDI Injection</title>" +
               "<meta name='viewport' content='width=device-width, initial-scale=1'>" +
               "<link href='/css/bootstrap.min.css' rel='stylesheet'>" +
               "<link href='/css/all.min.css' rel='stylesheet'>" +
               "</head><body class='bg-light'>" +
               "<nav class='navbar navbar-expand-lg navbar-dark bg-primary'>" +
               "<div class='container'>" +
               "<a class='navbar-brand' href='/'><i class='fas fa-building'></i> Analogical Ocean </a>" +
               "<span class='navbar-text text-light'>Customer Management System</span>" +
               "</div></nav>" +
               "<div class='container mt-4'>" +
               "<div class='row'>" +
               "<div class='col-lg-8'>" +
               "<div class='card'>" +
               "<div class='card-header bg-primary text-white'>" +
               "<h4><i class='fas fa-sign-in-alt'></i> Access</h4>" +
               "</div>" +
               "<div class='card-body'>" +
               "<form action='/auth/login' method='post'>" +
               "<div class='mb-3'>" +
               "<label class='form-label'>Employee ID:</label>" +
               "<input type='text' name='employeeId' class='form-control' placeholder='e.g., EMP001' required>" +
               "</div>" +
               "<div class='mb-3'>" +
               "<label class='form-label'>Password:</label>" +
               "<input type='password' name='password' class='form-control' required>" +
               "</div>" +
               "<div class='mb-3'>" +
               "<label class='form-label'>Department:</label>" +
               "<select name='department' class='form-select' required>" +
               "<option value=''>Select Department</option>" +
               "<option value='sales'>Sales</option>" +
               "<option value='marketing'>Marketing</option>" +
               "<option value='support'>Customer Support</option>" +
               "<option value='it'>IT Operations</option>" +
               "</select>" +
               "</div>" +
               "<button type='submit' class='btn btn-primary'><i class='fas fa-unlock'></i> Access System</button>" +
               "</form>" +
               "</div></div></div>" +
               "<div class='col-lg-4'>" +
               "<div class='card'>" +
               "<div class='card-header bg-info text-white'>" +
               "<h5><i class='fas fa-chart-line'></i> System Status</h5>" +
               "</div>" +
               "<div class='card-body'>" +
               "<p><i class='fas fa-database text-success'></i> Database: Online</p>" +
               "<p><i class='fas fa-server text-success'></i> Services: Running</p>" +
               "<p><i class='fas fa-users text-warning'></i> Active Users: 47</p>" +
               "<p><i class='fas fa-clock text-info'></i> Uptime: 15d 4h 23m</p>" +
               "</div></div>" +
               "<div class='card mt-3'>" +
               "<div class='card-header bg-secondary text-white'>" +
               "<h6><i class='fas fa-tools'></i> Quick Actions</h6>" +
               "</div>" +
               "<div class='card-body'>" +
               "<a href='/customers/search' class='btn btn-outline-primary btn-sm mb-2 w-100'>Customer Search</a>" +
               "<a href='/reports/dashboard' class='btn btn-outline-success btn-sm mb-2 w-100'>Sales Dashboard</a>" +
               "<a href='/api/health' class='btn btn-outline-info btn-sm mb-2 w-100'>System Health</a>" +
               "</div></div></div></div>" +
               "<div class='row mt-4'>" +
               "<div class='col-12'>" +
               "<div class='card'>" +
               "<div class='card-header'>" +
               "<h5><i class='fas fa-info-circle'></i> System Information</h5>" +
               "</div>" +
               "<div class='card-body'>" +
               "<div class='row'>" +
               "<div class='col-md-6'>" +
               "<h6>Available Endpoints:</h6>" +
               "<ul class='list-unstyled'>" +
               "<li><code>POST /auth/login</code> - Employee authentication</li>" +
               "<li><code>GET /customers/search</code> - Customer lookup</li>" +
               "<li><code>POST /customers/feedback</code> - Feedback submission</li>" +
               "<li><code>GET /reports/dashboard</code> - Analytics dashboard</li>" +
               "<li><code>GET /api/health</code> - System diagnostics</li>" +
               "</ul>" +
               "</div>" +
               "<div class='col-md-6'>" +
               "<h6>Test Credentials:</h6>" +
               "<p><strong>Employee ID:</strong> EMP001<br>" +
               "<strong>Password:</strong> Demo123<br>" +
               "<strong>Department:</strong> Any</p>" +
               "<small class='text-muted'>Preproduction system - Limited demo access</small><br>" +
               "<small class='text-muted'>Logging: Apache Log4j 2.14.1 | Build: Maven 3.8.2</small>" +
               "</div></div></div></div></div></div>" +
               "</div>" +
               "<footer class='mt-5 py-3 bg-dark text-light text-center'>" +
               "<div class='container'>" +
               "<p>&copy; <a property='dct:title' rel='cc:attributionURL' href='https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev'>WebVulnLab</a> by <a href='https://github.com/sil3ntH4ck3r'>sil3nth4ck3r</a> is licensed under <a href='http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1'>CC BY-NC-SA 4.0" +
               "</div>" +
               "</footer>" +
               "<script src='/js/bootstrap.bundle.min.js'></script>" +
               "</body></html>";
    }
    
    @PostMapping("/auth/login")
    public ResponseEntity<Map<String, Object>> authenticateEmployee(
            @RequestParam String employeeId,
            @RequestParam String password,
            @RequestParam String department,
            HttpServletRequest request,
            HttpSession session) {
        
        Map<String, Object> response = new HashMap<>();
        
        try {
            // Registro de auditoría para cumplimiento de seguridad
            auditService.logEmployeeAccess(employeeId, department, getClientIP(request));
            
            // Validación de credenciales empresariales
            if (customerService.validateEmployee(employeeId, password, department)) {
                String sessionId = UUID.randomUUID().toString();
                session.setAttribute("employeeId", employeeId);
                session.setAttribute("department", department);
                session.setAttribute("sessionId", sessionId);
                
                response.put("status", "authenticated");
                response.put("employeeId", employeeId);
                response.put("department", department);
                response.put("sessionId", sessionId);
                response.put("redirectUrl", "/dashboard");
                response.put("permissions", customerService.getEmployeePermissions(department));
                
                logger.info("Employee authentication successful - ID: {} Department: {} Session: {}", 
                           employeeId, department, sessionId);
                
            } else {
                response.put("status", "failed");
                response.put("message", "Invalid credentials or unauthorized department access");
                response.put("timestamp", LocalDateTime.now().format(DateTimeFormatter.ISO_LOCAL_DATE_TIME));
                
                // Log de seguridad para intentos fallidos
                logger.warn("Authentication failed for employee: {} from IP: {} Department: {}", 
                           employeeId, getClientIP(request), department);
            }
            
        } catch (Exception e) {
            logger.error("Authentication system error: {}", e.getMessage(), e);
            response.put("status", "error");
            response.put("message", "System temporarily unavailable");
        }
        
        return ResponseEntity.ok(response);
    }
    
    @GetMapping("/customers/search")
    public ResponseEntity<Map<String, Object>> searchCustomers(
            @RequestParam(required = false) String query,
            @RequestParam(required = false) String category,
            @RequestParam(required = false) String region,
            HttpServletRequest request) {
        
        Map<String, Object> response = new HashMap<>();
        
        try {
            String clientIP = getClientIP(request);
            
            // Log de búsqueda para análisis de negocio y auditoría
            String searchTerm = (query != null && !query.trim().isEmpty()) ? query : "none";
            logger.info("Customer search performed - Query: '{}' Category: {} Region: {} IP: {} UserAgent: {}", 
                       searchTerm, category, region, clientIP, request.getHeader("User-Agent"));
            
            List<Map<String, Object>> customers = customerService.searchCustomers(query, category, region);
            
            response.put("status", "success");
            response.put("searchQuery", query != null ? query : "none");
            response.put("category", category);
            response.put("region", region);
            response.put("results", customers);
            response.put("totalResults", customers.size());
            response.put("searchTimestamp", LocalDateTime.now().format(DateTimeFormatter.ISO_LOCAL_DATE_TIME));
            
        } catch (Exception e) {
            logger.error("Customer search error - Query: {} Error: {}", query, e.getMessage());
            response.put("status", "error");
            response.put("message", "Search service temporarily unavailable");
        }
        
        return ResponseEntity.ok(response);
    }
    
    @PostMapping("/customers/feedback")
    public ResponseEntity<Map<String, Object>> submitFeedback(
            @RequestBody Map<String, Object> feedbackData,
            HttpServletRequest request) {
        
        Map<String, Object> response = new HashMap<>();
        
        try {
            String clientIP = getClientIP(request);
            String customerId = (String) feedbackData.get("customerId");
            String feedbackText = (String) feedbackData.get("feedback");
            String category = (String) feedbackData.get("category");
            
            // Log detallado para análisis de satisfacción del cliente
            logger.info("Customer feedback received - CustomerID: {} Category: {} IP: {} Feedback: {}", 
                       customerId, category, clientIP, feedbackText);
            
            // Procesamiento del feedback
            String ticketId = customerService.processFeedback(customerId, feedbackText, category);
            
            response.put("status", "submitted");
            response.put("ticketId", ticketId);
            response.put("customerId", customerId);
            response.put("category", category);
            response.put("estimatedResponse", "24-48 hours");
            response.put("submissionTime", LocalDateTime.now().format(DateTimeFormatter.ISO_LOCAL_DATE_TIME));
            
        } catch (Exception e) {
            logger.error("Feedback submission error: {}", e.getMessage());
            response.put("status", "error");
            response.put("message", "Unable to process feedback at this time");
        }
        
        return ResponseEntity.ok(response);
    }
    
    @GetMapping("/reports/dashboard")
    public ResponseEntity<Map<String, Object>> getAnalyticsDashboard(
            @RequestParam(required = false) String dateRange,
            @RequestParam(required = false) String metrics,
            @RequestParam(required = false) String filters,
            HttpServletRequest request) {
        
        Map<String, Object> response = new HashMap<>();
        
        try {
            String userAgent = request.getHeader("User-Agent");
            String sessionId = request.getSession().getId();
            
            // Log de acceso a reportes para auditoría de business intelligence
            logger.info("Analytics dashboard accessed - DateRange: {} Metrics: {} Filters: {} Session: {} UserAgent: {}", 
                       dateRange, metrics, filters, sessionId, userAgent);
            
            Map<String, Object> dashboardData = customerService.generateDashboardData(dateRange, metrics, filters);
            
            response.put("status", "loaded");
            response.put("dateRange", dateRange);
            response.put("requestedMetrics", metrics);
            response.put("appliedFilters", filters);
            response.put("dashboardData", dashboardData);
            response.put("generatedAt", LocalDateTime.now().format(DateTimeFormatter.ISO_LOCAL_DATE_TIME));
            response.put("cacheStatus", "fresh");
            
        } catch (Exception e) {
            logger.error("Dashboard generation error - Filters: {} Error: {}", filters, e.getMessage());
            response.put("status", "error");
            response.put("message", "Dashboard temporarily unavailable");
        }
        
        return ResponseEntity.ok(response);
    }
    
    @GetMapping("/api/health")
    public ResponseEntity<Map<String, Object>> systemHealth(HttpServletRequest request) {
        Map<String, Object> response = new HashMap<>();
        
        try {
            String diagnosticId = UUID.randomUUID().toString();
            Map<String, String> systemInfo = new HashMap<>();
            systemInfo.put("version", "2.1.3");
            systemInfo.put("build", "2023.12.RELEASE");
            systemInfo.put("java.version", System.getProperty("java.version"));
            systemInfo.put("memory.used", String.valueOf(Runtime.getRuntime().totalMemory() - Runtime.getRuntime().freeMemory()));
            
            // Log de diagnóstico del sistema
            logger.info("System health check performed - DiagnosticID: {} IP: {} SystemInfo: {}", 
                       diagnosticId, getClientIP(request), systemInfo);
            
            Map<String, Object> services = new HashMap<>();
            services.put("database", "operational");
            services.put("cache", "operational");
            services.put("messaging", "operational");
            services.put("external-apis", "operational");
            
            response.put("status", "healthy");
            response.put("diagnosticId", diagnosticId);
            response.put("timestamp", LocalDateTime.now().format(DateTimeFormatter.ISO_LOCAL_DATE_TIME));
            response.put("systemInfo", systemInfo);
            response.put("services", services);
            
        } catch (Exception e) {
            logger.error("Health check error: {}", e.getMessage());
            response.put("status", "degraded");
            response.put("error", e.getMessage());
        }
        
        return ResponseEntity.ok(response);
    }
    
    private String getClientIP(HttpServletRequest request) {
        String xForwardedFor = request.getHeader("X-Forwarded-For");
        if (xForwardedFor != null && !xForwardedFor.isEmpty()) {
            return xForwardedFor.split(",")[0].trim();
        }
        
        String xRealIP = request.getHeader("X-Real-IP");
        if (xRealIP != null && !xRealIP.isEmpty()) {
            return xRealIP;
        }
        
        return request.getRemoteAddr();
    }
}

@Service
class CustomerService {
    
    private static final Logger logger = LogManager.getLogger(CustomerService.class);
    
    public boolean validateEmployee(String employeeId, String password, String department) {
        // Simulación de validación real
        return "EMP001".equals(employeeId) && "Demo123".equals(password);
    }
    
    public List<String> getEmployeePermissions(String department) {
        Map<String, List<String>> permissions = new HashMap<>();
        permissions.put("sales", Arrays.asList("customer.read", "customer.create", "reports.sales"));
        permissions.put("marketing", Arrays.asList("customer.read", "campaigns.manage", "reports.marketing"));
        permissions.put("support", Arrays.asList("customer.read", "customer.update", "tickets.manage"));
        permissions.put("it", Arrays.asList("system.admin", "logs.read", "health.check"));
        
        return permissions.getOrDefault(department, Arrays.asList("basic.access"));
    }
    
    public List<Map<String, Object>> searchCustomers(String query, String category, String region) {
        // Simulación de resultados de búsqueda
        List<Map<String, Object>> results = new ArrayList<>();
        for (int i = 1; i <= 5; i++) {
            Map<String, Object> customer = new HashMap<>();
            customer.put("id", "CUST" + String.format("%03d", i));
            customer.put("name", "Customer " + i);
            customer.put("email", "customer" + i + "@example.com");
            customer.put("category", category != null ? category : "standard");
            customer.put("region", region != null ? region : "north");
            results.add(customer);
        }
        return results;
    }
    
    public String processFeedback(String customerId, String feedback, String category) {
        return "TKT-" + System.currentTimeMillis();
    }
    
    public Map<String, Object> generateDashboardData(String dateRange, String metrics, String filters) {
        Map<String, Object> data = new HashMap<>();
        data.put("totalCustomers", 1247);
        data.put("activeCampaigns", 8);
        data.put("revenue", "$125,430");
        data.put("conversionRate", "3.2%");
        return data;
    }
}

@Service
class AuditService {
    
    private static final Logger logger = LogManager.getLogger(AuditService.class);
    
    public void logEmployeeAccess(String employeeId, String department, String clientIP) {
        // Este log es vulnerable - registra datos de entrada sin sanitización
        logger.info("Employee access audit - ID: {} Department: {} Source: {} Timestamp: {}", 
                   employeeId, department, clientIP, LocalDateTime.now());
    }
}