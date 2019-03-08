# German readme
[Readme](README.de.md)

---

- [Introduction](#introduction)
- [Installation](#installation)
    - [Installing the Module from a compressed Archive](#installing-the-module-from-a-compressed-archive)
    - [Activating the Module](#activating-the-module)
- [Backend Configuration](#backend-configuration)
    - [General Settings](#general-settings)
    - [Activated Web-Components](#activated-web-components)
    - [Advanced Settings](#advanced-settings)
    - [Product Data Export](#product-data-export)
    - [CMS Export](#cms-export)
- [Web Component Integration](#web-component-integration)
    - [Overview of relevant Files](#overview-of-relevant-files)
    - [Searchbox Integration and Functions](#searchbox-integration-and-functions)
    - [Process of Data Transfer between Shop and FACT-Finder](#process-of-data-transfer-between-shop-and-fact-finder)


# Introduction

This document helps you integrate the FACT-Finder Web Components SDK into your Magento 2 Shop. In addition, it gives a concise overview of its primary functions. The first chapter “Installation” walks you through the suggested installation processes. The second chapter “Backend Configuration” explains the customisation options in the Magento 2 backend. The final chapter “Web Component Integration” describes how the web components interface with the shop system and how to customise them. 

---

# Installation

## Installing the Module from a compressed Archive

If you have received the FACT-Finder Module as a compressed archive, decompress it to the app/code folder of your Magento 2 Installation. The file path must be app/code/Omikron/Factfinder. You can now integrate the module into your shop. Proceed to the next step: “Activating the Module”.

## Installation by Composer
In order to allow Composer to install module, please add following line to your project composer.json on ```repositores``` segment:
```
    "repositories": [
          {
              "type": "git",
              "url": "https://github.com/FACT-Finder-Web-Components/magento2-module.git"
          }
    ]
```
Then, add a module declaration in ```require``` segment:
```
  "require": {
        "omikron/magento2-factfinder": "VERSION YOU WANT TO INSTALL"
    },
```




## Activating the Module

On your server, go to the bin/ directory of your Magento 2 installation and enter these commands in sequence:

```
php -f magento setup:upgrade
php -f magento setup:di:compile
php -f magento cache:flush
```

As a final step, check the modules activation with this command:

```
php -f magento module:status
```

The module should now appear in the upper list *List of enabled modules*. If this is not the case, activate the module with this command:

```
php -f magento module:enable Omikron_Factfinder
```

After that, you have to enter the above-mentioned string of commands again:

```
php -f magento setup:upgrade
php -f magento setup:di:compile
php -f magento cache:flush
```

Also, check in the Magento 2 backend "Stores → Configuration → Advanced → Advanced" if the module output is activated.

---

# Backend Configuration

Once the FACT-Finder module is activated, you can find the configurations page under "Stores → Configuration → Catalog → FACT-Finder". Here you can customise the connection to the FACT-Finder service. You can also activate and deactivate single web components, as well as access many additional settings.

## General Settings

At the top of the configurations page are the general settings. The information with which the shop connects to and authorises itself to the FACT-Finder Service are entered here. In the first line, activate your FACT-Finder integration. Before any changes become active, save them by clicking “Save Config”. In some cases, you need to manually empty the cache (*Configuration* and *Page Cache*).

Click the button “Test Connection” to check the connection to the FACT-Finder service. Please note the channel name needs to be entered correctly to establish a connection.

At the end of the general settings section is an option *Show 'Add to Cart' Button in Search Results*. Activate this option to add a button to the products displayed on the search result page, which directly adds that product to the shopping cart.
Warning: The product added to the cart is identified by the variable "MasterProductNumber". To allow this function to work correctly, the field "MasterProductNumber" must be imported to the FACT-Finder backend (on fact-finder.de).   


![General Settings](docs/assets/general-settings_en.jpg "General Settings")

## Activate Web Components

Here you can decide which web components are activated. Only active web components can be used and displayed in the shop.

 - **Suggestions** activates loading and displaying suggestions while search terms are entered into the search bar.
 - **Filter / ASN** activates the functions to narrow down and refine search results.
 - **Paging** activates paging through the returned search results.
 - **Sorting** activates a sorting function for returned search results.
 - **Breadcrumb** activates displaying the current position during a search. Can be refined with the **Filter / ASN** component. 
 - **Products per Page** activates an option to limit the number of displayed search results per page.
 - **Campaigns** displays your active FACT-Finder campaigns, e.g. advisor and feedback campaigns.
 - **Pushed Products** displays your pushed products campaigns. 

## Advanced Settings

Advanced Settings contains additional parameters used for the `ff-communications` web component. Each setting is set to a default value and has a short explanatory text attached.  

## Product Data Export

This option configures the connection with the FACT-Finder system via FTP. Shop data can be generated and transferred to FACT-Finder using FTP. FACT-Finder needs to be up to date on the product data, to ensure that components like the search work as intended.

Enter an FTP-server to which the CSV file is uploaded automatically. The URL needs to be entered without the protocol prefix (ftp://) and without the slash at the end.

The CSV file uses double quotes ‚“‘ for field enclosure and a semi-colon ‚;‘ as field delimiter.

For the option *Manufacturer*, choose the product attribute, which signifies the brand or manufacturer, the latter being the default field.

The *Select additional Attributes* option offers a multiple-choice list of attributes. Select all of those you want added to the CSV file.

Before starting the export by clicking *Generate Export File(s) now*, you need to commit all changes by clicking “Save Config”.
 
![Product Data Export](docs/assets/export-settings_en.jpg "Product Data Export")

## Cron configuration

You can  set the program to generate the product data export automatically. Activate the option *Generate Export Files(s) automatically* and the export is generated every day at 01:00 server time.

 `<schedule>0 1 * * *</schedule>` is a default value however You can define your own cron expression in the module configuration at `Cron Schedule` section.
  
![Cron Configuration](docs/assets/cron-configuration_en.jpg "Cron Configuration")

---
## CMS Export
You can export Your cms pages to FACT-Finder to present them in suggest results. You can specify whether You want to export cms pages content to separate channel, or using single channel, which You are using for standard products information export.
Both ways offer same functionality but in different ways and are described below.
### Configuration
![CMS Configuration](docs/assets/cms-configuration.png "CMS Configuration - using single channel")
* **Export Enabled** - determine if CMS content should be exported or not
* **Use separate channel** - determine if exported CMS content should be exported to standard channel, or to the different one.
If this option is set to "Yes", additional field "Channel" appears where You need provide the name of channel which will serve CMS suggest results.
* **Channel** - Channel name used for CMS export. This field appears only if *Use separate channel* is turned on.
* **Generate CMS Feed** - On button click all CMS data is exported and uploaded to configured FTP server
* **Pages Blacklist** - allow user to filter out pages, which should not be exported, for example "404 Not Found page" should not be visible at suggested records

Before You start exporting Your CMS content to FACT-Finder You need to prepare it for correctly serving this data to Your Magento application.
### Create new suggest type
At first You need to create a new suggest type named **cms**. I'ts because the new <ff-suggest-item> was added with type attribute equals to"cms"

``  <ff-suggest-item type="cms">``

![New Suggest Type](docs/assets/cms-suggest-type.png "FACT-Finder backend - new suggest type")

It's also  required to configure the return data of newly created suggest type. It's recommended to set return data as it's shown 
on screen, however You can also choose more fields to be returned. You should add page url to returned data to allow users directly 
reaching them from suggest component. If You want to present also page images, it's also worth adding them to returned data
![Suggest Type Return Data](docs/assets/cms-type-return-data.png "FACT-Finder cms suggest return")
Please note that each field needs to be correctly bind to html tag using access path same as in the FACT-Finder JSON object. 
The example below shows how to render page url

 ```<a href="{{attributes.PageLink}}" data-redirect="{{attributes.PageLink}}"'```

### Using Single Channel 
Using single channel is recommended way of integrate Your CMS with FACT-Finder, however it requires additional configuration in FACT-Finder backend. In order to prevent CMS pages appears in search results
You need to mark CMS related columns as no searchable (CMS results are displayed only in suggest component).
![Columns searchability](docs/assets/columns-searchability.png "CMS related columns mark as no searchable")

### Using Separate channel
This solution does not require You to make any changes to channel configuration regarding columns searchability, however  You need to create a new channel.
You need also to add new suggest type in Your newly created channel, as it is described in section [New Suggest Type](#create-new-suggest-type). Also You need to
set configuration option **Activate Enrichment feature** to value **Yes** in module configuration.

despite the fact that due to the use of separate channels, the products data will not be mixed up with CMS, and you do not need to perform any additional
operations to prevent CMS from appearing in the search results, this solution has one drawback. Two requests to FACT-Finder, will be performed in order to recieve full response: one for products and
one for CMS content. To merge them before returning to browser, module uses proxy to prepare final response from two separate FACT-Finder response. Because of that, the performance
of this solution will be lower, since all request are passed through Http server of Your Magento application.

![Example Suggest Result](docs/assets/example-suggest-cms.png "Example suggest result with cms")

---

# Web Component Integration

You can activate and deactivate any web components from the configurations page in the Magento 2 backend.

The HTML code for the web components can be found in this folder:

```
Omikron/Factfinder/view/frontend/templates/ff
```

All web components are saved here as templates to be customised. CSS definitions can be put into this folder: 
```
Omikron/Factfinder/view/frontend/web/css/ff
```

Most templates have a CSS file in there. Additionally, you have the following css file to which you can add, for example, higher-lever stylings:
 
```
Omikron/Factfinder/view/frontend/web/css/default.css
```

Warning: After changing static content like CSS styles, you need to restart the Magento 2 environment, for Magento to be able to find them. Use this command (run from the bin/ directory):

```
php -f magento setup:upgrade setup:static-content:deploy  
```

The PHP classes for the templates can be found in this folder:
 
```
Omikron/Factfinder/Block/FF
```

There you can write your own functions which can be called from the template if needed.

You can integrate the templates anywhere within your shop system. Magento 2 offers several ways to do so, e.g. layer definitions in XML. The templates can be layered as needed. As an example, the `ff-suggest` element was integrated into the `ff-searchbox` template for this SDK. Please check:

```php
Omikron/Factfinder/view/frontend/templates/ff/searchbox.phtml:7

<?php echo $this->getLayout()
->createBlock('Omikron\Factfinder\Block\FF\Suggest')
->setTemplate('Omikron_Factfinder::ff/suggest.phtml')
->toHtml(); ?>
```

## Overview of relevant Files

This is an overview over the files and their locations. These files are all important. Some of them have been explained in the preceding chapters, others are explained later.

```
.
`-- Omikron  
    `-- Factfinder  
        |-- Block  
        |   `-- FF  
        |       |-- ASN.php  
        |       |-- Breadcrumb.php  
        |       |-- Campaign.php  
        |       |-- Communication.php  
        |       |-- Paging.php  
        |       |-- ProductsPerPage.php  
        |       |-- PushedProductsCampaign.php  
        |       |-- RecordList.php  
        |       |-- Searchbox.php  
        |       |-- Sortbox.php  
        |       `-- Suggest.php
        |-- Helper  
        |   `-- ResultRefinder.php
        |-- etc
        |   `-- crontab.xml
        `-- view  
            `-- frontend  
                |-- layout  
                |   |-- default.xml  
                |   `-- factfinder_result_index.xml  
                |-- templates  
                |   `-- ff  
                |       |-- asn.phtml  
                |       |-- breadcrumb.phtml  
                |       |-- campaign.phtml  
                |       |-- communication.phtml  
                |       |-- paging.phtml  
                |       |-- products-per-page.phtml  
                |       |-- pushed-products-campaign.phtml  
                |       |-- record-list.phtml  
                |       |-- searchbox.phtml  
                |       |-- sortbox.phtml  
                |       `-- suggest.phtml  
                `-- web  
                    |-- css  
                    |   |-- ff  
                    |   |   |-- asn.css
                    |   |   |-- breadcrumb-trail.css
                    |   |   |-- paging.css
                    |   |   |-- products-per-page-dropdown.css
                    |   |   |-- record-list.css
                    |   |   |-- sortbox.css
                    |   `-- default.css 
                    `-- images 
                            `-- ... 
```


## Search Box Integration and Functions

As soon as the FACT-Finder-Integration is activated in the configuration, the search box web component is automatically activated. It replaces your standard search in Magenteo 2.

You can find the template for the FACT-Finder Search at:
```
Omikron/Factfinder/view/frontend/templates/ff/searchbox.phtml
```

Once you perform a search, you will automatically be redirected to a new and improved version of the Magento 2 search result page, which works with FACT-Finder data. Additionally, FACT-Finder enriches the new search result page’s URL with relevant data, like the search’s FACT-Finder channel or the search query string. The module’s source code contains the search results’ layout definition in this XML file:
```
Omikron/Factfinder/view/frontend/layout/factfinder_result_index.xml
```

Several templates are already integrated into this layout, among others `ff-record-list`, which displays the search results.
 
## Process of Data Transfer between Shop and FACT-Finder

Once a search query is sent, it does not immediately reach FACT-Finder, but is handed off to a specific controller, which hands the question to the FACT-Finder system, receives the answer, processes it and only then returns it to the frontend/web component.

The answer is always processed in this PHP class:

```
Omikron/Factfinder/Helper/ResultRefiner.php
```

There you can enrich or process the returned JSON-string using the method `refine($jsonString)`, before it is returned with `return $jsonString`.

![Communication Overview](docs/assets/communication-overview.png "Communication Overview")

