#!/bin/bash
# ============================================
# Estrich Digital – Sicheres Deploy-Script
# ============================================
# Lädt Code-Dateien auf den Server hoch,
# OHNE die online bearbeiteten Daten zu überschreiben.
#
# Verwendung: ./deploy.sh
# ============================================

SERVER="ubuntu@164.30.2.145"
KEY="/Users/dominikdiener/Documents/Estrich-Messung/Software/OTC Server/key-thingsboard.pem"
LOCAL_DIR="/Users/dominikdiener/Documents/Estrich-Messung/Marketing/Homepage/"
REMOTE_DIR="~/homepage/Homepage/"

echo "🚀 Deploy startet..."
echo ""

# Schritt 1: Nur Code hochladen (NICHT data/)
echo "📦 Lade Code-Dateien hoch (data/ wird übersprungen)..."
rsync -avz \
    --exclude='.git' \
    --exclude='.claude' \
    --exclude='data/' \
    --exclude='msmtprc' \
    --exclude='deploy.sh' \
    -e "ssh -i \"$KEY\"" \
    "$LOCAL_DIR" "$SERVER:$REMOTE_DIR"

echo ""

# Schritt 2: Container neu bauen
echo "🐳 Baue Docker-Container neu..."
ssh -i "$KEY" "$SERVER" "cd $REMOTE_DIR && docker compose up -d --build --remove-orphans"

echo ""

# Schritt 3: Berechtigungen setzen
echo "🔑 Setze Berechtigungen..."
ssh -i "$KEY" "$SERVER" "docker exec homepage-web-1 chmod -R 755 /var/www/html/ && docker exec homepage-web-1 chown -R www-data:www-data /var/www/html/data/"

echo ""
echo "✅ Deploy abgeschlossen!"
echo "🌐 Homepage: http://164.30.2.145"
echo "🔧 Admin: http://164.30.2.145/admin/"
