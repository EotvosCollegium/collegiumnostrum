# Collegium Nostrum

The alumni database for Eötvös Collegium.

## How to start contributing

Maybe I have finally found a way that works:D at least under **Ubuntu 20.04**.

- Clone the repo and follow the usual Laravel installation instructions (I will detail these here later). Don't bother with the database; that will be solved by Sail.
- Install Docker with the help of [these instructions](https://docs.docker.com/engine/install/ubuntu/).
- Add your user to the `docker` group: `sudo groupadd docker && sudo usermod -aG docker $USER`. This will ensure you can manage Docker without `sudo` later. _(Note: this might be a bit unsafe. But this is the way it worked for me.)_
- Update `APP_URL` to `http://localhost:8080`.
- Add these lines to `.env`:
```
APP_PORT=8080
FORWARD_DB_PORT=33066
```
- In `.env`, rewrite `DB_HOST` from the given IP to `mysql`.
- Run `./vendor/bin/sail up`.
- Open another terminal. Before seeding, add the correct privilege to the user `collegiumnostrum` in MySQL (this has to be done every time) by running:

```sh
docker exec -it collegiumnostrum-mysql-1 mysql --password -e "SET GLOBAL log_bin_trust_function_creators = 1;"
```

- Run `./vendor/bin/sail artisan migrate:fresh --seed`. (Other Artisan commands need to be executed similarly.)
- Now you can test the site at `http://localhost:8080`.
- Instead of SSH, you can use `docker exec -it collegiumnostrum-laravel.test-1 bash`.
- And to access MySQL, run `docker exec -it collegiumnostrum-mysql-1 mysql --user=collegiumnostrum --password collegiumnostrum` (change the container name, the username and the database name if needed; the latter two are in .env) and log in with the password (also found in .env).
