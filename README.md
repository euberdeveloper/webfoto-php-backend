**Note that this project has been moved to the organization [Dev-digitalgarda](https://github.com/Dev-digitalgarda)**

# webfoto-php-backend

This is the php backend of the project "Webfoto".

## The project

The "Webfoto" project's purpose is to put some cameras, that take a photo every a certain amount of time, in some hotels, so that a web component can show the timeline of the view of that hotel in the hotel's website.

## The PHP backend

The php backend is the code that periodically handles and organizes the images sent by the cameras, making them homogeneous, and that furnishes the APIs to the webcomponent. It is a PHP code that can be used by any server that can run PHP.

## What does it exactly do

For the **cronjob** part:
1. It parses the `.env` file
2. It reads the `settings.json` file
3. Then it does the next steps for each of the albums that it finds in the `settings.json`
4. If a table for the photos of for the album is missing in the db, it creates it
5. Then it retrieves all the images in the input folder of the album
6. It analyzes them and, in base of the configurations, removes the ones that are not needed
7. It then adds a good name to the kept photos and moves them to the output folder of the album, by adding also a tuple to the database for each photo
8. If it is written in the settings, it sends via ftp the last image of the album
9. It then checks if there are images that, depending on the configurations, are too days old, and removes them
10. It then checks if, depending on the configuration, there were not input images for a certain amount of time and, if this is the case, it sends an email alert

For the **api** part:

| endpoint                 | description                                                                                                                                                                                                                         |
|--------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| /api/albums/:name/images | It returns all the ISO timestamps of all the images of this album, sorted in ascending order, as a json array. The param `:name` is exactly the one used in the webcomponent that shows this album and that is in the settings.json |
| /api/albums/:name/images | It redirects to the last image of this album, which is statically served. The param `:name` is exactly the one used in the webcomponent that shows this album and that is in the settings.json                                      |


## The configuration

The configuration is done both with a `.env` file with environment variables and with a `settings.json` file.

### The .env file

There is in this repo a `.env.example` file with an example of `.env` file.

The variables are the ones in the table below:

| Variable                   | Required           | Default         | Description                                                                                                            |
|----------------------------|--------------------|-----------------|------------------------------------------------------------------------------------------------------------------------|
| DB_HOST                    | :heavy_check_mark: | undefined       | The host of the mysql database                                                                                         |
| DB_PORT                    | :heavy_check_mark: | undefined       | The port of the mysql database                                                                                         |
| DB_DATABASE                | :heavy_check_mark: | undefined       | The name of the mysql database                                                                                         |
| DB_USER                    | :heavy_check_mark: | undefined       | The user of the mysql database                                                                                         |
| DB_PASSWORD                | :heavy_check_mark: | undefined       | The password of the mysql database                                                                                     |
| DB_CHARSET                 | :white_check_mark: | utf8mb4         | The charsed used by the mysql database                                                                                 |
| EMAIL_AUTH_TYPE            | :white_check_mark: | undefined       | It can be `CREDENTIALS` or `GOOGLE` and specifies the way the user is authenticated to send emails in case of an alert |
| EMAIL_HOST                 | :white_check_mark: | undefined       | The smtp host of the email                                                                                             |
| EMAIL_USERNAME             | :white_check_mark: | undefined       | The email username                                                                                                     |
| EMAIL_PASSWORD             | :white_check_mark: | undefined       | The email password, used in case of `CREDENTIALS` auth                                                                 |
| EMAIL_GOOGLE_CLIENT_ID     | :white_check_mark: | undefined       | The email google client id, used in case of `GOOGLE` auth                                                              |
| EMAIL_GOOGLE_CLIENT_SECRET | :white_check_mark: | undefined       | The email google client secret token, used in case of `GOOGLE` auth                                                    |
| EMAIL_GOOGLE_REFRESH_TOKEN | :white_check_mark: | undefined       | The email google refresh token, used in case of `GOOGLE` auth                                                          |
| EMAIL_RECIPIENT            | :white_check_mark: | undefined       | The email address of the recipient of the alert email                                                                  |
| EMAIL_RECIPIENT_TEXT       | :white_check_mark: | undefined       | The name of the email recipient of the alert email                                                                     |
| EMAIL_SUBJECT              | :white_check_mark: | undefined       | The subject of the email alert. Note that `{{ALBUM}}` is substituted with the album name                               |
| EMAIL_BODY                 | :white_check_mark: | undefined       | The body of the email alert. Note that `{{ALBUM}}` is substituted with the album name                                  |
| ALERT_THRESHOLD_HOURS      | :white_check_mark: | undefined       | After how many hours without images will an email alert be sent                                                        |
| SETTINGS_PATH              | :white_check_mark: | ./settings.json | The path of the settings json file                                                                                     |
| KEEP_LAST_DAYS             | :heavy_check_mark: | undefined       | How many days of photos will be kept (Note that older photos will be deleted).                                         |
| OUTPUT_FOTOS_PATH          | :heavy_check_mark: | undefined       | The path where the handled photos will be saved (Note that there will be a subfolder with the album's name).           |
| OUTPUT_FOTOS_URL           | :heavy_check_mark: | undefined       | The path that statically serves the photos (the root folder). It is used bye the last-image api endpoint.              |
| ADD_CORS                   | :white_check_mark: | true            | If in the api endpoint a cors-allow-anywhere will be added.                                                            |

### The settings.json file

It is an array defining each album (which represents an hotel). Note that there is a `settings.example.json` file in this repo.

The properties are:

| Variable         | Type   | Required           | Description                                                                             |
|------------------|--------|--------------------|-----------------------------------------------------------------------------------------|
| name             | string | :heavy_check_mark: | The name of the album, which will be used also in the database                          |
| inputPath        | string | :heavy_check_mark: | The path to the directory were the camera saves the album photos                        |
| driver           | string | :heavy_check_mark: | The type of driver used to parse the input folder (for instance `dahua` or `hikvision`) |
| keepEverySeconds | number | :heavy_check_mark: | Which is the minimum gap in seconds of two subsequent photos that are kept              |
| ftp.host         | string | :white_check_mark: | The ftp host where the last images will be sent                                         |
| ftp.user         | string | :white_check_mark: | The ftp user where the last images will be sent                                         |
| ftp.port         | number | :white_check_mark: | The ftp port where the last images will be sent                                         |
| ftp.password     | string | :white_check_mark: | The ftp password where the last images will be sent                                     |
| ftp.destination  | string | :white_check_mark: | The ftp path where the last images will be sent                                         |

Note: `ftp` is a nested object.

### The database

The database is **mysql** or **mariadb** (neither the implementation nor the configuration change).

The database contains two tables:
* __images__: It contains the saved images
* __alerts__: It contains information about alerts

The image table:
| Column    | Type     | Description                                                          |
|-----------|----------|----------------------------------------------------------------------|
| id        | integer  | The incremental id of the tuple                                      |
| name      | text     | The name of the album                                                |
| path      | text     | The path where the image is saved, relative to the outputs subfolder |
| timestamp | datetime | The datetime of the image                                            |

The alerts table:
| Column    | Type     | Description                                                                                                                |
|-----------|----------|----------------------------------------------------------------------------------------------------------------------------|
| id        | integer  | The incremental id of the tuple                                                                                            |
| name      | text     | The name of the album                                                                                                      |
| timestamp | datetime | The timestamp when the last time an alert was sent for this album. It is used to avoid send more than an email alert a day |

## How was it made

Because **Rector** is used to downgrade the php code to **legacy php code versions**, ideally, the last version of php can be used for this code. At the time this text was written, the latest version was php 8.

For the dependencies, **composer** was used. All the code is well modularized and organized in namespaces with classes.

## How to use it

1. Download the last generated code from the [releases](https://github.com/Dev-digitalgarda/webfoto-php-backend/releases)
2. Add a `.env` and a `settings.json` file, by looking at the analogue example files and at this readme
3. Execute `composer install`
4. Serve the php code and note that you are responsible to call the `cronjob.php` periodically

## Automatic deploy

The deploy is actually automatically handled through a **github action**.

The github action:
1. Starts an ubuntu docker container
2. Adds php and composer
3. Installs the dependencies
4. Uses rector to downgrade the php version
5. Compresses the code
6. Gets the composer.json version
7. Pulishes a new release with that version and with the compressed backend

## Were are the static files that I just have to serve?

Every automatic deploy creates a **[new release](https://github.com/Dev-digitalgarda/webfoto-php-backend/releases)**. In that release, there is attached a compressed (.tar.gz) file containing the generated code.

## How should I contribute

Whoever will contribute and work on this code:

1. Should install locally **php**, **composer** and a **mysql-like db**
2. Clone the project and checkout on the **dev branch**
3. Change the code
4. Add the submodule with `composer run pull-core`
5. Test locally the changes with `composer start`
6. Push the code **on dev**
7. Only when you want a new release to be published, you should **update the version in composer.json and merge the dev branch into main**
