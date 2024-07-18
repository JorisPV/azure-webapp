# TP - Azure WebApp 

Présentation oral : 18/07

# Présentation du Schéma

![Schema](https://acenox.fr/projet/esgi/schema.png)

# Analyse budgétaire du projet

https://azure.com/e/c9559134339742c18bb1ecbecdf75933
Coût : 92,77€

# Déployer l'infrastructure

### Etape 1 : Créer une VM Ubuntu

### Etape 2 : Installer Terraform

```
sudo apt-get update && sudo apt-get install -y gnupg software-properties-common
wget -O- https://apt.releases.hashicorp.com/gpg | \
gpg --dearmor | \
sudo tee /usr/share/keyrings/hashicorp-archive-keyring.gpg > /dev/null
echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] \
https://apt.releases.hashicorp.com $(lsb_release -cs) main" | \
sudo tee /etc/apt/sources.list.d/hashicorp.list
sudo apt update
sudo apt-get install terraform
```

### Etape 3 : Installer Azure CLI

- curl -sL https://aka.ms/InstallAzureCLIDeb | sudo bash

### Etape 4 : Créer un utilisateur RBAC

- az ad sp create-for-rbac --name "Azure" --role contributor --scopes /subscriptions/c8cdc2f3-9a36-403d-8d8f-2eb1cd887422

/!\ Il faut changer la subscription trouvable ici : https://portal.azure.com/#view/Microsoft_Azure_Billing/SubscriptionsBladeV1 /!\

### Etape 5 : Clôner le répertoire sur Github

### Etape 6 : Créer un token Github

- Créer un token ici : https://github.com/settings/tokens
- Créer un fichier variables.tf avec le token généré
variable "github_auth_token" {
  type = string
  default = "id"
}
- Clôner le répertoire sur la machine Ubuntu (git clone <url de votre répo>)

### Etape 7 : Installer un Runner

- Installer le runner --> se rendre dans "Settings" du répertoire
- Se rendre dans "Actions" puis "Runners"
- Ajouter un Runners

### Modifier .github/workflows/azure.yml

Changer le dossier répertoire

```
  update-files:
    runs-on: self-hosted
    needs: login-azure

    steps:
      - name: Supprimer le fichier terraform
        run: |
          if [ -f /home/joris/azure/terraform/main.tf ]; then
            rm /home/joris/azure/terraform/main.tf
          fi

      - name: Télécharger main.tf
        run: |
          curl -o /home/joris/azure/terraform/main.tf https://raw.githubusercontent.com/JorisPV/azure-webapp/main/main.tf
          if [ $? -ne 0 ]; then
            echo "Échec du téléchargement de main.tf"
            exit 1
          fi
          chmod 755 /home/joris/azure/terraform/main.tf

  init-terraform:
    runs-on: self-hosted
    needs: update-files

    outputs:
      plan-has-changes: ${{ steps.plan.outputs.changes }}

    steps:
      - name: Init Terraform
        working-directory: /home/joris/azure/terraform
        run: terraform init

      - name: Validation du fichier Terraform
        working-directory: /home/joris/azure/terraform
        run: terraform validate

      - name: Plan Terraform
        working-directory: /home/joris/azure/terraform
        run: terraform plan

      - name: Apply Terraform
        working-directory: /home/joris/azure/terraform
        run: terraform apply -auto-approve

```

  Changer /home/joris/azure/terraform par votre répertoire où vous avez votre main.tf & votre variables.tf

  ### Etape 9 : Déployer l'application

  - Update un fichier sur github ou réaliser un terraform apply sur la machine ubuntu.
