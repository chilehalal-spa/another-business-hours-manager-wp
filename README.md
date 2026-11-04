# Another Business Hours Manager

Gestor de horarios comercial con detección automática de feriados y widget de estado.

Se usa en: ninguno por ahora.

## Instalación

1. Descarga `another-business-hours-manager.zip` desde la
   [última release](https://github.com/chilehalal-spa/another-business-hours-manager-wp/releases/latest).
   No uses el botón "Code → Download ZIP": ese zip trae otro nombre de carpeta.
2. En WordPress: *Plugins → Añadir nuevo → Subir plugin*, elige el zip y activa.

## Uso

Shortcodes: `[abh_hours]`.

## Actualizaciones

El plugin se actualiza desde las releases de este repositorio, como cualquier
plugin de wordpress.org: aparecen en *Escritorio → Actualizaciones* y se pueden
dejar automáticas en *Plugins → Activar actualizaciones automáticas*.

WordPress revisa cada 12 horas; la respuesta de GitHub se guarda 6 horas.

## Publicar una versión nueva

1. Sube `Version:` en `another-business-hours-manager.php` (por ejemplo `1.2.0`).
2. Haz commit y push.
3. Crea y sube el tag con el mismo número:

   ```bash
   git tag v1.2.0
   git push origin v1.2.0
   ```

La Action [Release](.github/workflows/release.yml) verifica que el tag coincida
con la versión, arma `another-business-hours-manager.zip` y publica la release. Desde ese
momento los sitios ven la actualización.

## Licencia

GPL-2.0-or-later. Ver [LICENSE](LICENSE).
