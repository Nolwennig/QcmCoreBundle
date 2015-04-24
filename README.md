QcmCoreBundle
=============

[![Latest Stable Version](https://poser.pugx.org/avoo/qcm-core-bundle/v/stable)](https://packagist.org/packages/avoo/qcm-core-bundle)
[![License](https://poser.pugx.org/avoo/qcm-core-bundle/license)](https://packagist.org/packages/avoo/qcm-core-bundle)
[![Build Status](https://scrutinizer-ci.com/g/avoo/QcmCoreBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/avoo/QcmCoreBundle/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/avoo/QcmCoreBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/avoo/QcmCoreBundle/?branch=master)

The core bundle includes the basic functionalities of qcm based on [`SyliusResourceBundle`](https://github.com/Sylius/SyliusResourceBundle) concept and implement [`QcmComponent`](https://github.com/avoo/QcmComponents)

Installation
------------

Require [`avoo/qcm-core-bundle`](https://packagist.org/packages/avoo/qcm-core-bundle)
into your `composer.json` file:

``` json
{
    "require": {
        "avoo/qcm-core-bundle": "@dev-master"
    }
}
```

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new FOS\RestBundle\FOSRestBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle($this),
        new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
        new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
        new Qcm\Bundle\CoreBundle\QcmCoreBundle(),
    );
}
```

In `app/config.yml`

``` yml
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    - { resource: @QcmCoreBundle/Resources/config/core.yml }
```

Security default configuration
------------------------------

You can use the default register/login process:

In `app/config/routing.yml`

``` yml
qcm_core:
    prefix:   /
    resource: "@QcmCoreBundle/Resources/config/routing.yml"
```

Add default user provider, firewall and access control in `app/config/security.yml`:

``` yml
security:
    encoders:
        Qcm\Component\User\Model\UserInterface: sha512
    providers:
        qcm_corebundle:
            id: qcm_core.user_provider.username
    firewalls:
        login_firewall:
            pattern:    ^/security/login$
            anonymous:  ~
        secured_area:
            pattern:    ^/
            anonymous:  ~
            form_login:
                provider:            qcm_corebundle
                login_path:          qcm_core_security_login
                check_path:          qcm_core_security_login_check
                remember_me:         true
                default_target_path: you_homepage_route
            logout:
                path:   qcm_core_security_logout
    access_control:
        - { path: "^/security/login", roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: "^/", roles: ROLE_USER }
```

QCM Configuration
-----------------

You can override de default questionnaire configuration in `app/config/config.yml`

``` yml
qcm_core:
    website_name: Qcm Demo // The website name
    configuration:
        max_questions: 40 //Questions number max by questionnaire
        question_level: ["beginner", "senior", "jedi"] //Determine the level for each question
        answers_max: 5 //Number of answers max by question
        timeout: 2400 //Total time of the questionnaire (in seconds)
        time_per_question: 60 //Time per question, if you choose this value, the timeout will be disabled
```

Override service
----------------

For statistics class:

``` yml
qcm_core:
    service:
        statistics:
            class: MyBundle\Statistics\Class // For better compatibility extends the Model\QuestionnaireStatistics
            template: MyBundle\Answers\Template\Class // You need to implements the qcm TemplateInterface
```

Credits
-------

* Jérémy Jégou <jejeavo@gmail.com>

License
-------

This bundle is released under the MIT license. See the complete license in the bundle:

[License](https://github.com/avoo/QcmCoreBundle/blob/master/LICENSE)
