# 🎲 RaffleHub - Sistema de Rifas Online

## 📋 Descripción

**RaffleHub** es un sistema completo de gestión de rifas desarrollado con Laravel 12 y Tailwind CSS. Permite crear, administrar y vender boletos de rifas de manera eficiente con actualización en tiempo real del estado de los números.

### ✨ Características Principales

- 🎯 **Vista Pública Interactiva**: Grid visual con todos los números disponibles y vendidos
- ⚡ **Actualización Automática**: Los números se actualizan cada 30 segundos sin recargar la página
- 📱 **Diseño Responsive**: Interfaz adaptable a dispositivos móviles, tablets y desktop
- 🔧 **Panel de Administración**: Gestión completa de configuración de rifas
- 💰 **Gestión de Ventas**: Registro y seguimiento de boletos vendidos
- 📊 **Estadísticas en Tiempo Real**: Total, vendidos, disponibles y porcentaje de venta
- 📲 **Integración con WhatsApp**: Contacto directo para compra de boletos
- 🎨 **Interfaz Moderna**: Diseño atractivo con Tailwind CSS
- 🖼️ **Gestión de Imágenes**: Carga y visualización de imagen del premio

### 🛠️ Tecnologías

- **Backend**: Laravel 12 (PHP)
- **Frontend**: Blade Templates, Tailwind CSS, JavaScript (Vanilla)
- **Base de Datos**: MySQL/PostgreSQL
- **Autenticación**: Laravel Breeze
- **Almacenamiento**: Laravel Storage (imágenes)

### 📦 Instalación

```bash
# Clonar el repositorio
git clone https://github.com/jhonrymat/rafflehub.git
cd rafflehub

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
php artisan migrate --seed

# Crear enlace simbólico para imágenes
php artisan storage:link

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

### 🚀 Uso

1. **Acceso Público**: Visita la página principal para ver los números disponibles
2. **Panel Admin**: Ingresa a `/login` para gestionar la rifa
3. **Configuración**: Edita el premio, fechas, precios y métodos de sorteo
4. **Ventas**: Marca números como vendidos desde el panel de administración


### 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu característica (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo 
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

### 👨‍💻 Autor

**Tu Nombre**
- GitHub: [@jhonrymat](https://github.com/jhonrymat)

### 🙏 Agradecimientos

- Laravel Framework
- Tailwind CSS
- Comunidad Open Source

---

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub!
