

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


# API Endpoints 
## Restaurants
#### Endpoint: POST `/api/v1/auth/register`
Create user.

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

#### Endpoint: POST `/api/v1/auth/login`
Login a user.

 ```json
curl --location '[URL_PROJECT]/api/v1/auth/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "andres@gmail.com",
    "password": "Andres"
}'
 ```

## Endpoint: GET `/api/v1/me`
Show all user information.

 ```json
 curl --location '[URL_PROJECT]/api/v1/me' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer [TOKEN]'
 ```


## Endpoint: GET `/api/v1/admin/restaurant/`
Get all user's restautants.

 ```json
 curl --location '[URL_PROJECT]/api/v1/admin/restaurant/' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data ''
 ```


## Endpoint: GET `/api/v1/admin/restaurant/:id`
Get a single restaurant.

 ```json
 curl --location '[URL_PROJECT]/api/v1/admin/restaurant/35899fe3-aaba-49f3-be22-867ef5b09d82' \
--header 'Accept: application/json' \
--data ''
 ```


#### Endpoint: POST `/api/v1/admin/restaurant`
Create a restaurant.

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


## Endpoint: PUT `/api/v1/admin/restaurant/:id`
Update a restaurant.

 ```json
 curl --location --request PUT '[URL_PROJECT]/api/v1/admin/restaurant' \
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


## Endpoint: DELETE `/api/v1/admin/restaurant`
Delete a restaurant.

 ```json
 curl --location --request DELETE '[URL_PROJECT]/api/v1/admin/restaurant' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "id": "35899fe3-aaba-49f3-be22-867ef5b09d82"
}'
 ```

## Categories
## Endpoint: GET `/api/v1/admin/categories`
Get all categories

```json
curl --location '[URL_PROJECT]/api/v1/admin/categories' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data ''
```

## Endpoint: POST `/api/v1/admin/categories`
Create category

```json
curl --location '[URL_PROJECT]/api/v1/admin/categories' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "name": "Categoria 2",
    "description": "Descripcion dela restaurasnte de Andres",
    "position": 2,
    "active": true
}'
```

## Endpoint: PUT `/api/v1/admin/categories/:id`
Update category

```json
curl --location --request PUT '[URL_PROJECT]/api/v1/admin/categories/cd5a296f-0f25-47c6-b83b-110ae4815fae' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "name": "Categoria 4",
    "description": "Descripcion dela restaurasnte de Andres",
    "position": 1,
    "active": true
}'
```

## Endpoint: DELETE `/api/v1/admin/categories/:id`
Delete category

```json
curl --location --request DELETE '[URL_PROJECT]/api/v1/admin/categories/c0e14916-02a3-4a4f-bbc8-ccb5ce4d1e40' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "name": "Categoria 4",
    "description": "Descripcion dela restaurasnte de Andres",
    "position": 1,
    "active": true
}'
```

## Endpoint: PATCH `/api/v1/admin/categories/reorder`
Reorder categories

```json
curl --location --request PATCH '[URL_PROJECT]/api/v1/admin/categories/reorder' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "categories": [
        {
            "id": "0ae5318f-a797-4f58-9ab7-d553655a9bc5",
            "position": 1
        },
        {
            "id": "df889c16-7090-4684-b6b4-3da7706b9c7a",
            "position": 2
        }
    ]
}'
```

## Dishes
## Endpoint: GET `/api/v1/admin/dishes`
Get all dishes

```json
curl --location --request GET '[URL_PROJECT]/api/v1/admin/dishes' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "category_id": "df889c16-7090-4684-b6b4-3da7706b9c7a"
}'
```

## Endpoint: GET `/api/v1/admin/dishes/:id`
Get a single dish

```json
curl --location --request GET '[URL_PROJECT]/api/v1/admin/dishes/1d6fa306-17b7-4d92-9c21-0d8a5544fb15' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
    "category_id": "df889c16-7090-4684-b6b4-3da7706b9c7a"
}'
```

## Endpoint: POST `/api/v1/admin/dishes`
Create dish

```json
curl --location '[URL_PROJECT]/api/v1/admin/dishes' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
  "name": "Hamburguesa Angus Especial",
  "description": "Hamburguesa de carne Angus 200g con queso cheddar, tocineta crujiente y salsa de la casa.",
  "price": 32000,
  "offer_price": 28000,
  "image_url": "https://mirestaurante.com/images/hamburguesa-angus.jpg",
  "available": true,
  "featured": true,
  "tags": ["carne", "popular", "sin gluten"],
  "position": 1,
  "category_id": "df889c16-7090-4684-b6b4-3da7706b9c7a"
}'
```

## Endpoint: PUT `/api/v1/admin/dishes/:id`
Update dish

```json
curl --location --request PUT '[URL_PROJECT]/api/v1/admin/dishes/8fded667-9a12-445f-bdc0-e8ff1898a1fc' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
  "name": "Hamburguesa Angus Especial",
  "description": "Hamburguesa de carne Angus 200g con queso cheddar, tocineta crujiente y salsa de la casa.",
  "price": 32000,
  "offer_price": 38000,
  "image_url": "https://mirestaurante.com/images/hamburguesa-angus.jpg",
  "available": true,
  "featured": true,
  "tags": ["carne", "popular", "sin gluten"],
  "position": 1,
  "category_id": 3
}'
```

## Endpoint: DELETE `/api/v1/admin/dishes/:id`
Delete dish

```json
curl --location --request DELETE '[URL_PROJECT]/api/v1/admin/dishes/f6645e59-75d4-4638-bdc6-f3bc4ee24d19' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
  "name": "Hamburguesa Angus Especial",
  "description": "Hamburguesa de carne Angus 200g con queso cheddar, tocineta crujiente y salsa de la casa.",
  "price": 32000,
  "offer_price": 28000,
  "image_url": "https://mirestaurante.com/images/hamburguesa-angus.jpg",
  "available": true,
  "featured": true,
  "tags": ["carne", "popular", "sin gluten"],
  "position": 1,
  "category_id": 3
}'
```

## Endpoint: PATCH `/api/v1/admin/dishes/:id/availability`
Toggle dish aviability

```json
curl --location --request PATCH '[URL_PROJECT]/api/v1/admin/dishes/1d6fa306-17b7-4d92-9c21-0d8a5544fb15/availability' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer [TOKEN]' \
--data '{
  "name": "Hamburguesa Angus Especial",
  "description": "Hamburguesa de carne Angus 200g con queso cheddar, tocineta crujiente y salsa de la casa.",
  "price": 32000,
  "offer_price": 28000,
  "image_url": "https://mirestaurante.com/images/hamburguesa-angus.jpg",
  "available": true,
  "featured": true,
  "tags": ["carne", "popular", "sin gluten"],
  "position": 1,
  "category_id": 3
}'
```


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


