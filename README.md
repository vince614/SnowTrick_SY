# SnowTrick_SY
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e4071ea9bf6044e480194e8525830367)](https://www.codacy.com/gh/vince614/SnowTrick_SY/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=vince614/SnowTrick_SY&amp;utm_campaign=Badge_Grade)

Projet symfony blog pour les figures de snowboard

### Pour installer le project : 

Cloné le projet avec la commande
`git clone https://github.com/vince614/SnowTrick_SY.git`

Une fois cela fais faites juste la commande `cd SnowTrick_SY && composer install`.

Ensuite crée un fichier `.env` à la racine du projet contenant les différente configurations, 
puis remplacés-les avec votre config.
```dotenv
APP_ENV=dev
APP_SECRET=<YOUR_APP_SECRET>
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
DATABASE_URL="mysql://<MYSQL_USER>:<MYSQL_PASSWORD>@127.0.0.1:3306/<DATABASE_NAME>"
MAILER_DSN=<YOUR_SMTP_CONNECTION>
```


