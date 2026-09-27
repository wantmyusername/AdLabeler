# AdLabeler — Etiqueta tus banners de AdSense (WordPress)

Plugin de **WordPress** que agrega **automáticamente etiquetas** tipo *"Anuncio"* / *"Advertising"* a los banners de **Google AdSense**, para mejorar la **transparencia** (recomendación de AdSense) y la experiencia del usuario.

No toca el código de AdSense: solo inyecta un pequeño `<style>` en el `<head>` que pone un `::before` con el texto elegido sobre los anuncios.

- **Autor:** BunkerLATAM — <https://bunkerlatam.gumroad.com>
- **Licencia:** GPLv3
- **Requiere:** WordPress 5.3+ · PHP 7.4+

## Características

- **Dos modos** de etiquetado (ver [Funcionamiento](#funcionamiento)):
  - **Auto + Manual (global)** → etiqueta **todos** los bloques de AdSense.
  - **Solo automáticos** → etiqueta **únicamente** los anuncios automáticos.
- **Texto de etiqueta personalizable** (por defecto `Anuncio`).
- **Aviso en el admin** si el plugin no está configurado.
- **Ligero**: sin JS ni dependencias externas; solo CSS.
- **i18n**: `text domain: adlabeler` + traducción **es_ES**.
- Las opciones se guardan en `wp_options` y se **borran al desactivar**.

## Instalación

1. Sube la carpeta `adlabeler/` a `/wp-content/plugins/`, **o** sube `adlabeler.zip` desde *Plugins → Añadir nuevo → Subir plugin*.
2. **Actívalo** en el menú *Plugins*.
3. Configúralo en **Ajustes → AdLabeler**.

## Cómo se usa

En **Ajustes → AdLabeler** hay dos opciones, cada una con su casilla y su texto:

| Opción | Selector CSS | Alcance |
|---|---|---|
| **Auto + Manual (global)** | `.adsbygoogle::before` | Todos los bloques de AdSense (manuales y automáticos). |
| **Solo automáticos** | `.google-auto-placed::before` | Solo los anuncios automáticos de AdSense. |

Además, al activar el modo global, se ocultan los anuncios sin rellenar:

```css
ins.adsbygoogle[data-ad-status="unfilled"] { display: none !important; }
```

## Estructura

```
adlabeler/
  index.php                     El plugin (todo el código)
  readme.txt                    Readme en formato WordPress (metadatos, FAQ, changelog)
  languages/
    adlabeler-es_ES.po          Traducción al español
adlabeler.zip                   ZIP listo para instalar/subir a WordPress
```

## Funcionamiento

El plugin engancha `wp_head` (`adlabeler_add_code_to_head`) y escribe un `<style>` con, según la configuración:

```css
/* Modo global */
.adsbygoogle { margin-bottom: 50px; }
.adsbygoogle::before { content: "Anuncio"; display: block; text-align: center; font-weight: bold; margin-bottom: 10px; }
ins.adsbygoogle[data-ad-status="unfilled"] { display: none !important; }

/* Solo automáticos */
.google-auto-placed { margin-bottom: 50px; }
.adsbygoogle.adsbygoogle-noablate::before { content: "Anuncio"; /* ... */ }
```

- El texto se escapa con `esc_attr()` antes de meterlo en el CSS.
- La página de ajustes está protegida con **nonce** (`wp_nonce_field` / `wp_verify_nonce`) y usa las APIs de opciones de WordPress.

## Requisitos

- **WordPress 5.3+**
- **PHP 7.4+**

## Licencia

**GPLv3** — ver `adlabeler/readme.txt` (`License: GPLv3`). Autor original: **BunkerLATAM**.

## Notas

- La etiqueta es **solo visual** (`::before` con `content`): no modifica ni altera la entrega del anuncio.
- Los selectores dependen de las clases que pone AdSense (`.adsbygoogle`, `.google-auto-placed`); si Google cambia el marcado, habría que actualizarlos.
- En la interfaz, las descripciones de las dos opciones pueden parecer **intercambiadas**; lo que manda es el selector CSS que usa cada una (tabla de arriba).
