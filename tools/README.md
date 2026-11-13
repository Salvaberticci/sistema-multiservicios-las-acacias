# Herramientas

## Tailwind CSS standalone CLI

El binario `tailwindcss.exe` no se versiona porque pesa ~111 MB y supera el límite de archivo de GitHub.

Para recompilar el CSS:

1. Descargar `tailwindcss-windows-x64.exe` desde [Tailwind CSS Releases](https://github.com/tailwindlabs/tailwindcss/releases/latest).
2. Guardarlo como `tools/tailwindcss.exe`.
3. Ejecutar desde la raíz del proyecto:

```powershell
.\tools\tailwindcss.exe -i resources\css\app.css -o public\css\app.css --minify
```

En bash:

```bash
./tools/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify
```

El CSS compilado (`public/css/app.css`) sí se versiona para que el proyecto funcione sin Node.js.
