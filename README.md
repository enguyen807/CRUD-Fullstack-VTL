# Get Started
1. In Backend Folder
    1. Duplicate .env.example -> .env
       
    2. Install backend files
       1. ``` docker-compose run —-rm composer install ```
      
    3. Run table migrations
       1. ``` docker-compose run --rm artisan migrate ```

    4. Build and start backend images
       1. ``` docker-compose up -d --build server ```

2. In Frontend Folder
    1. Duplicate .env.example -> .env.development.local
       
    2. Install frontend files
        1. ```docker-compose run --rm npm install```
           
    3. Build and Start Frontend image
        1. ```docker-compose up -d --build frontend```

# Other useful commands #

## Frontend
    # Build Frontend
    docker-compose run --rm npm {a command in /frontend/package.json}

## Backend
    # Build Backend
    docker-compose run --rm composer create-project --prefer-dist laravel/lumen
    # Create tables
    docker-compose run --rm artisan migrate

## Start
    # Start Containers
    docker-compose up -d server frontend
    # Start Containers and build/rebuild images
    docker-compose up -d --build server frontend

## Test
    docker-compose run --rm php ./vendor/bin/phpunit
    docker-compose run --rm php ./vendor/bin/phpunit --filter 'CustomerCreateTest'

## End 
    docker-compose down
    
