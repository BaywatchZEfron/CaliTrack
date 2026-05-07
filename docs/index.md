# CaliTrack

Plataforma web de análisis y seguimiento de progresión en calistenia.

## ¿Qué es?

CaliTrack permite a atletas de calistenia registrar sus entrenamientos, visualizar su progresión por ejercicio y analizar métricas de rendimiento en un dashboard interactivo.

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Frontend | Vue 3 + TypeScript + Vite |
| Backend | Laravel 11 + PHP 8.3 |
| Base de datos | MySQL 8.0 |
| Autenticación | Laravel Sanctum |
| Despliegue | Docker + Nginx |

## Funcionalidades principales

- Registro de entrenamientos con series, reps, peso y RPE
- Dashboard con KPIs calculados desde la base de datos
- Progresión por ejercicio con gráficas
- Sistema de planes Free/Premium con facturación
- Autenticación completa con tokens Sanctum