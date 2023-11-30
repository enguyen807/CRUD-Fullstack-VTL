# Get Started
1. In Backend Folder
    a. Duplicate .env.example -> .env
    b. Install backend files
        i. docker-compose run —-rm composer install
    b. Run table migrations
        i.  docker-compose run --rm artisan migrate
    c. Build and start backend images
        i.  docker-compose up -d --build server  

2. In Frontend Folder
    a. Duplicate .env.example -> .env.development.local
    b. Install frontend files
        i. docker-compose run --rm npm install
    c. Build and Start Frontend image
        i. docker-compose up -d --build frontend

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
    
