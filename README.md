# Canoe Code Challenge

## Dev (Linux Setup)
Note:
The following steps assume that PHP, Composer, and Docker are installed and
accessible at a bash prompt with the following alias added to your bash init
(Please see https://laravel.com/docs/11.x/sail for questions about Laravel
Sail):
```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

### Initial Setup
- Install PHP dependencies via Composer:
```bash
composer install
```

- Bring up the sail container:
```bash
sail up -d
```

### Run Tests (Sail container must be running from above step):
```bash
sail artisan test
```