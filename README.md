

### How to start


To start the project, follow these steps:

1. Clone the repository:
    ```bash
    git clone [REPOSITORY_URL]
    ```

2. Navigate to the project directory:
    ```bash
    cd p1-menu-digital-api
    ```

3. Install dependencies:
    ```bash
    composer install
    ```

4. Copy the `.env.example` file to `.env`:
    ```bash
    cp .env.example .env
    ```

5. Generate the application key:
    ```bash
    php artisan key:generate
    ```

6. Set up the database in the `.env` file and run migrations:
    ```bash
    php artisan migrate
    ```

7. Install Passport and generate encryption keys:
    ```bash
    php artisan passport:client --personal  
    ```

8. Create passport keys:
    ```bash
    php artisan passport:keys    
    ```

9. Start the development server:
    ```bash
    php artisan serve
    ```


### API Endpoints 
### Endpoint: `/api/v1/auth/register`
 ```json
curl --location '[URL_PROJECT]/api/v1/auth/register' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "Andres",
    "email": "andres@gmail.com",
    "password": "Andres"
}'
 ```

### Endpoint: `/api/v1/auth/login`
 ```json
curl --location '[URL_PROJECT]/api/v1/auth/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "andres@gmail.com",
    "password": "Andres"
}'
 ```

### Endpoint: `/api/v1/me`

 ```json
 curl --location '[URL_PROJECT]/api/v1/me' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer [TOKEN]'
 ```


### Endpoint: `/api/v1/admin/restaurant/`

 ```json
 curl --location '[URL_PROJECT]/api/v1/admin/restaurant/' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data ''
 ```


### Endpoint: `/api/v1/admin/restaurant/:id`

 ```json
 curl --location '[URL_PROJECT]/api/v1/admin/restaurant/35899fe3-aaba-49f3-be22-867ef5b09d82' \
--header 'Accept: application/json' \
--data ''
 ```


### Endpoint: `/api/v1/admin/restaurant`

 ```json
 curl --location '[URL_PROJECT]/api/v1/admin/restaurant' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "name": "Restaurante de Adsssnddres",
    "description": "Descripcion dela restaurasnte de Andres",
    "phone": "3412345678",
    "address": "Calle Falsa 123",
    "hours": {
            "monday": [
            { "open": "08:00", "close": "12:00" },
            { "open": "14:00", "close": "17:00" }
            ],
            "tuesday": [
            { "open": "08:00", "close": "18:00" }
            ],
            "sunday": []
        },
    "logo": "https://example.com/logo.png"
}'
 ```


### Endpoint: `/api/v1/admin/restaurant?`

 ```json
 curl --location --request PUT '[URL_PROJECT]/api/v1/admin/restaurant?=null' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "name": "Restaurante de Adsssnddres",
    "description": "Descripcion dela restaurasnte de Andres",
    "phone": "3412345678",
    "address": "Calle Falsa 123",
    "hours": {
            "monday": [
            { "open": "08:00", "close": "12:00" },
            { "open": "14:00", "close": "17:00" }
            ],
            "tuesday": [
            { "open": "08:00", "close": "18:00" }
            ],
            "sunday": []
        },
    "logo": "https://example.com/logo.png"
}'
 ```


### Endpoint: `/api/v1/admin/restaurant`

 ```json
 curl --location --request DELETE '[URL_PROJECT]/api/v1/admin/restaurant' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "id": "35899fe3-aaba-49f3-be22-867ef5b09d82"
}'
 ```





#### Notes:
- Ensure the `email` field is unique.
- Passwords should be stored securely (e.g., hashed) on the server.
aksdj

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


