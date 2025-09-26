# Justificación Técnica de la Arquitectura

La arquitectura de este proyecto se diseñó para ser **modular**, **escalable** y **robusta**, siguiendo buenas prácticas. A continuación, se detalla cada componente clave:

---

## 1. Patrón de Diseño: Service-Repository

El núcleo de la lógica de negocio se implementa utilizando el patrón **Service-Repository**, que permite una clara separación de responsabilidades:

| Componente       | Descripción                                                                                    |
| ---------------- | ---------------------------------------------------------------------------------------------- |
| **Servicios**    | Contienen la lógica de negocio pura, como la creación de reservas y validaciones.              |
| **Repositorios** | Encapsulan la lógica de acceso a datos, desacoplando la lógica de negocio de la base de datos. |

### Beneficios:

- **Modularidad**: Los controladores se mantienen limpios y enfocados en manejar peticiones HTTP.
- **Flexibilidad**: Permite cambiar la base de datos o el ORM sin modificar la lógica de negocio.

---

## 2. Comunicación en Tiempo Real

Se utiliza **Redis**, **Node.js** y **Socket.IO** para implementar un sistema de comunicación en tiempo real basado en el patrón **pub/sub**:

| Componente     | Descripción                                                                                      |
| -------------- | ------------------------------------------------------------------------------------------------ |
| **Publicador** | Laravel actúa como publicador, disparando eventos cuando una reserva cambia de estado.           |
| **Canal**      | Redis actúa como intermediario, gestionando los mensajes entre el publicador y los suscriptores. |
| **Suscriptor** | Un servidor Node.js retransmite las notificaciones a los clientes conectados vía WebSockets.     |

### Casos de Uso:

- Notificaciones en tiempo real para actualizaciones de reservas.
- Desacoplamiento entre el emisor (Laravel) y el receptor (dashboard en vivo).

---

## 3. Base de Datos y Optimización

Se eligió **MySQL** como base de datos por su fiabilidad y rendimiento. La estructura de las tablas incluye índices estratégicos para optimizar las consultas:

| Tabla             | Campos Principales                                | Índices Propuestos                             |
| ----------------- | ------------------------------------------------- | ---------------------------------------------- |
| **bookings**      | `id`, `flight_number`, `status`, `departure_time` | `status`, `created_at`                         |
| **passengers**    | `id`, `booking_id`, `name`, `passport_number`     | `booking_id`, `passport_number`                |
| **notifications** | `id`, `type`, `notifiable_type`, `notifiable_id`  | `notifiable_type`, `notifiable_id` (compuesto) |

sin optimizar la base de datos

EXPLAIN SELECT \* FROM skylink.bookings WHERE status = 'CONFIRMED';

id|select_type|table |type|possible_keys|key|key_len|ref|rows|Extra |
--+-----------+--------+----+-------------+---+-------+---+----+-----------+
1|SIMPLE |bookings|ALL | | | | |22 |Using where|

luego de crear el indice

id|select_type|table |type|possible_keys|key |key_len|ref |rows|Extra |
--+-----------+--------+----+-------------+------------+-------+-----+----+---------------------+
1|SIMPLE |bookings|ref |status_index |status_index|1022 |const|3 |Using index condition|

type: ref: Ahora MySQL utiliza un índice para encontrar las filas, lo que es significativamente más rápido que un escaneo completo.

key: status_index: Confirma que el índice creado se utilizó para la consulta.

## rows: 3: El número de filas examinadas se redujo a solo 3, demostrando una eficiencia masiva.

## 4. Testing Unitario

Se implementaron pruebas unitarias en la capa de servicio (`BookingService`) utilizando **PHPUnit** y **Mockery**. Estas pruebas están desacopladas de la base de datos, lo que garantiza:

- **Velocidad**: Las pruebas son rápidas y confiables.
- **Seguridad**: Permiten refactorizar la lógica de negocio sin romper la funcionalidad.

---

## Cómo Correr el Proyecto

### Requisitos 🛠️

Asegúrate de tener instaladas las siguientes herramientas:

- **Docker**
- **PHP** (versión 8.2 o superior)
- **Composer**
- **Node.js** y **npm**

### 1. Configuración de Entorno

Clona el repositorio y configura el entorno:

```bash
git clone [URL_DEL_REPOSITORIO]
cd [nombre_del_directorio]
cp .env.example .env
```

Configura las variables de entorno en el archivo `.env`:

```ini
# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skylink
DB_USERNAME=root
DB_PASSWORD=123456Cc

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 2. Iniciar Servicios de Contenedor

Para que la API funcione correctamente, necesitas iniciar un servidor de base de datos MySQL y un servidor de Redis. Si ya los tienes corriendo localmente, puedes omitir este paso. Si no, usa Docker.

Inicia el contenedor de Redis:

```bash
docker pull redis
docker run --name my-redis-server -d -p 6379:6379 redis
```

Asegúrate de que tu servidor MySQL local esté en ejecución.

### 3. Instalación de Dependencias

Instala las dependencias de PHP y Node.js que requiere el proyecto.

```bash
# Dependencias de PHP
composer install

# Dependencias de Node.js
npm install
```

### 4. Preparar la Base de Datos

Genera la clave de la aplicación y ejecuta las migraciones para crear las tablas y rellenarlas con datos falsos.

```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Iniciar Servidores de la Aplicación

Abre dos terminales separadas para iniciar los servidores necesarios.

**Terminal 1**: Inicia el servidor de desarrollo de Laravel (dentro del path `backend`).

```bash
php artisan serve
```

**Terminal 2**: Inicia el servidor de Node.js para las notificaciones en tiempo real (dentro del path donde se encuentra la carpeta `streaming-service`).

```bash
npm init -y
npm install express socket.io ioredis
node server.js
```

Con estos pasos, tu API y el sistema de notificaciones en tiempo real estarán completamente operativos y listos para ser probados.

---

## Probando el Sistema

Ejecutar desde la raiz del projecto (dentro del path de la carpeta backend)

```bash
php artisan l5-swagger:generate
```

Después de esto, se puede leer la documentación de los endpoints desde el navegador utilizando `http://127.0.0.1:8000/api/documentation`.

Por último, abre una terminal desde la raíz del proyecto (dentro de la carpeta `backend`) y ejecuta:

```bash
php artisan skylink:simulate-bookings
```

Esto generara el comando para simular el cambio de status de las reservas.

Para poder visualizarlo en el dashboard (ver las notificaciones en tiempo real) , se puede abrir en un navegador el archivo dashboard.html que se encuentra dentro de la carpeta raiz (challenge365asist)
