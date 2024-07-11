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

resource "azurerm_resource_group" "webapp-rg" {
  name     = "webapp-rg"
  location = "westeurope"
}

resource "azurerm_service_plan" "appservice-plan" {
  name                = "appservice-plan"
  resource_group_name = azurerm_resource_group.webapp-rg.name
  location            = "westeurope"
  os_type             = "Linux"
  sku_name            = "F1"
}

resource "azurerm_linux_web_app" "linux-web-app" {
  name                = "tp-azure-joris-ameline"
  resource_group_name = azurerm_resource_group.webapp-rg.name
  location            = "westeurope"
  service_plan_id     = azurerm_service_plan.appservice-plan.id

  site_config {
    application_stack {
      php_version = "8.2" # Change to appropriate application and version
    }
    always_on = false  # Explicitly set always_on to false
  }
}

resource "azurerm_app_service_source_control" "source_control" {
  app_id                 = azurerm_linux_web_app.linux-web-app.id
  repo_url               = "https://github.com/JorisPV/azure-webapp"
  branch                 = "main"
  use_manual_integration = true
}

resource "azurerm_source_control_token" "source_control_token" {
  type         = "GitHub"
  token        = var.github_auth_token
  token_secret = var.github_auth_token
}
