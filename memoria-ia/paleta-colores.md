# Paleta de colores

> Fuente: `logo.jpeg` (Multiservicios Las Acacias). Valores hexadecimales muestreados directamente del logo.
> Los tres colores corporativos son **azul**, **blanco** y **rojo**.

## Colores corporativos

| Rol | Nombre | Hex | Uso sugerido |
|---|---|---|---|
| **Azul principal** | Azul corporativo | `#263F9B` | Botones primarios, encabezados, enlaces, elementos de marca |
| **Azul claro (acento)** | Cian del logo | `#12B2EC` | Degradados del "ACACIAS", acentos, hover, íconos |
| **Rojo principal** | Rojo corporativo | `#DD2024` | Acciones destructivas/urgentes, énfasis, alertas, CTA secundario |
| **Blanco** | Fondo | `#FFFFFF` | Fondo general, superficies, texto sobre fondos oscuros |

## Variaciones (escalas)

### Azul
| Variante | Hex | Uso |
|---|---|---|
| Azul oscuro | `#1B2E73` | Hover/pressed, textos de marca sobre blanco |
| Azul principal | `#263F9B` | Base de marca |
| Azul claro | `#12B2EC` | Acentos, degradados, estados activos |
| Azul muy claro | `#E7F6FD` | Fondos de sección, hover suave |

### Rojo
| Variante | Hex | Uso |
|---|---|---|
| Rojo oscuro | `#A8161A` | Hover/pressed, texto de error |
| Rojo principal | `#DD2024` | Base de marca, alertas |
| Rojo claro | `#F26366` | Fondos suaves, badges |
| Rojo muy claro | `#FCE8E8` | Fondos de alerta, hover |

### Neutros (complementarios para UI)
| Variante | Hex | Uso |
|---|---|---|
| Negro (del logo) | `#020202` | Bordes/contornos, texto de máximo contraste |
| Gris 900 | `#1A1A1A` | Texto principal |
| Gris 700 | `#404040` | Texto secundario |
| Gris 300 | `#CCCCCC` | Bordes, separadores |
| Gris 100 | `#F5F5F5` | Fondos de tarjetas, hover |
| Gris 50 | `#FAFAFA` | Fondo general de la aplicación |

## Reglas de uso

1. Los tres colores del logo (azul, blanco, rojo) son **obligatorios** en la identidad visual del sistema.
2. El **blanco** es el fondo principal; **azul** y **rojo** son acentos. Evitar bloques grandes de rojo (reservarlo para acciones/alertas).
3. El azul principal `#263F9B` es el color de marca dominante; el cian `#12B2EC` se usa para degradados y acentos, no como texto sobre blanco (bajo contraste).
4. Los neutros (grises) son los únicos colores fuera de marca permitidos.
5. No introducir colores adicionales sin justificación de negocio.

## Implementación (Tailwind 4)

En `resources/css/app.css`, definir el tema con `@theme` (Tailwind 4 es CSS-first, **no** crear `tailwind.config.js`):

```css
@theme {
  /* Marca */
  --color-brand-blue: #263F9B;
  --color-brand-blue-dark: #1B2E73;
  --color-brand-blue-light: #12B2EC;
  --color-brand-blue-soft: #E7F6FD;
  --color-brand-red: #DD2024;
  --color-brand-red-dark: #A8161A;
  --color-brand-red-light: #F26366;
  --color-brand-red-soft: #FCE8E8;

  /* Neutros */
  --color-ink: #020202;
  --color-gray-900: #1A1A1A;
  --color-gray-700: #404040;
  --color-gray-300: #CCCCCC;
  --color-gray-100: #F5F5F5;
  --color-gray-50: #FAFAFA;
}
```

## Pendiente

- [ ] Obtener el logo en formato vectorial (AI/SVG) para confirmar colores exactos y degradados.
- [ ] Definir fuentes tipográficas (display para "MULTISERVICIOS/ACACIAS", sans-serif para UI).
- [ ] Definir estilos de botones (primario = azul, peligro/CTA = rojo).