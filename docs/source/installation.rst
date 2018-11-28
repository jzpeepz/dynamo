Installation
============

Install via Composer::

    composer require jzpeepz/dynamo

Include the service provider in your config/app.php::

    Jzpeepz\Dynamo\DynamoServiceProvider::class

Publish the Dynamo config file::

    php artisan vendor:publish --tag=dynamo

.. note:: NOTE: If using a local disk for uploading, be sure to symlink it to your public directory and provide the proper path in the config file.

If you'd like, the Github repo for Dynamo is |github|.

.. |github| raw:: html

   <a href="https://github.com/jzpeepz/dynamo" target="_blank">here</a>



Configuration
^^^^^^^^^^^^^

You can edit the configuration of Dynamo at:

vendor->jzpeepz->dynamo->src->config->dynamo.php.

.. image:: images/Config.png
    :align: center

Storage disk to use to store uploaded files.

Path within the storage disk to store the uploaded files. This is also the directory within the public directory to which the storage directory is linked.

Prefix to add to all Dynamo routes.

Layout to use with Dynamo views.

Controller Namespace and path tells Dynamo where you want you're controllers to be auto-generated. TODO
