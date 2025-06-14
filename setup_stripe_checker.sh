#!/bin/bash

echo "📦 Installing Docker & Docker Compose..."

# Update and install Docker
sudo apt-get update -y
sudo apt-get install -y     ca-certificates     curl     gnupg     lsb-release

sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

echo   "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg]   https://download.docker.com/linux/ubuntu   $(lsb_release -cs) stable" |   sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt-get update -y
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Enable Docker
sudo systemctl enable docker
sudo systemctl start docker

echo "✅ Docker installed."

# Create project directory
mkdir -p ~/stripe-checker
cd ~/stripe-checker

echo "📦 Download and extract your deployment ZIP..."

# If hosted externally, you could use wget/curl. For now, assume file is uploaded manually.
echo "👉 Please upload the ZIP file to your server and extract it here."
echo "Run: unzip stripe_checker_docker_setup.zip -d ~/stripe-checker"
echo "Then run: docker-compose up --build -d"

