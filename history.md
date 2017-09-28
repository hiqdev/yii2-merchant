# hiqdev/yii2-merchant

## [0.1.0] - 2017-09-28

- Refactored transactions storage
    - [e4c0085] 2017-09-28 fixed tests [@hiqsol]
    - [9723af5] 2017-09-28 csfixed [@hiqsol]
    - [47379d4] 2017-07-31 Updated Module to use hostname instead of servername [@SilverFire]
    - [2ee5644] 2017-06-20 renamed `web` config <- hisite [@hiqsol]
    - [ee8ca60] 2017-06-20 renamed `hidev.yml` [@hiqsol]
    - [7e76575] 2017-03-31 Removed redundant check in Module::saveTransaction() [@SilverFire]
    - [80f5213] 2017-03-31 Fixed case when tansaction is not found [@SilverFire]
    - [60eecd6] 2017-03-31 Renamed `status` to `success` in Transaction table [@SilverFire]
    - [d2116fd] 2017-03-30 Refactored transactions storage [@SilverFire]
    - [9d55e1d] 2017-03-29 fixed getting available merchants in `pay/deposit` [@hiqsol]
    - [35326fe] 2017-03-28 Updated PayButton and Deposit form to follow php-merchant API changes [@SilverFire]
    - [99a4950] 2017-03-28 Enhanced PayController::actionRequest() to check merchant presence [@SilverFire]
    - [2fd82c1] 2017-03-27 redone `Module::getCollection` function to fetch collection every time again [@hiqsol]
    - [f443293] 2017-03-27 csfixed [@SilverFire]
    - [9c8aaea] 2017-03-27 Added PHPUnit 6 compatibility [@SilverFire]
    - [250ba1e] 2017-03-23 Removed commented code [@tafid]
    - [a47e48a] 2017-03-22 Added to obtain available merchants to Deposit action [@tafid]
    - [8e34c11] 2017-02-16 Added extra config-plugin hisite config [@tafid]
    - [0455e49] 2017-02-01 PayButton refactord to trigger local event instead of a global one [@SilverFire]
    - [2e5cbd8] 2017-02-01 Implemented button comment rendering [@SilverFire]
- Changed design
    - [56a4365] 2017-03-31 Removed box header from deposit view [@tafid]
    - [6e55765] 2017-03-31 Changed deposit and deposit-form views markup [@tafid]
    - [efaad49] 2017-03-27 Added responsive styles to merchant select [@tafid]
    - [37fbf3b] 2017-03-27 Removed inline css for deposit view and placed it to ccs file, call it from PayButton widget [@tafid]
    - [b338802] 2017-03-27 Changed box size in deposit view [@tafid]
    - [7a14b5d] 2017-03-22 Changed design [@tafid]
    - [937be79] 2017-02-16 Added extra css to PayButton marckup [@tafid]
    - [820df72] 2017-02-16 Added class clearly [@tafid]
    - [4ed4be1] 2017-02-16 Added Theme Manager settings [@tafid]
- Added commision support
    - [4f1c26c] 2017-01-20 Added commission support [@SilverFire]
- Fixed translations
    - [e1a9c7e] 2017-03-27 added i18n hisite config [@hiqsol]
    - [c17c7e0] 2017-03-27 Updated translations, fixed presentation logic [@SilverFire]
    - [a20b8c2] 2017-03-22 translations [@tafid]
    - [c8145a4] 2017-01-13 Translations updated [@SilverFire]
- Fixed eCoin and Paxum
    - [33e91a1] 2016-07-13 rehideved [@hiqsol]
    - [9dc45c6] 2016-04-18 fixed comleting history [@hiqsol]
    - [a0213a0] 2016-03-25 fixed History functions [@hiqsol]
    - [77afc6c] 2016-03-25 fixed eCoin to work, it makes no notify just return [@hiqsol]
- Fixed bugs
    - [87b761f] 2016-08-05 changed bumping to use `chkipper` [@hiqsol]
    - [e4fa5f9] 2016-07-13 csfixed [@hiqsol]
    - [0be4d2e] 2016-07-12 csfixed [@hiqsol]
    - [e367e59] 2016-06-30 Removed dependency on Err class [@SilverFire]
    - [d16a1ec] 2016-03-25 redone history functions: - id argument [@hiqsol]
    - [7e9b380] 2016-03-11 Added translations [@SilverFire]
    - [99618c7] 2016-02-11 Minor fix. Add Box view [@tafid]
    - [4254b94] 2016-02-04 phpcsfixed [@hiqsol]
    - [c66c63c] 2016-02-03 + localizePage to build proper url to /merchant/pay/return [@hiqsol]
    - [bca7393] 2016-02-03 pay/deposit - added the message for a case when no payments methods available [@SilverFire]
