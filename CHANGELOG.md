# Changelog
## Unreleased
### Changed
- Simplify module configuration
- Remove CMS export from the module

## [v1.6.5] - 2020.12.16
### Changed
- Upgrade Web Components to version 3.15.10

### Fixed
- Fix redirect URL in `controller_action_predispatch_*` observer
- Exclude category filter from URL on category pages
- Fix CMS export does not export pages assigned to all stores when exporting from specific store scope
- Fix CMS export does not export any page if nothing is selected in the export blacklist

## [v1.6.4] - 2020.10.15
### Changed
- Upgrade Web Components to version 3.15.8

### Fixed
- Make category optional in CategoryPath view model, preventing errors on Varnish ESI

## [v1.6.3] - 2020.09.23
### Fixed
- Magento CSP compatibility
- Filter non-printable characters in export feed

## [v1.6.2] - 2020.08.06
### Added
- Add information about checkout tracking to the README file
- Add troubleshooting section to the README file

### Changed
- Export `manufacturer` using a separate field model
- Upgrade Web Components to version 3.15.4

### Fixed
- Fix all GET params containing spaces when using the Proxy

## [v1.6.1] - 2020.05.11
### Fixed
- Fixed stack overflow error on IE11

## [v1.6.0] - 2020.04.30
### Added
- Track requests coming from internal IPs

### Changed
- Upgrade Web Components to version 3.14.1
- Deprecate `search-navigation.js`: search redirection is now implemented using a
  dedicated JS component on the relevant elements.

## [v1.5.1] - 2020.04.02
### Added
- Added RECOMMENDATION to available import type to be pushed after feed is uploaded
- Added Check FTP connection functionality in the module configuration

### Changed
- Upgrade Web Components to version 3.13.0

## [v1.5.0] - 2020.03.06
### Changed
- Improve extendability of product export (by @aptudock)

### Fixed
- Fixed cron feed export is now working correctly with multistore
- Prevent slider filter requests from being redirected to search result page

## [v1.4.2] - 2020.02.06
### Changed
- Upgrade Web Components to version 3.11.4

### Fixed
- Follow-up fixup to encoding parameter names to make them parsed correctly by `http_build_query`
- Enforce correct category path order in ViewModel
- Prevent merging and bundling of Web Components

## [v1.4.1] - 2020.01.28
### Fixed
- Remove typo in campaign template which prevents the rendering
- Fixed filterCategory not encoded correctly for categories with more words in name when using Proxy

## [v1.4.0] - 2019.12.17
### Changed
- Upgrade Web Components to v3.11.1
- Only offer _Add to cart_ button for products without variants

### Fixed
- Fix handling of REST calls via proxy
- Fix sorting of campaign blocks on search result page
- Fix tracking of products with options and submit correct master ID
- Prevent duplicate login tracking when customer data is reloaded

## [v1.3.4] - 2019.11.08
### Added
- Improve CI suite by introducing PHPMD checks

### Changed
- Upgrade Web Components to v3.9.0

### Fixed
- The ASN is now compatible with IE11
- Check current configuration before switching layout on category pages
- Prevent duplicate login tracking
- Remove wrapping link tag in suggest which was parsed by bots

## [v1.3.3] - 2019.10.21
### Changed
- Improve German language package
- Upgrade Web Components to v3.7.0

### Fixed
- Correctly merge communication params added via layout
- Prevent search request before redirecting to search result page

## [v1.3.2] - 2019.08.30
### Fixed
- Fix tracking model compatibility with Magento 2.2.*

## [v1.3.1] - 2019.08.19
### Fixed
- Fix log plugin compatibility with Magento 2.2.*

## [v1.3.0] - 2019.08.14
### Added
- Add missing product campaigns on product detail page

### Changed
- Remove `ff-navigation`
- Render category pages using Web Components
- Upgrade Web Components to v3.6.0

### Fixed
- Downgrade `magento/module-directory` to be compatible with Magento 2.2

## [v1.2.0] - 2019.07.24
### Added
- Add data providers for bundle and grouped products

### Changed
- Add push FACT-Finder import on cron feed export

### Fixed
- Allow empty multiselect fields in system configuration
- Base URL is now set, if needed, before redirect in search/navigation
- Currency code is now taken from store config
- Fix use-cache communication parameter value

## [v1.1.2] - 2019.06.28
### Changed
- Upgrade Web Components to v3.4.0

### Fixed
- Fix fatal PHP error which occurs on cron export

## [v1.1.1] - 2019.05.16
### Added
- Add logging to tracking exceptions

### Changed
- Upgrade Web Components to v3.3.1

