package com.techrepair.model;

import java.io.Serializable;
import java.util.Date;

public class RepairRequest implements Serializable {
    private static final long serialVersionUID = 1L;
    
    private String id;
    private String customerName;
    private String customerEmail;
    private String deviceType;
    private String deviceBrand;
    private String deviceModel;
    private String problemDescription;
    private String priority;
    private String status;
    private Date createdDate;
    private Double estimatedCost;
    
    public RepairRequest() {
        this.createdDate = new Date();
        this.status = "PENDING";
    }
    
    public RepairRequest(String customerName, String customerEmail, String deviceType, 
                        String deviceBrand, String deviceModel, String problemDescription) {
        this();
        this.customerName = customerName;
        this.customerEmail = customerEmail;
        this.deviceType = deviceType;
        this.deviceBrand = deviceBrand;
        this.deviceModel = deviceModel;
        this.problemDescription = problemDescription;
    }
    
    // Getters and Setters
    public String getId() { return id; }
    public void setId(String id) { this.id = id; }
    
    public String getCustomerName() { return customerName; }
    public void setCustomerName(String customerName) { this.customerName = customerName; }
    
    public String getCustomerEmail() { return customerEmail; }
    public void setCustomerEmail(String customerEmail) { this.customerEmail = customerEmail; }
    
    public String getDeviceType() { return deviceType; }
    public void setDeviceType(String deviceType) { this.deviceType = deviceType; }
    
    public String getDeviceBrand() { return deviceBrand; }
    public void setDeviceBrand(String deviceBrand) { this.deviceBrand = deviceBrand; }
    
    public String getDeviceModel() { return deviceModel; }
    public void setDeviceModel(String deviceModel) { this.deviceModel = deviceModel; }
    
    public String getProblemDescription() { return problemDescription; }
    public void setProblemDescription(String problemDescription) { this.problemDescription = problemDescription; }
    
    public String getPriority() { return priority; }
    public void setPriority(String priority) { this.priority = priority; }
    
    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
    
    public Date getCreatedDate() { return createdDate; }
    public void setCreatedDate(Date createdDate) { this.createdDate = createdDate; }
    
    public Double getEstimatedCost() { return estimatedCost; }
    public void setEstimatedCost(Double estimatedCost) { this.estimatedCost = estimatedCost; }
    
    @Override
    public String toString() {
        return "RepairRequest{" +
                "id='" + id + '\'' +
                ", customerName='" + customerName + '\'' +
                ", deviceType='" + deviceType + '\'' +
                ", deviceBrand='" + deviceBrand + '\'' +
                ", status='" + status + '\'' +
                '}';
    }
}