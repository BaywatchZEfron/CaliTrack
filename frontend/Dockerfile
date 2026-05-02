# ── ETAPA 1: compilar Vue ────────────────────────────────────────────────────
FROM node:20-alpine AS builder
# node:20-alpine → imagen ligera de Node 20 para compilar

WORKDIR /app
# Establece /app como directorio de trabajo dentro del contenedor

COPY package*.json ./
# Copia solo los archivos de dependencias primero
# Así Docker cachea esta capa y no reinstala si no cambian las deps

RUN npm ci
# npm ci → instalación limpia y reproducible (mejor que npm install en CI)

COPY . .
# Copia el resto del código fuente

RUN npm run build
# Compila Vue → genera la carpeta dist/ con HTML, CSS y JS estáticos

# ── ETAPA 2: servir con NGINX ────────────────────────────────────────────────
FROM nginx:alpine
# Imagen limpia de NGINX — no incluye Node, mucho más pequeña

COPY --from=builder /app/dist /usr/share/nginx/html
# Copia solo los archivos compilados de la etapa anterior
# El código fuente y Node.js no van al contenedor final

COPY nginx.conf /etc/nginx/conf.d/default.conf
# Configuración de NGINX para Vue Router (SPA)

EXPOSE 80
