FROM python:3.11-slim

# Instala dependências básicas
RUN apt-get update && apt-get install -y wget gnupg unzip curl \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://dl-ssl.google.com/linux/linux_signing_key.pub | gpg --dearmor -o /etc/apt/keyrings/google-chrome.gpg \
    && echo "deb [arch=amd64 signed-by=/etc/apt/keyrings/google-chrome.gpg] http://dl.google.com/linux/chrome/deb/ stable main" > /etc/apt/sources.list.d/google.list \
    && apt-get update && apt-get install -y google-chrome-stable \
    && apt-get clean

# Instala as bibliotecas de Python
RUN pip install --no-cache-dir flask requests beautifulsoup4 selenium webdriver-manager

WORKDIR /app