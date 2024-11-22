# ![Limbarine] LIMBAS

[Limbarine]: Limbarine.png "Limbarine"

About Limbas
============

Limbas is a low-code database framework for creating database-driven business applications.
As a graphical database front-end, it enables the efficient processing of data stocks and the flexible development of comfortable database applications with hardly any programming knowledge. LIMBAS is brought to you by the LIMBAS team at LIMBAS GmbH.

Supported databases
-------------------

* PostgreSQL
* MySQL
* MSSQL
* SAP MaxDB
* Oracle

Requirements
------------

* Linux server
* Apache
* PHP8+
* PDO / unixODBC

Online Resources
----------------

If you have any questions please take advantage of one of the following online resources:
* [Documentation](http://www.limbas.org/)
* [Free Demoserver](https://www.limbas.com/en/Service___Support/Demoserver/)
* [Website](https://www.limbas.com/en/)
* [Latest Release](https://github.com/limbas/limbas/releases/latest)
* [More Downloads](https://sourceforge.net/projects/limbas/files/)
* [Forum](http://sourceforge.net/projects/limbas/)
* [Docker Hub](https://hub.docker.com/r/limbas/limbas)

License
-------

The Limbas framework is licensed under the [GNU GPL v2.0](https://opensource.org/licenses/GPL-2.0).

Installation
------------

A detailed description of how to perform a new INSTALLATION is provided at [Limbas Documentation](https://limbas.org/en/documentation/get-started-en/)

### Web installer

The [web installer](https://github.com/limbas/web-installer) is the easiest way to install Limbas on a webspace (recommended for non developers).\
[Download here](https://github.com/limbas/web-installer/releases)\
It downloads the latest Limbas package and unpacks it with the right permissions and the right user account.\
Finally, you will be redirected to the Limbas installer. Internet connection required.

After installation, change the domain root to the "public" directory.

### Installation with docker

Limbas can be started via Docker with just a few clicks.\
For details see [Limbas Docker](https://github.com/limbas/limbas-docker)

### Installation from release package

1. Upload the openlimbas_X.X.tar.gz package to an empty directory on your server and extract it.
2. Be sure your domain root points on the directory "public".
3. Open Limbas in your browser. It will redirect you to the installation page.
4. Follow the steps on the installation page.
5. Once the setup is completed you can sign in with the default credentials

UPDATE
------

Make sure to **back up** the directories before updating.\
To update an existing Limbas system, replace the "limbas_src", "vendor" and "assets" (in "public") folders.
