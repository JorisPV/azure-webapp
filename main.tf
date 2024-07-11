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

resource "azurerm_key_vault" "keyvault" {
  name                        = "tp-azure-keyvault"
  location                    = azurerm_resource_group.Azure.location
  resource_group_name         = azurerm_resource_group.Azure.name
  enabled_for_disk_encryption = true
  enabled_for_template_deployment = true
  sku_name = "standard"
}

resource "azurerm_key_vault_secret" "github_token" {
  name         = "github-token"
  value        = var.github_auth_token
  key_vault_id = azurerm_key_vault.example.id
}


resource "azurerm_service_plan" "TP_Azure" {
  name                = "TP_Azure"
  resource_group_name = azurerm_resource_group.Azure.name
  location            = "westeurope"
  os_type             = "Linux"
  sku_name            = "B1"
}

resource "azurerm_linux_web_app" "linux-azure" {
  name                = "tp-azure-jpab"
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

resource "azurerm_source_control_token" "token_source" {
  type         = "GitHub"
  token        = var.github_auth_token
  token_secret = var.github_auth_token
}
