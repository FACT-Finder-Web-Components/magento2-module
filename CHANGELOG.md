# Changelog
## [v0.9-beta.11] - 2019.03.01
### Changed
- Drop support for PHP 7.0
- Replace Communication helper with dedicated models
- Remove core controller rewrites and perform redirects using event observers
- Upgraded Web Components version to 3.0

### Removed
- ResultRefiner: Use DI or plugins to edit the JSON result

## [v0.9-beta.10] - 2019.02.04
### Added
- Added possibility to enable FACT-Finder server responses logging
- Added possibility to push data import to FACT-Finder. Previously, only suggest import was pushed
- Added <ff-redirect campaign> web component support

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

[v0.9-beta.11]: https://github.com/FACT-Finder-Web-Components/magento2-module/compare/v0.9-beta.10...v0.9-beta.11
[v0.9-beta.10]: https://github.com/FACT-Finder-Web-Components/magento2-module/compare/v0.9-beta.9...v0.9-beta.10
[v0.9-beta.9]: https://github.com/FACT-Finder-Web-Components/magento2-module/compare/v0.9-beta.8...v0.9-beta.9
[v0.9-beta.8]: https://github.com/FACT-Finder-Web-Components/magento2-module/compare/v0.9-beta.7...v0.9-beta.8
[v0.9-beta.7]: https://github.com/FACT-Finder-Web-Components/magento2-module/compare/v0.9-beta.6...v0.9-beta.7
[v0.9-beta.6]: https://github.com/FACT-Finder-Web-Components/magento2-module/compare/v0.9-beta.5...v0.9-beta.6
[v0.9-beta.5]: https://github.com/FACT-Finder-Web-Components/magento2-module/releases/tag/v0.9-beta.5
