# README

## Introduction
There are a lot of modules for Drupal 7 that let you login using OneLogin. However, the modules that are there for Drupal 8 lack documentation, proper coding or don't even work correctly. Therefore, we, Youwe, tried to built or own. As a starting point, we used the working Drupal 7 module from OneLogin themselve, which can be found[here](https://github.com/onelogin/drupal-saml/tree/master/onelogin_saml).

## Index
* 1 - General information
  * 1.1 External library
* 2 - Installation
  * 2.1 Composer
  * 2.2 Drush
  * 2.3 Manual
* 3 - Usage
* 4 - File overview
  * 4.1 - Files
    * 4.1.1 - 
    *
  
## 1 - General information
**Library**  
The modules uses the onelogin-saml library. 

## 2- Installation
### 2.1 Composer
If you installed the module through Composer, everything should be there and working as expected. The module is called OneLogin SAML and can be found within the <i>OneLogin</i> package.
### 2.2 Drush
-- To be written --

The modules uses the onelogin-saml library. You need to include this as well with the following command: ```"onelogin/php-saml": "^2.11"```.
### 2.3 Manually
-- To be written --

The modules uses the onelogin-saml library. You need to include this as well with the following command: ```"onelogin/php-saml": "^2.11"```.

## 3 - Usage

## 4 - File/folder overview
The project tree is as follows:
```
OneLogin Integration
│
│──── config
│     └──── install 
│          └──── onelogin_saml.settings.yml 
│
│──── src
│     └──── Controller
│     │     └──── OneLoginSAMLController.php
│     └──── Form
│     │    └──── OneLoginSAMLAdminForm.php
│     │   AuthenticationService.php
│     │   AuthenticationServiceInterface.php
│     │   SAMLAuthenticatorFactory.php
│     │   SAMLAuthenticatorFactoryInterface.php
│     │   UserService.php
│     │   UserServiceInterface.php
│   
│  composer.json
│  LICENSE.md
│  onelogin_saml.info.yml
│  onelogin_saml.install
│  onelogin_saml.links.menu.yml
│  onelogin_saml.module
│  onelogin_saml.permissions.yml
│  onelogin_saml.routing.yml
│  onelogin_saml.services.yml
│  README.md
```

### 4.1 - Files
#### 4.1.1 - Config
**onelogin_saml.settings.yml**  
The settings file defines default values for the admin form. Some of the fields require some input when the module is just installed or some fallbacks. That's where this settings file kicks in. 

#### 4.1.2 - Controller
**OneLoginSAMLController.php**  
This controller takes care of the actions defined by the routes in the routes file. 

#### 4.1.3 - Form
**OneLoginSAMLAdminForm.php**  
The form file defines an admin form that can be reached through the backend. In this form, you insert the URL's that OneLogin gives you and configure how the application should behave.

#### 4.1.4 - Src
**AuthenticationService.php**  
The Authenticationservice takes care of the processes after a correct login response from OneLogin. It syncs the roles and creates a new user if the one from the request is not in the system yet.

**AuthenticationServiceInterface.php**  
The interface for the Authentication Service.

**SAMLAuthenticatorFactory.php**  
This factory creates an instance of the third-party library class OneLogin_Saml2_Auth. The library itself can be found [here](https://github.com/onelogin/php-saml). The instance uses a default set of settings, mainly coming from the admin form in the backend, but it is possible to provide your own settings. In that case, the default settings and the given settings are merged into one settings variable and used as a parameter when the OneLogin_Saml2_Auth is instantiated.

**SAMLAuthenticatorFactoryInterface.php**
The interface for the SAML Authenticator Factory

**UserService.php**  
If it turns out that a new user has to be created, then this service is called to take care of that.

**UserServiceInterface.php**  
The interface for the User service.

#### 4.1.5 - Project root
**composer.json**


**LICENSE.md**

**onelogin_saml.info.yml**

**onelogin_saml.install**

**onelogin_saml.links.menu.yml**

**onelogin.saml.module**

**onelogin_saml.permissions.yml**

**onelogin_saml.routing.yml**

**onelogin_saml.services.yml**