terraform {
  required_providers {
    azurerm = {
      source  = "hashicorp/azurerm"
      version = "3.112.0"
    }
  }
}

data "azurerm_subscription" "current" {}

provider "azurerm" {
  features {}
}

resource "azurerm_resource_group" "Azure" {
  name     = "Azure"
  location = "northeurope"
}

resource "azurerm_service_plan" "TP_Azure" {
  name                = "TP_Azure"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = "northeurope"
  os_type             = "Linux"
  sku_name            = "S1"
}

resource "azurerm_linux_web_app" "linux-azure" {
  name                = "tp-azure-jpab"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = azurerm_service_plan.TP_Azure.location
  service_plan_id     = azurerm_service_plan.TP_Azure.id

  site_config {
    application_stack {
      php_version = "8.2"
    }
    always_on = false
  }

  depends_on = [
    azurerm_service_plan.TP_Azure
  ]
}

resource "azurerm_log_analytics_workspace" "monitoring" {
  name                = "log-analytics-workspace"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = azurerm_resource_group.Azure.location
  sku                 = "PerGB2018"
  retention_in_days   = 30
}

resource "azurerm_monitor_diagnostic_setting" "monitor" {
  name               = "monitor-app"
  target_resource_id = azurerm_linux_web_app.linux-azure.id

  log_analytics_workspace_id = azurerm_log_analytics_workspace.monitoring.id

  enabled_log {
    category = "AppServiceHTTPLogs"
  }

  metric {
    category = "AllMetrics"
    enabled  = true
  }
}

resource "azurerm_storage_account" "storage" {
  name                     = "tpazurejpab"
  resource_group_name      = azurerm_resource_group.Azure.name
  location                 = azurerm_resource_group.Azure.location
  account_tier             = "Standard"
  account_replication_type = "LRS"
  account_kind             = "BlobStorage"
}

resource "azurerm_storage_management_policy" "storage_policy" {
  storage_account_id = azurerm_storage_account.storage.id

  rule {
    name    = "retention-rule"
    enabled = true

    filters {
      blob_types = ["blockBlob"]
    }

    actions {
      base_blob {
        delete_after_days_since_modification_greater_than = 30
      }
    }
  }
}

resource "azurerm_app_service_source_control" "source_control" {
  app_id                 = azurerm_linux_web_app.linux-azure.id
  repo_url               = "https://github.com/JorisPV/azure-webapp"
  branch                 = "main"
  use_manual_integration = true
}

resource "azurerm_source_control_token" "token_source" {
  type         = "GitHub"
  token        = var.github_auth_token
  token_secret = var.github_auth_token
}

output "log_analytics_workspace_url" {
  value = "https://portal.azure.com/#@${data.azurerm_subscription.current.tenant_id}/resource/${azurerm_log_analytics_workspace.monitoring.id}/logs"
}