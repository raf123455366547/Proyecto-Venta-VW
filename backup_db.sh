#!/usr/bin/env bash

# ==========================================
# Script de Respaldo de Base de Datos - ANCGVW
# ==========================================

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR" || exit 1

ENV_FILE="$PROJECT_DIR/.env"
if [ ! -f "$ENV_FILE" ]; then
    echo " Error: No se encontró el archivo .env en $PROJECT_DIR"
    exit 1
fi

DB_HOST=$(grep -E '^DB_HOST=' "$ENV_FILE" | cut -d '=' -f 2- | tr -d '"' | tr -d "'")
DB_PORT=$(grep -E '^DB_PORT=' "$ENV_FILE" | cut -d '=' -f 2- | tr -d '"' | tr -d "'")
DB_DATABASE=$(grep -E '^DB_DATABASE=' "$ENV_FILE" | cut -d '=' -f 2- | tr -d '"' | tr -d "'")
DB_USERNAME=$(grep -E '^DB_USERNAME=' "$ENV_FILE" | cut -d '=' -f 2- | tr -d '"' | tr -d "'")
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "$ENV_FILE" | cut -d '=' -f 2- | tr -d '"' | tr -d "'")

DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}

if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo " Error: DB_DATABASE o DB_USERNAME no están configurados en el .env"
    exit 1
fi

BACKUP_DIR="$PROJECT_DIR/database/backups"
mkdir -p "$BACKUP_DIR"

if [ ! -f "$BACKUP_DIR/.gitkeep" ]; then
    touch "$BACKUP_DIR/.gitkeep"
fi

GITIGNORE_FILE="$PROJECT_DIR/.gitignore"
if ! grep -q "database/backups/\*" "$GITIGNORE_FILE" 2>/dev/null; then
    echo "" >> "$GITIGNORE_FILE"
    echo "# Ignorar respaldos de base de datos" >> "$GITIGNORE_FILE"
    echo "database/backups/*" >> "$GITIGNORE_FILE"
    echo "!database/backups/.gitkeep" >> "$GITIGNORE_FILE"
fi

TIMESTAMP=$(date +"%Y-%m-%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/backup_${TIMESTAMP}.sql"

echo " Iniciando respaldo de la base de datos '$DB_DATABASE'..."

MYSQL_PWD="$DB_PASSWORD" mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    --single-transaction \
    --quick \
    "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null

if [ $? -eq 0 ] && [ -s "$BACKUP_FILE" ]; then
    echo " Respaldo generado con éxito: $BACKUP_FILE"
else
    echo " Error al generar el respaldo con mysqldump."
    rm -f "$BACKUP_FILE"
    exit 1
fi

echo " Limpiando respaldos antiguos (mayores a 7 días)..."
find "$BACKUP_DIR" -type f -name "backup_*.sql" -mtime +7 -exec rm -f {} \;

read -p " ¿Deseas hacer commit y push a Git de los cambios del proyecto? (s/N): " RESPONSE
if [[ "$RESPONSE" =~ ^[Ss]$ ]]; then
    read -p " Mensaje para el commit: " COMMIT_MSG
    COMMIT_MSG=${COMMIT_MSG:-"Respaldo automático y actualización de código"}
    
    echo " Agregando cambios a Git..."
    git add .
    git commit -m "$COMMIT_MSG"
    
    BRANCH=$(git rev-parse --abbrev-ref HEAD)
    echo " Subiendo cambios a la rama $BRANCH..."
    git push origin "$BRANCH"
    echo " Control de versiones actualizado."
else
    echo "Se omitió la sincronización con Git."
fi

echo " Proceso finalizado correctamente para ANCGVW."