- Fixed Travis build
    - [10c6932] 2016-01-30 fixed tests [@hiqsol]
    - [f8fa4aa] 2016-01-26 rehideved [@hiqsol]
    - [2616a6e] 2015-12-15 php-cs-fixed [@hiqsol]
    - [cae2de5] 2015-12-15 PayController - back ~> returnUrl, PHPDoc updated [@SilverFire]
    - [f394b3a] 2015-12-15 fixed tests [@hiqsol]
    - [f1ecb98] 2015-12-14 fixed travis: dont build 5.4, dont allow fail 7 [@hiqsol]
    - [72c57a8] 2015-12-14 trying hidev.phar [@hiqsol]
- Changed: redone with `php-merchant`
    - [d914e86] 2015-12-16 + finishUrl [@hiqsol]
    - [099a0a7] 2015-12-14 Action check-return implemented [@SilverFire]
    - [eb568af] 2015-12-14 php-cs-fixed [@hiqsol]
    - [e3e8e2a] 2015-12-14 Deposit - added validation rules for sum attribute [@SilverFire]
    - [15dc2d9] 2015-12-14 Module tidied up more [@SilverFire]
    - [25703a6] 2015-12-14 Module tidied up [@SilverFire]
    - [d66f028] 2015-12-11 + return/cancel method set to POST [@hiqsol]
    - [b57d0fb] 2015-12-11 renamed `transactionId` <- `internalid` [@hiqsol]
    - [1d0f00e] 2015-12-11 + return/cancel method set to POST [@hiqsol]
    - [cbba2e2] 2015-12-11 renamed `transactionId` <- `internalid` [@hiqsol]
    - [278543e] 2015-12-11 + internalid and history [@hiqsol]
    - [957f3aa] 2015-12-09 simplified merchant creating [@hiqsol]
    - [fce8fd4] 2015-12-08 finishing redoing to php-merchant [@hiqsol]
    - [ff73135] 2015-12-08 redone with php-merchant [@hiqsol]
- Fixed redirection to payment systems
    - [a2b0337] 2015-12-04 fixed bulidUrl to pass proper merchant [@hiqsol]
    - [294da33] 2015-12-03 fixed tests [@hiqsol]
    - [2befc31] 2015-12-03 fixed tests [@hiqsol]
    - [bc7177c] 2015-12-03 fixed redirection to payment systems [@hiqsol]
    - [9379eb2] 2015-12-03 + preparing data for Omnipay merchant unification [@hiqsol]
    - [247c544] 2015-12-03 fixed payment icons [@hiqsol]
- Added `renderDeposit` facility
    - [c366ba7] 2015-12-01 + `renderDeposit` facility [@hiqsol]
- Added use of Payment Icons
    - [79c0ce8] 2015-11-30 used Payment Icons [@hiqsol]
- Added tests and Travis CI
    - [995394c] 2015-11-30 improved tests [@hiqsol]
    - [4f3ceb1] 2015-11-25 added tests and Travis CI [@hiqsol]
    - [277f5e6] 2015-11-25 fixed Module::createMerchant to properly pass data [@hiqsol]
- Chnaged: redone with Omnipay
    - [bf5d311] 2015-11-23 Changed namespace to yii2-collection [@SilverFire]
    - [b6400ea] 2015-11-12 php-cs-fixed [@hiqsol]
    - [ef52743] 2015-11-12 php-cs-fixed [@hiqsol]
    - [024f9a1] 2015-11-12 still redoing to omnipay [@hiqsol]
    - [b941ed3] 2015-11-09 + require yii2-collection [@hiqsol]
    - [1d6f98b] 2015-11-09 started redoing to omnipay [@hiqsol]
    - [67aa249] 2015-11-09 improved package description [@hiqsol]
- Added basics
    - [ce5afe7] 2015-11-07 added basics [@hiqsol]
    - [cf31f89] 2015-10-30 php-cs-fixed [@hiqsol]
    - [1b82630] 2015-10-30 finished translation [@hiqsol]
    - [961f39d] 2015-10-30 fixed composer type to yii2-extension [@hiqsol]
    - [66b9ec7] 2015-10-30 fixed composer type to yii2-extension; adding translations [@hiqsol]
    - [124f377] 2015-10-26 adding deposit views, model and action [@hiqsol]
    - [234edfc] 2015-10-26 + pay button rendering with merchant [@hiqsol]
    - [0614bd9] 2015-10-22 improved with `_loadMerchant` [@hiqsol]
    - [26d44b4] 2015-10-21 php-cs-fixed [@hiqsol]
    - [f538d37] 2015-10-21 hideved [@hiqsol]
    - [dfadf30] 2015-10-21 inited [@hiqsol]

## [Development started] - 2015-10-21