### Fixed
- Skip tracking if the FACT-Finder integration is disabled in the backend

## [v1.1.0] - 2019.04.05
### Added
- Replace main navigation with the `<ff-navigation>` component

### Changed
- Upgrade Web Components to v3.1.1

### Fixed
- Fix export attribute selection in system config

## [v1.0.0] - 2019.03.18
### Added
- Add CMS export

### Changed
- Refactor product export
- Reorganize folder structure: source code is now found under `src`
- Upgrade Web Components version to 3.1.0
- Serve JS files using RequireJS

## [v0.9-beta.11] - 2019.03.01
### Changed
- Drop support for PHP 7.0
- Replace Communication helper with dedicated models
- Remove core controller rewrites and perform redirects using event observers
- Upgrade Web Components version to 3.0

### Removed
- ResultRefiner: Use DI or plugins to edit the JSON result

## [v0.9-beta.10] - 2019.02.04
### Added
- Added possibility to enable FACT-Finder server responses logging
- Added possibility to push data import to FACT-Finder. Previously, only suggest import was pushed
- Added `<ff-campaign-redirect>` web component support

### Changed
- Changed additional attributes source model to shows only products attributes
- Replaced all .css files with .less
- Removed hardcoded css files from page layout

### Fixed
- Fixed authorization in HTTP export

## [v0.9-beta.9] - 2019.01.09
### Fixed
- Add missing title to `factfinder_result_index` site
- Exclude FACT-Finder Web Components js script from minification

### Changed
- Remove 'keep-filters' parameter from module configuration
- Load stylesheets via LESS
- Drop support for PHP 5.6

### Added
- Added possibility to export additional attributes in separate columns
- Adds possibility to configure frequency of feed file generation by Cron
- Introduce coding standards based on the Magento ECG one

## [v0.9-beta.8] - 2018.12.17
### Fixed
- Correct type of `seo-prefix` parameter

### Changed
- Changed `ff-suggest` styles to differ from native Luma and Blank Magento theme
- Changed the `ff-communication` location from `header.panel` to `after.body.start`

### Added
- Allow user to set value of the `disable-single-hit-redirect` parameter  in module configuration

## [v0.9-beta.7] - 2018.11.29
### Fixed
- Prevents flashing of unstyled content (FOUC) on web browsers which natively doesn't support web components
- Gets correct "EAN" and "Manufacter" atribute values depending on attribute type set. For instance if selected attribute is a type of select, correct label is returned instead of its option identifier

### Changed
- Upgraded FACT-Finder WebComponents to version 1.2.14

## [v0.9-beta.6] - 2018.11.23
### Fixed
- removed the product limit on feed export
- returns simple product sku as master product number when it has a relation to nonexistent configurable product
- the price of a configurable product is now correctly exported as the cheapest price
- images urls are now exported correctly with respect the store, the feed file is exported from
- A missing ampersand character between parameters in http query is now added in checkout tracking

### Changed
- test connection functionality now uses data send from form with no need to save the configuration before checking the connection
- `ff-communication` component is now is now included in the server response when entering the page and just the parameters `sid` and `uid` are fetched dynamically
- change the Helper\Product methods access level to protected making this class easier to override

### Added
- allow user to choose which visibilities should be applied to collection filter
- divide product collection into batches in order to prevent memory exhaustion on product collection load

## [v0.9-beta.5] - 2018.10.31
### Fixed
- fix module structure and composer.json file allowing install module by composer
- `ff-communication` component is now download by Magento Customer Sections mechanism, which allowed to turn FPC on

### Added
- Feed Export: Export feed file is now available via separate link

[v1.6.5]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.6.5
[v1.6.4]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.6.4
[v1.6.3]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.6.3
[v1.6.2]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.6.2
[v1.6.1]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.6.1
[v1.6.0]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.6.0
[v1.5.1]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.5.1
[v1.5.0]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.5.0
[v1.4.2]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.4.2
[v1.4.1]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.4.1
[v1.4.0]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.4.0
[v1.3.4]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.3.4
[v1.3.3]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.3.3
[v1.3.2]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.3.2
[v1.3.1]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.3.1
[v1.3.0]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.3.0
[v1.2.0]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.2.0
[v1.1.2]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.1.2
[v1.1.1]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.1.1
[v1.1.0]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.1.0
[v1.0.0]:       https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v1.0.0
[v0.9-beta.11]: https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.11
[v0.9-beta.10]: https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.10
[v0.9-beta.9]:  https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.9
[v0.9-beta.8]:  https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.8
[v0.9-beta.7]:  https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.7
[v0.9-beta.6]:  https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.6
[v0.9-beta.5]:  https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.6
