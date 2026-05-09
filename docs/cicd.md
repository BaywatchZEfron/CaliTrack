# CI/CD y GitHub Pages

CaliTrack usa **GitHub Actions** para automatizar el despliegue de la documentación. Cada vez que se hace un push a `main`, el pipeline construye la documentación con MkDocs y la publica automáticamente en **GitHub Pages**.

---

## Qué problema resuelve

Sin CI/CD, publicar la documentación requiere ejecutar manualmente `mkdocs build` y subir los archivos generados. Con el pipeline, basta con hacer `git push`: GitHub se encarga del resto en menos de 60 segundos.

---

## Estructura del pipeline

```
git push a main
    ↓
GitHub Actions detecta el push
    ↓
Lanza el job en un runner ubuntu-latest
    ↓
1. Clona el repositorio (checkout)
2. Instala Python 3.11
3. Instala mkdocs + mkdocs-material
4. Ejecuta: mkdocs build --strict  →  genera /site
5. Sube /site como artefacto de Pages
6. Despliega el artefacto en GitHub Pages
    ↓
Documentación accesible en:
https://BaywatchZEfron.github.io/CaliTrack/
```

---

## Archivo del workflow

Ubicación en el repositorio: `.github/workflows/deploy-docs.yml`

```yaml
name: Deploy MkDocs to GitHub Pages

on:
  push:
    branches:
      - main          # Dispara el pipeline en cada push a main
  workflow_dispatch:  # Permite lanzarlo manualmente desde GitHub UI

permissions:
  contents: read
  pages: write        # Necesario para escribir en GitHub Pages
  id-token: write     # Necesario para autenticación con el servicio de Pages

concurrency:
  group: "pages"
  cancel-in-progress: false  # Si hay dos deploys en cola, no cancela el anterior

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout código
        uses: actions/checkout@v4
        # Clona el repositorio completo en el runner

      - name: Configurar Python
        uses: actions/setup-python@v5
        with:
          python-version: '3.11'

      - name: Instalar MkDocs y tema Material
        run: |
          pip install mkdocs mkdocs-material
        # Instala exactamente las mismas dependencias que en local

      - name: Build de la documentación
        run: python -m mkdocs build --strict
        # Genera HTML estático en la carpeta /site
        # --strict: si hay un enlace roto o archivo faltante, el build falla
        # y no se despliega una versión rota

      - name: Subir artefacto a GitHub Pages
        uses: actions/upload-pages-artifact@v3
        with:
          path: ./site
        # Empaqueta /site para que el siguiente paso lo despliegue

      - name: Desplegar en GitHub Pages
        id: deployment
        uses: actions/deploy-pages@v4
        # Publica el artefacto en la URL de GitHub Pages del repositorio
```

---

## Configuración necesaria en GitHub (una sola vez)

### 1. Activar GitHub Pages con Actions como fuente

En el repositorio de GitHub:

1. Ve a **Settings → Pages**
2. En _Source_, selecciona **GitHub Actions**
3. Guarda

> Si dejas _Source_ en "Deploy from a branch", el workflow fallará porque intentará escribir en un lugar incorrecto.

### 2. Verificar permisos del workflow

En **Settings → Actions → General → Workflow permissions**:

- Selecciona **Read and write permissions**
- Marca **Allow GitHub Actions to create and approve pull requests**
- Guarda

---

## Cómo activarlo en el repositorio

```bash
# En la raíz del proyecto, crea la carpeta del workflow
mkdir -p .github/workflows

# Coloca el archivo deploy-docs.yml ahí
# (el archivo está en la raíz de este repositorio como referencia)

# Añade, commitea y sube
git add .github/workflows/deploy-docs.yml
git commit -m "ci: añadir workflow de despliegue de documentación en GitHub Pages"
git push origin main
```

El pipeline arranca automáticamente. Se puede seguir en tiempo real en la pestaña **Actions** del repositorio.

---

## Verificar que el despliegue funcionó

1. Ve a la pestaña **Actions** del repositorio
2. Busca el workflow _Deploy MkDocs to GitHub Pages_
3. Haz clic en la ejecución más reciente
4. Si todos los pasos tienen ✅ verde, la documentación está publicada

La URL final es:
```
https://BaywatchZEfron.github.io/CaliTrack/
```

---

## Qué pasa si el build falla

El flag `--strict` hace que el build falle si hay problemas en la documentación:

| Error común | Causa | Solución |
|---|---|---|
| `WARNING - Doc file not found` | Un archivo referenciado en `mkdocs.yml` no existe en `docs/` | Crear el archivo que falta |
| `WARNING - Broken link` | Un enlace interno apunta a un archivo que no existe | Corregir la ruta del enlace |
| Permiso denegado | Pages no está configurado como fuente Actions | Revisar Settings → Pages |

En todos los casos, el pipeline no despliega nada hasta que el build sea limpio. Esto evita publicar documentación rota.

---

## `mkdocs.yml` — configuración completa

```yaml
site_name: CaliTrack Docs
site_url: https://BaywatchZEfron.github.io/CaliTrack/
repo_url: https://github.com/BaywatchZEfron/CaliTrack
repo_name: BaywatchZEfron/CaliTrack

theme:
  name: material
  language: es
  palette:
    scheme: slate          # Tema oscuro, acorde al diseño de la app
    primary: indigo
    accent: indigo
  features:
    - navigation.tabs
    - navigation.sections
    - toc.integrate
    - content.code.copy    # Botón de copiar en bloques de código

nav:
  - Inicio: index.md
  - Arquitectura: arquitectura.md
  - Frontend: frontend.md
  - Backend: backend.md
  - Despliegue Docker: despliegue.md
  - CI/CD y GitHub Pages: cicd.md
  - Planes y facturación: ssg.md
  - API: api.md
```