[@hiqsol]: https://github.com/hiqsol
[sol@hiqdev.com]: https://github.com/hiqsol
[@SilverFire]: https://github.com/SilverFire
[d.naumenko.a@gmail.com]: https://github.com/SilverFire
[@tafid]: https://github.com/tafid
[andreyklochok@gmail.com]: https://github.com/tafid
[@BladeRoot]: https://github.com/BladeRoot
[bladeroot@gmail.com]: https://github.com/BladeRoot
[a0213a0]: https://github.com/hiqdev/yii2-merchant/commit/a0213a0
[77afc6c]: https://github.com/hiqdev/yii2-merchant/commit/77afc6c
[d16a1ec]: https://github.com/hiqdev/yii2-merchant/commit/d16a1ec
[7e9b380]: https://github.com/hiqdev/yii2-merchant/commit/7e9b380
[99618c7]: https://github.com/hiqdev/yii2-merchant/commit/99618c7
[4254b94]: https://github.com/hiqdev/yii2-merchant/commit/4254b94
[c66c63c]: https://github.com/hiqdev/yii2-merchant/commit/c66c63c
[bca7393]: https://github.com/hiqdev/yii2-merchant/commit/bca7393
[10c6932]: https://github.com/hiqdev/yii2-merchant/commit/10c6932
[f8fa4aa]: https://github.com/hiqdev/yii2-merchant/commit/f8fa4aa
[2616a6e]: https://github.com/hiqdev/yii2-merchant/commit/2616a6e
[cae2de5]: https://github.com/hiqdev/yii2-merchant/commit/cae2de5
[f394b3a]: https://github.com/hiqdev/yii2-merchant/commit/f394b3a
[f1ecb98]: https://github.com/hiqdev/yii2-merchant/commit/f1ecb98
[72c57a8]: https://github.com/hiqdev/yii2-merchant/commit/72c57a8
[d914e86]: https://github.com/hiqdev/yii2-merchant/commit/d914e86
[099a0a7]: https://github.com/hiqdev/yii2-merchant/commit/099a0a7
[eb568af]: https://github.com/hiqdev/yii2-merchant/commit/eb568af
[e3e8e2a]: https://github.com/hiqdev/yii2-merchant/commit/e3e8e2a
[15dc2d9]: https://github.com/hiqdev/yii2-merchant/commit/15dc2d9
[25703a6]: https://github.com/hiqdev/yii2-merchant/commit/25703a6
[d66f028]: https://github.com/hiqdev/yii2-merchant/commit/d66f028
[b57d0fb]: https://github.com/hiqdev/yii2-merchant/commit/b57d0fb
[1d0f00e]: https://github.com/hiqdev/yii2-merchant/commit/1d0f00e
[cbba2e2]: https://github.com/hiqdev/yii2-merchant/commit/cbba2e2
[278543e]: https://github.com/hiqdev/yii2-merchant/commit/278543e
[957f3aa]: https://github.com/hiqdev/yii2-merchant/commit/957f3aa
[fce8fd4]: https://github.com/hiqdev/yii2-merchant/commit/fce8fd4
[ff73135]: https://github.com/hiqdev/yii2-merchant/commit/ff73135
[a2b0337]: https://github.com/hiqdev/yii2-merchant/commit/a2b0337
[294da33]: https://github.com/hiqdev/yii2-merchant/commit/294da33
[2befc31]: https://github.com/hiqdev/yii2-merchant/commit/2befc31
[bc7177c]: https://github.com/hiqdev/yii2-merchant/commit/bc7177c
[9379eb2]: https://github.com/hiqdev/yii2-merchant/commit/9379eb2
[247c544]: https://github.com/hiqdev/yii2-merchant/commit/247c544
[c366ba7]: https://github.com/hiqdev/yii2-merchant/commit/c366ba7
[79c0ce8]: https://github.com/hiqdev/yii2-merchant/commit/79c0ce8
[995394c]: https://github.com/hiqdev/yii2-merchant/commit/995394c
[4f3ceb1]: https://github.com/hiqdev/yii2-merchant/commit/4f3ceb1
[277f5e6]: https://github.com/hiqdev/yii2-merchant/commit/277f5e6
[bf5d311]: https://github.com/hiqdev/yii2-merchant/commit/bf5d311
[b6400ea]: https://github.com/hiqdev/yii2-merchant/commit/b6400ea
[ef52743]: https://github.com/hiqdev/yii2-merchant/commit/ef52743
[024f9a1]: https://github.com/hiqdev/yii2-merchant/commit/024f9a1
[b941ed3]: https://github.com/hiqdev/yii2-merchant/commit/b941ed3
[1d6f98b]: https://github.com/hiqdev/yii2-merchant/commit/1d6f98b
[67aa249]: https://github.com/hiqdev/yii2-merchant/commit/67aa249
[ce5afe7]: https://github.com/hiqdev/yii2-merchant/commit/ce5afe7
[cf31f89]: https://github.com/hiqdev/yii2-merchant/commit/cf31f89
[1b82630]: https://github.com/hiqdev/yii2-merchant/commit/1b82630
[961f39d]: https://github.com/hiqdev/yii2-merchant/commit/961f39d
[66b9ec7]: https://github.com/hiqdev/yii2-merchant/commit/66b9ec7
[124f377]: https://github.com/hiqdev/yii2-merchant/commit/124f377
[234edfc]: https://github.com/hiqdev/yii2-merchant/commit/234edfc
[0614bd9]: https://github.com/hiqdev/yii2-merchant/commit/0614bd9
[26d44b4]: https://github.com/hiqdev/yii2-merchant/commit/26d44b4
[f538d37]: https://github.com/hiqdev/yii2-merchant/commit/f538d37
[dfadf30]: https://github.com/hiqdev/yii2-merchant/commit/dfadf30
[33e91a1]: https://github.com/hiqdev/yii2-merchant/commit/33e91a1
[e4fa5f9]: https://github.com/hiqdev/yii2-merchant/commit/e4fa5f9
[0be4d2e]: https://github.com/hiqdev/yii2-merchant/commit/0be4d2e
[e367e59]: https://github.com/hiqdev/yii2-merchant/commit/e367e59
[9dc45c6]: https://github.com/hiqdev/yii2-merchant/commit/9dc45c6
[87b761f]: https://github.com/hiqdev/yii2-merchant/commit/87b761f
[e4c0085]: https://github.com/hiqdev/yii2-merchant/commit/e4c0085
[9723af5]: https://github.com/hiqdev/yii2-merchant/commit/9723af5
[47379d4]: https://github.com/hiqdev/yii2-merchant/commit/47379d4
[2ee5644]: https://github.com/hiqdev/yii2-merchant/commit/2ee5644
[ee8ca60]: https://github.com/hiqdev/yii2-merchant/commit/ee8ca60
[56a4365]: https://github.com/hiqdev/yii2-merchant/commit/56a4365
[6e55765]: https://github.com/hiqdev/yii2-merchant/commit/6e55765
[7e76575]: https://github.com/hiqdev/yii2-merchant/commit/7e76575
[80f5213]: https://github.com/hiqdev/yii2-merchant/commit/80f5213
[60eecd6]: https://github.com/hiqdev/yii2-merchant/commit/60eecd6
[d2116fd]: https://github.com/hiqdev/yii2-merchant/commit/d2116fd
[9d55e1d]: https://github.com/hiqdev/yii2-merchant/commit/9d55e1d
[35326fe]: https://github.com/hiqdev/yii2-merchant/commit/35326fe
[99a4950]: https://github.com/hiqdev/yii2-merchant/commit/99a4950
[2fd82c1]: https://github.com/hiqdev/yii2-merchant/commit/2fd82c1
[e1a9c7e]: https://github.com/hiqdev/yii2-merchant/commit/e1a9c7e
[c17c7e0]: https://github.com/hiqdev/yii2-merchant/commit/c17c7e0
[f443293]: https://github.com/hiqdev/yii2-merchant/commit/f443293
[9c8aaea]: https://github.com/hiqdev/yii2-merchant/commit/9c8aaea
[efaad49]: https://github.com/hiqdev/yii2-merchant/commit/efaad49
[b338802]: https://github.com/hiqdev/yii2-merchant/commit/b338802
[37fbf3b]: https://github.com/hiqdev/yii2-merchant/commit/37fbf3b
[250ba1e]: https://github.com/hiqdev/yii2-merchant/commit/250ba1e
[a47e48a]: https://github.com/hiqdev/yii2-merchant/commit/a47e48a
[7a14b5d]: https://github.com/hiqdev/yii2-merchant/commit/7a14b5d
[a20b8c2]: https://github.com/hiqdev/yii2-merchant/commit/a20b8c2
[937be79]: https://github.com/hiqdev/yii2-merchant/commit/937be79
[820df72]: https://github.com/hiqdev/yii2-merchant/commit/820df72
[8e34c11]: https://github.com/hiqdev/yii2-merchant/commit/8e34c11
[4ed4be1]: https://github.com/hiqdev/yii2-merchant/commit/4ed4be1
[0455e49]: https://github.com/hiqdev/yii2-merchant/commit/0455e49
[2e5cbd8]: https://github.com/hiqdev/yii2-merchant/commit/2e5cbd8
[4f1c26c]: https://github.com/hiqdev/yii2-merchant/commit/4f1c26c
[c8145a4]: https://github.com/hiqdev/yii2-merchant/commit/c8145a4
[Under development]: https://github.com/hiqdev/yii2-merchant/releases
[0.1.0]: https://github.com/hiqdev/yii2-merchant/releases/tag/0.1.0
