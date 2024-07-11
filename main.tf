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
  name     = "Azure"
  location = "westeurope"
}

resource "azurerm_service_plan" "TP_Azure" {
  name                = "TP_Azure"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = "westeurope"
  os_type             = "Linux"
  sku_name            = "F1"
}

resource "azurerm_linux_web_app" "linux-azure" {
  name                = "tp-azure-ja"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = "westeurope"
  service_plan_id     = azurerm_service_plan.TP_Azure.id

  site_config {
    application_stack {
      php_version = "8.2"
    }
    always_on = false
  }
}

resource "azurerm_app_service_source_control" "source_control" {
  app_id                 = azurerm_linux_web_app.linux-azure.id
  repo_url               = "https://github.com/JorisPV/azure-webapp"
  branch                 = "main"
  use_manual_integration = true
}

resource "azurerm_source_control_token" "source_control_token" {
  type         = "GitHub"
  token        = var.github_auth_token
  token_secret = var.github_auth_token
}
