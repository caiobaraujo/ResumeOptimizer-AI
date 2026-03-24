FROM python:3.11-slim

# Evita .pyc e garante logs imediatos
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

WORKDIR /app

# Instala apenas o mínimo necessário (Playwright cuida do resto)
RUN apt-get update && apt-get install -y \
    wget curl gnupg \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala libs Python
RUN pip install --no-cache-dir \
    flask \
    requests \
    beautifulsoup4 \
    playwright \
    playwright-stealth \
    sentence-transformers \
    pandas

# Instala browsers + dependências automaticamente (FORMA CORRETA)
RUN playwright install --with-deps chromium

# Copia seu código (faltava isso no seu Dockerfile)
COPY . .

# Comando para rodar o Flask
CMD ["python", "app.py"]