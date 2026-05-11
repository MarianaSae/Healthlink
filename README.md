# 🏥 Healthlink — Catálogo de Médicos
### Ciudad Juárez, Chihuahua · 2025

---

## 📁 Archivos del proyecto

| Archivo | Tipo | Descripción |
|---|---|---|
| `index.php` | PHP + HTML5 | Página principal: carrusel, catálogo, filtros |
| `login.html` | HTML5 | Inicio de sesión con bcrypt |
| `contacto.html` | HTML5 | Formulario EmailJS + redes sociales |
| `geolocalizacion.html` | HTML5 | Mapa Leaflet + geolocalización GPS |
| `estilo.css` | CSS3 | Estilos globales (3 fuentes Google, sin Bootstrap) |
| `api.php` | PHP | API REST JSON (doctores, citas, especialidades) |
| `auth.php` | PHP | Autenticación bcrypt (login/registro/logout) |
| `config.php` | PHP | Conexión MySQL — **EDITA antes de subir** |
| `healthlink.sql` | SQL | Volcado completo de la base de datos |
| `diccionario_datos.html` | HTML | Diccionario de datos |
| `sitemap.html` | HTML | Mapa del sitio |
| `ficha_desarrollador.html` | HTML | Ficha de identificación del desarrollador |

---

## 🚀 INSTRUCCIONES PARA SUBIR A AWARDSPACE

### Paso 1 — Crear base de datos en AwardSpace
1. Entra a **my.awardspace.com** → Login
2. Ve a **"MySQL Databases"**
3. Clic en **"Create Database"**
4. Anota: **nombre BD**, **usuario**, **contraseña** y **host** (suele ser `mysql8.awardspace.info`)
5. Clic en **"phpMyAdmin"** → selecciona tu BD
6. Pestaña **"Importar"** → elige `healthlink.sql` → **"Continuar"**

### Paso 2 — Editar config.php
Abre `config.php` y reemplaza con tus datos:
```php
define('DB_HOST', 'mysql8.awardspace.info'); // o el que te dé AwardSpace
define('DB_USER', 'tu_usuario_aqui');
define('DB_PASS', 'tu_password_aqui');
define('DB_NAME', 'tu_nombre_bd_aqui');
```

### Paso 3 — Subir archivos
1. En AwardSpace panel → **"File Manager"**
2. Navega a la carpeta de tu dominio (`healthlink229753.atwebpages.com`)
3. Sube TODOS los archivos (puedes usar FileZilla FTP o el gestor web)
4. Asegúrate que `index.php` esté en la raíz

### Paso 4 — Verificar
Abre: **https://healthlink229753.atwebpages.com/index.php**

---

## 📊 CONFIGURAR GOOGLE ANALYTICS (G-JDETFCBL00)

El ID **G-JDETFCBL00** ya está integrado en todos los archivos HTML/PHP.

### Verificar que funciona:
1. Abre tu sitio en el navegador
2. En otra pestaña ve a **analytics.google.com**
3. Panel → **"Informes"** → **"Tiempo real"**
4. Deberías ver 1 usuario activo (tú mismo)

### Eventos que ya se rastrean automáticamente:
- `login` — cuando alguien inicia sesión
- `search` — búsquedas de doctores
- `view_item` — ver perfil de doctor
- `generate_lead` — agendar cita / enviar contacto
- `add_to_wishlist` — guardar favorito
- `carousel_interact` — clicks en el carrusel
- `geo_location_used` — usar geolocalización
- `map_select_doctor` — seleccionar doctor en mapa

---

## 📧 EmailJS — Ya configurado

Credenciales integradas en `contacto.html`:
- Service ID: `service_k6t50zs`
- Template ID: `template_4nywtrb`
- Public Key: `aPJLYY7YBgfR5Wwvy`

**Para que funcione el template**, ve a **emailjs.com** → Email Templates → `template_4nywtrb`
y asegúrate que tenga estas variables:
```
{{from_name}} {{from_email}} {{telefono}} {{asunto}} {{message}}
```

---

## 🔑 Credenciales de prueba

| Email | Contraseña | Rol |
|---|---|---|
| `demo@healthlink.mx` | `password` | Paciente |
| `admin@healthlink.mx` | `password` | Admin |

> ⚠️ Cambia estas contraseñas en producción con:
> ```bash
> php -r "echo password_hash('NuevaContraseña', PASSWORD_BCRYPT, ['cost'=>12]);"
> ```
> Luego actualiza el hash en la tabla `usuarios`.

---

## 🎨 Paleta de colores

| Nombre | Hex | Uso |
|---|---|---|
| Azul Petróleo | `#0b4f6c` | Color primario, navbar, botones |
| Petróleo Claro | `#1a7a9a` | Hovers, acentos secundarios |
| Coral Cálido | `#e8634a` | CTA, alertas, dots activos |
| Verde Menta | `#4caf8a` | Badges online, éxito |
| Ámbar | `#f9a825` | Estrellas de rating |
| Crema | `#f7f3ee` | Fondo general |
| Oscuro | `#0e1c23` | Textos, footer |

## 🔤 Fuentes (Google Fonts)
1. **Cormorant Garamond** — Títulos y display (serif elegante)
2. **Outfit** — Cuerpo de texto (sans-serif moderno)
3. **DM Mono** — Datos técnicos, precios, contadores

---

## 📐 Modelo E-R (simplificado)

```
ciudades (1) ──< (N) doctores (N) >── (1) especialidades
                      │
                      (1)
                       \
                       (N) citas
                       (N) resenias
                      
usuarios (1) ──< (N) citas
```

---

## 🌐 Tecnologías

- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **Backend:** PHP 7.4+
- **Base de datos:** MySQL 5.7+ / MariaDB
- **Mapa:** Leaflet.js (OpenStreetMap)
- **Email:** EmailJS SDK v4
- **Analytics:** Google Analytics 4 (G-JDETFCBL00)
- **Hosting:** AwardSpace (atwebpages.com)
- **Sin Bootstrap** — CSS completamente propio

---

© 2025 Healthlink · healthlink229753.atwebpages.com
