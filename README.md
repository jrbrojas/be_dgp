## Forma de iniciar el proyecto backend - DGP

### 1. Instalar dependencias de composer

```bash
$ composer install
```

### 2. Copiar archivo de entorno

```bash
$ cp .env.example .env
```

### 3. Copiar archivo de entorno

```bash
$ php artisan key:generate
```

### 4. Ejecutar migraciones
El proyecto se puede construir con las migraciones de Laravel, por lo que es
independiente del motor de base de datos que se esté utilzando. Como primer
comando se debe ejecutar:

```bash
$ php artisan migrate:fresh --seed
```

### 5. Levantar servidor de desarrollo
Para probar el servidor, debe ejecutar:

```bash
$ php artisan serve
```

### 5. Publicas las imagenes de los mapas para los escenarios
Debe ejecutar:

```bash
$ php artisan storage:link
```

