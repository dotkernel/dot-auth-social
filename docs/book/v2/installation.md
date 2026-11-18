# Installation

## Requirements

- PHP 8.3, 8.4, 8.5 or 8.6
- A PSR-11 container that accepts one factory for each service key, for example Laminas Service Manager

`dotkernel/dot-auth-social` does not install a container.
If your application uses Laminas Service Manager, require `laminas/laminas-servicemanager` in the `composer.json` file of your application.

## Install the package

Run this Composer command in your project directory:

```shell
composer require dotkernel/dot-auth-social
```

Then add `Dot\AuthSocial\ConfigProvider` to your configuration aggregate.
