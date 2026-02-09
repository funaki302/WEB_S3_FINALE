#!/bin/bash

echo "⏹️ Arrêt de Apache2..."
sudo systemctl stop apache2

echo "🚀 Lancement de XAMPP..."
sudo /opt/lampp/manager-linux-x64.run
