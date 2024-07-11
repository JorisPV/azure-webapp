terraform {
  required_providers {
    azurerm = {
      source  = "hashicorp/azurerm"
      version = "3.55.0"
    }
  }
}

provider "azurerm" {
  features {}
}

resource "azurerm_resource_group" "Azure" {
  name     = "TP-Azure-ResourceGroup"
  location = "westeurope"
}

resource "azurerm_key_vault" "keyvault" {
  name                        = "TP-Azure-KeyVault"
  location                    = azurerm_resource_group.Azure.location
  resource_group_name         = azurerm_resource_group.Azure.name
  enabled_for_disk_encryption = true
  enabled_for_template_deployment = true
  sku_name = "standard"
}

resource "azurerm_key_vault_secret" "github_token" {
  name         = "github-token"
  value        = var.github_auth_token
  key_vault_id = azurerm_key_vault.keyvault.id
}

data "azurerm_key_vault_secret" "github_token" {
  name         = azurerm_key_vault_secret.github_token.name
  key_vault_id = azurerm_key_vault_secret.github_token.key_vault_id
}

resource "azurerm_service_plan" "TP_Azure" {
  name                = "TP-Azure-ServicePlan"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = azurerm_resource_group.Azure.location
  os_type             = "Linux"
  sku_name            = "B1"
}

resource "azurerm_linux_web_app" "linux-azure" {
  name                = "TP-Azure-WebApp"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = azurerm_resource_group.Azure.location
  service_plan_id     = azurerm_service_plan.TP_Azure.id

  site_config {
    application_stack {
      php_version = "8.2"
    }
    always_on     = false
    http2_enabled = true
    scm_type      = "LocalGit"
  }

  identity {
    type = "SystemAssigned"
  }
}

resource "azurerm_app_service_source_control" "source_control" {
  app_id                 = azurerm_linux_web_app.linux-azure.id
  repo_url               = "https://github.com/JorisPV/azure-webapp"
  branch                 = "main"
  use_manual_integration = true
  token                  = data.azurerm_key_vault_secret.github_token.value
}

resource "azurerm_application_gateway" "appgw" {
  name                = "TP-Azure-ApplicationGateway"
  location            = azurerm_resource_group.Azure.location
  resource_group_name = azurerm_resource_group.Azure.name

  sku {
    name     = "WAF_v2"
    tier     = "WAF_v2"
    capacity = 2
  }

  gateway_ip_configuration {
    name      = "configuration"
    subnet_id = azurerm_subnet.appgw_subnet.id
  }

  frontend_ip_configuration {
    name                 = "frontendConfiguration"
    public_ip_address_id = azurerm_public_ip.appgw_public_ip.id
  }

  frontend_port {
    name = "frontendPort"
    port = 443
  }

  backend_address_pool {
    name = "backendPool"
  }

  backend_http_settings {
    name                  = "backendHttpSettings"
    cookie_based_affinity = "Disabled"
    port                  = 443
    protocol              = "Https"
    request_timeout       = 20
  }

  http_listener {
    name                           = "listener"
    frontend_ip_configuration_name = "frontendConfiguration"
    frontend_port_name             = "frontendPort"
    protocol                       = "Https"
    ssl_certificate_name           = "exampleSslCert"
  }

  request_routing_rule {
    name                       = "routingRule"
    rule_type                  = "Basic"
    http_listener_name         = "listener"
    backend_address_pool_name  = "backendPool"
    backend_http_settings_name = "backendHttpSettings"
  }

  ssl_certificate {
    name     = "exampleSslCert"
    data     = filebase64("${path.module}/certificate.pfx")
    password = "your-password"
  }

  waf_configuration {
    enabled          = true
    firewall_mode    = "Prevention"
    rule_set_type    = "OWASP"
    rule_set_version = "3.2"
  }
}

resource "azurerm_public_ip" "appgw_public_ip" {
  name                = "TP-Azure-PublicIP"
  location            = azurerm_resource_group.Azure.location
  resource_group_name = azurerm_resource_group.Azure.name
  allocation_method   = "Static"
}

resource "azurerm_virtual_network" "example" {
  name                = "TP-Azure-VirtualNetwork"
  location            = azurerm_resource_group.Azure.location
  resource_group_name = azurerm_resource_group.Azure.name
  address_space       = ["10.0.0.0/16"]
}

resource "azurerm_subnet" "appgw_subnet" {
  name                 = "TP-Azure-Subnet"
  resource_group_name  = azurerm_resource_group.Azure.name
  virtual_network_name = azurerm_virtual_network.example.name
  address_prefixes     = ["10.0.2.0/24"]
}

resource "azurerm_monitor_diagnostic_setting" "example" {
  name                     = "TP-Azure-DiagnosticSetting"
  target_resource_id       = azurerm_linux_web_app.linux-azure.id
  log_analytics_workspace_id = azurerm_log_analytics_workspace.example.id

  log {
    category = "AppServiceHTTPLogs"
    enabled  = true
    retention_policy {
      enabled = true
      days    = 7
    }
  }

  log {
    category = "AppServiceConsoleLogs"
    enabled  = true
    retention_policy {
      enabled = true
      days    = 7
    }
  }

  metric {
    category = "AllMetrics"
    enabled  = true
    retention_policy {
      enabled = true
      days    = 7
    }
  }
}

resource "azurerm_log_analytics_workspace" "example" {
  name                = "TP-Azure-LogAnalyticsWorkspace"
  location            = azurerm_resource_group.Azure.location
  resource_group_name = azurerm_resource_group.Azure.name
  sku                 = "PerGB2018"
  retention_in_days   = 30
}

resource "azurerm_security_center_contact" "example" {
  email               = "security@example.com"
  phone               = "123456789"
  alert_notifications = true
  alerts_to_admins    = true
}

resource "azurerm_security_center_subscription_pricing" "example" {
  tier = "Standard"
}

output "resource_group_name" {
  description = "The name of the resource group"
  value       = azurerm_resource_group.Azure.name
}

output "key_vault_id" {
  description = "The ID of the Key Vault"
  value       = azurerm_key_vault.keyvault.id
}

output "service_plan_id" {
  description = "The ID of the Service Plan"
  value       = azurerm_service_plan.TP_Azure.id
}

output "linux_web_app_url" {
  description = "The URL of the Linux Web App"
  value       = azurerm_linux_web_app.linux-azure.default_site_hostname
}

output "application_gateway_id" {
  description = "The ID of the Application Gateway"
  value       = azurerm_application_gateway.appgw.id
}

output "log_analytics_workspace_id" {
  description = "The ID of the Log Analytics Workspace"
  value       = azurerm_log_analytics_workspace.example.id
}
