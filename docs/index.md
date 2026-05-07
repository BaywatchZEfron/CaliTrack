# CaliTrack — Documentación técnica

## Introducción

CaliTrack es una plataforma web de análisis y seguimiento de progresión en calistenia. Permite a los atletas registrar sus entrenamientos, visualizar su progresión por ejercicio y analizar métricas de rendimiento en un dashboard interactivo.

El proyecto nace de la necesidad de tener una herramienta específica para calistenia — la mayoría de apps de fitness están orientadas a gimnasio con máquinas y pesos, y no contemplan bien la progresión en ejercicios de peso corporal donde la métrica clave son las repeticiones y la técnica, no el peso levantado.

## Objetivos

- Permitir el registro estructurado de entrenamientos con series, repeticiones, carga y RPE
- Calcular métricas de progresión reales por ejercicio semana a semana
- Ofrecer un dashboard con KPIs accionables para el atleta
- Desplegar la aplicación en un entorno completamente containerizado y reproducible

## Entorno de desarrollo

| Componente | Detalle |
|-----------|---------|
| Sistema operativo | Windows 11 |
| Editor | Visual Studio Code |
| Gestor de paquetes PHP | Composer |
| Gestor de paquetes JS | npm |
| Servidor local PHP | Laravel Herd |
| Base de datos local | MySQL 8.0 (puerto 3306) |
| Contenedores | Docker Desktop con backend WSL 2 |
| Control de versiones | Git + GitHub |

## Stack tecnológico

| Capa | Tecnología | Versión |
|------|-----------|---------|
| Frontend | Vue 3 + TypeScript + Vite | Vue 3.5 |
| Estado global | Pinia | 2.x |
| Router | Vue Router | 4.x |
| Gráficas | Chart.js + vue-chartjs | 4.x |
| HTTP client | Axios | 1.x |
| Backend | Laravel | 11.x |
| Lenguaje backend | PHP | 8.3 |
| Autenticación | Laravel Sanctum | 4.x |
| Base de datos | MySQL | 8.0 |
| Servidor web | Nginx | alpine |
| Contenedores | Docker + Docker Compose | 2.x |

## Estructura del repositorio

```
CaliTrack/
├── frontend/          # Aplicación Vue 3
│   ├── src/
│   │   ├── views/     # Páginas de la app
│   │   ├── components/# Componentes reutilizables
│   │   ├── composables/ # Lógica reactiva reutilizable
│   │   ├── stores/    # Estado global Pinia
│   │   ├── services/  # Llamadas a la API
│   │   └── types/     # Tipos TypeScript
│   ├── Dockerfile
│   └── nginx.conf
├── backend/           # API Laravel 11
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── Dockerfile
├── nginx/             # Configuración del proxy
│   └── default.conf
├── docker-compose.yml
└── docs/              # Esta documentación
```