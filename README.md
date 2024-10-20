# Canoe Code Challenge

## Prelude
I'll make this section short, but thank you very much for taking the time to
review this challenge, and the opportunity to work with you guys. I look
forward to what is to come.

## TL;DR
Skip to Evaluator Notes at the bottom for usage instructions

## Assumptions
- I try to avoid saving binaries in repositories, but felt my ERD could be
  an exception this time around.  You can find it in the `docs` folder
- Since 'Fund Managers' were called out as companies in the exercise spec, and
  they took the same 'shape' as a company (with only a name), I combined them
  as one 'Company' model. A Fund Manager was then appointed as such by creating
  a relationship from the 'Fund' model's 'fund_manager_id' property, which is
  required. I can see some downsides to this, so this would obviously be called
  out in a design meeting at some point.
- I've always been really liberal with ids and dates fields on a table.  If
  data sets become big, and they are deamed unnecessary, they can be removed
  later.
- I left authentication and user management completely out of this exercise.
  I felt really gross doing this, but all endpoints are completely open.

## Improvements I could still make
- I think storing aliases in a json blob as an array would have made the api
  and logic WAY more elegant
- As the data set grows, I could probably trim some unnecessary columns
  (mentioned above)
- Tests could probably be written more cleanly/precisely, but I'm already
  over time

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
composer install --ignore-platform-reqs
```

- Copy .env.example to .env
```bash
cp .env.example .env
```

- Bring up development environment:
```bash
sail up -d
```

- Generate App key
```bash
sail artisan key:generate
```

- Run database migrations
```bash
sail artisan migrate
```
Note: This was developed using sqlite.  Please ensure ./database/database.sqlite exists

- Seed the database (Company endpoints were not created, so you need company data)
```bash
sail artisan db:seed
```

### Run Tests (Sail container must be running from above step):
```bash
sail artisan test
```

## Evaluator Notes
- The ERD can be located in the ./docs/erd.png file and should be replaced with
  a link to the living document.  This document was generated by LucidCharts.
- Endpoints can be retrieved by running `sail artisan route:list`
- Endpoint payloads can be gleaned from looking at the
  `tests/Feature/Http/Controllers/FundControllerTest.php` file.  Funds'
  alias and portfolio payloads can notably be found in
  `test_update_endpoint_with_aliases`, and
  `test_update_endpoint_with_portfolio_entries` tests.
- If you've made it this far, you're awesome! It's so much! Thank you again!