# electron-update-host
Stores a built Electron app and serves it through an update channel 🗂🔄

Why? Isn't there [Hazel](https://github.com/zeit/hazel) already?
Of course! And if you have the chance, go for it!

But you might also be in the situation, that you don't have the needed code certificates, wich would enable you to roll with Electrons autoUpdater implementation 😅

In that case, you would need to serve your App updates yourself somehow. The following tools make it quite easy for you:

- [electron-update-host](https://github.com/SMenigat/electron-update-host) - Stores your built Electron app and serves it through an update channel.
- [electron-update-host-adapter](https://github.com/SMenigat/electron-update-host-adapter) - Drop in replacement for Electron `autoUpdater`, which makes it a breeze to connect your app to your `electron-update-host` instance.
- [electron-release-uploader](https://github.com/SMenigat/electron-release-uploader) - CLI tool, that helps you uploading your latest app build to your `electron-update-host` instance.

## Installation
This project is based on the [PHP Lumen framework](https://lumen.laravel.com/).
You need at least PHP `v7.1.3` to run this app. Check [Lumens requirements](https://lumen.laravel.com/docs/5.6#server-requirements) for more details.

**Installation Quick Guide:**

- Clone / Download latest build of [electron-update-host](https://github.com/SMenigat/electron-update-host)
- Run `composer install` to install all dependencies
- Configure your `.env` file (see `Configuration` hints below)
- Upload everything to your webspace
- Point the webroot of your domain into the `/public` folder
- Make sure your `.htaccess` file within `/public` works (`mod_rewrite` needs to be enabled)
- PHP needs write access to the directories `/app-updates` and `/storage`

## Configuration
Besides the standard [.env configuration](https://lumen.laravel.com/docs/5.2/configuration#environment-configuration) you should secure your upload enpoint by defining a **password header**.

You can define these password settings in your `.env` file. Have a look at the example below:

```INI
UPLOAD_PASSWORD_HEADER=X-Password-Header
UPLOAD_PASSWORD=SuperSavePassword
```

For now on, a header with the name `X-Password-Header` and the value `SuperSavePassword` needs to be set for **`POST`**`/update/{version}` requests. A status `401 - Unauthorized` is beeing thrown otherwise. 

If you don't set the `.env` password settings, every upload will be accepted 🔥


## API Endpoints
Below a brief explenation of the available endpoints. 

### `POST` /update/{version}
Requires a password header, if configured.
Accepts `multipart/form-data` file upload. The file param should be named `app-update` and contain your `tar`'ed Electron app build.
The url parameter `{version}` needs to be in a format like `1-0-0` and not like `1.0.0`.

### `GET` /update/{platform}/latest
Triggers download of latest Electron app build for requested platform. 
Supported platforms are mentioned here in the node [os.platform() documentation](https://nodejs.org/api/os.html#os_os_platform).

### `GET` /check-update/{platform}/{version}
Evaluates if there is an available update for the requested `version` and `platform`.
If there is one, a object like this is returned:

```JSON
{
  "releaseNotes": "",
  "releaseName": "1.8.3",
  "releaseDate": 1527686593,
  "updateURL": "http://<your-electron-update-host>.com/update/mac/latest"
}
```

If there is no update available, HTTP status `204 - No Content` is beeing returned.

Supported platforms are mentioned here in the node [os.platform() documentation](https://nodejs.org/api/os.html#os_os_platform).
The url parameter `{version}` needs to be in a format like `1-0-0` and not like `1.0.0`.





