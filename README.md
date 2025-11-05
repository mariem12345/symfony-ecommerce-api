# symfony-ecommerce-api
This project is a **Symfony-based application** structured using **Hexagonal Architecture (Ports & Adapters)** and fully **Dockerized** for local development.

---

## 🚀 Quick Start

### 1. Clone the repository
```bash
git clone https://github.com/mariem12345/symfony-ecommerce-api.git
cd symfony-ecommerce-api
```

### 2. Build and start containers

```bash
docker compose up -d --build
```

### 3. Access the app

Once the containers are running:

. App → http://localhost:8080

. Database (MySQL) → port 3306

### 4. Common Commands

```bash
docker exec -it symfony_php bash
```
#### Run Symfony console
```bash
php bin/console
```

#### Run migrations


```bash
php bin/console doctrine:migrations:migrate
```

#### Run tests


```bash
php bin/phpunit
```

#### Stop containers
```bash
docker compose down
```
