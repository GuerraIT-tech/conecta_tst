FROM python:3.11-slim

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

WORKDIR /app

# Dependências de sistema (Python/MySQL + utilitários)
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    curl \
    ca-certificates \
    gnupg \
    default-libmysqlclient-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Node.js para build/execução do front-end React (Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Dependências Python do backend Flask
COPY flask_api/requirements.txt /tmp/requirements.txt
RUN pip install --no-cache-dir -r /tmp/requirements.txt

# Dependências do front-end
COPY frontend-react/package.json /app/frontend-react/package.json
RUN cd /app/frontend-react && npm install

# Código da aplicação
COPY . /app

EXPOSE 5000 5173

# Backend padrão (Flask API)
CMD ["python", "flask_api/app.py"]
