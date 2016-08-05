hiqdev/yii2-merchant
--------------------

## [Under development]

    - [33e91a1] 2016-07-13 rehideved [sol@hiqdev.com]
    - [e4fa5f9] 2016-07-13 csfixed [sol@hiqdev.com]
    - [0be4d2e] 2016-07-12 csfixed [sol@hiqdev.com]
    - [e367e59] 2016-06-30 Removed dependency on Err class [d.naumenko.a@gmail.com]
    - [9dc45c6] 2016-04-18 fixed comleting history [sol@hiqdev.com]
- Fixed eCoin and Paxum
    - [a0213a0] 2016-03-25 fixed History functions [sol@hiqdev.com]
    - [77afc6c] 2016-03-25 fixed eCoin to work, it makes no notify just return [sol@hiqdev.com]
    - [d16a1ec] 2016-03-25 redone history functions: - id argument [sol@hiqdev.com]
- Fixed minor issues
    - [7e9b380] 2016-03-11 Added translations [d.naumenko.a@gmail.com]
    - [99618c7] 2016-02-11 Minor fix. Add Box view [andreyklochok@gmail.com]
    - [4254b94] 2016-02-04 phpcsfixed [sol@hiqdev.com]
    - [c66c63c] 2016-02-03 + localizePage to build proper url to /merchant/pay/return [sol@hiqdev.com]
    - [bca7393] 2016-02-03 pay/deposit - added the message for a case when no payments methods available [d.naumenko.a@gmail.com]
- Fixed Travis build
    - [10c6932] 2016-01-30 fixed tests [sol@hiqdev.com]
    - [f8fa4aa] 2016-01-26 rehideved [sol@hiqdev.com]
    - [2616a6e] 2015-12-15 php-cs-fixed [sol@hiqdev.com]
    - [cae2de5] 2015-12-15 PayController - back ~> returnUrl, PHPDoc updated [d.naumenko.a@gmail.com]
    - [f394b3a] 2015-12-15 fixed tests [sol@hiqdev.com]
    - [f1ecb98] 2015-12-14 fixed travis: dont build 5.4, dont allow fail 7 [sol@hiqdev.com]
    - [72c57a8] 2015-12-14 trying hidev.phar [sol@hiqdev.com]
- Changed: redone with `php-merchant`
    - [d914e86] 2015-12-16 + finishUrl [sol@hiqdev.com]
    - [099a0a7] 2015-12-14 Action check-return implemented [d.naumenko.a@gmail.com]
    - [eb568af] 2015-12-14 php-cs-fixed [sol@hiqdev.com]
    - [e3e8e2a] 2015-12-14 Deposit - added validation rules for sum attribute [d.naumenko.a@gmail.com]
    - [15dc2d9] 2015-12-14 Module tidied up more [d.naumenko.a@gmail.com]
    - [25703a6] 2015-12-14 Module tidied up [d.naumenko.a@gmail.com]
    - [d66f028] 2015-12-11 + return/cancel method set to POST [sol@hiqdev.com]
    - [b57d0fb] 2015-12-11 renamed `transactionId` <- `internalid` [sol@hiqdev.com]
    - [1d0f00e] 2015-12-11 + return/cancel method set to POST [sol@hiqdev.com]
    - [cbba2e2] 2015-12-11 renamed `transactionId` <- `internalid` [sol@hiqdev.com]
    - [278543e] 2015-12-11 + internalid and history [sol@hiqdev.com]
    - [957f3aa] 2015-12-09 simplified merchant creating [sol@hiqdev.com]
    - [fce8fd4] 2015-12-08 finishing redoing to php-merchant [sol@hiqdev.com]
    - [ff73135] 2015-12-08 redone with php-merchant [sol@hiqdev.com]
- Fixed redirection to payment systems
    - [a2b0337] 2015-12-04 fixed bulidUrl to pass proper merchant [sol@hiqdev.com]
    - [294da33] 2015-12-03 fixed tests [sol@hiqdev.com]
    - [2befc31] 2015-12-03 fixed tests [sol@hiqdev.com]
    - [bc7177c] 2015-12-03 fixed redirection to payment systems [sol@hiqdev.com]
    - [9379eb2] 2015-12-03 + preparing data for Omnipay merchant unification [sol@hiqdev.com]
    - [247c544] 2015-12-03 fixed payment icons [sol@hiqdev.com]
- Added `renderDeposit` facility
    - [c366ba7] 2015-12-01 + `renderDeposit` facility [sol@hiqdev.com]
- Added use of Payment Icons
    - [79c0ce8] 2015-11-30 used Payment Icons [sol@hiqdev.com]
- Added tests and Travis CI
    - [995394c] 2015-11-30 improved tests [sol@hiqdev.com]
    - [4f3ceb1] 2015-11-25 added tests and Travis CI [sol@hiqdev.com]
    - [277f5e6] 2015-11-25 fixed Module::createMerchant to properly pass data [sol@hiqdev.com]
- Chnaged: redone with Omnipay
    - [bf5d311] 2015-11-23 Changed namespace to yii2-collection [d.naumenko.a@gmail.com]
    - [b6400ea] 2015-11-12 php-cs-fixed [sol@hiqdev.com]
    - [ef52743] 2015-11-12 php-cs-fixed [sol@hiqdev.com]
    - [024f9a1] 2015-11-12 still redoing to omnipay [sol@hiqdev.com]
    - [b941ed3] 2015-11-09 + require yii2-collection [sol@hiqdev.com]
    - [1d6f98b] 2015-11-09 started redoing to omnipay [sol@hiqdev.com]
    - [67aa249] 2015-11-09 improved package description [sol@hiqdev.com]
- Added basics
    - [ce5afe7] 2015-11-07 added basics [sol@hiqdev.com]
    - [cf31f89] 2015-10-30 php-cs-fixed [sol@hiqdev.com]
    - [1b82630] 2015-10-30 finished translation [sol@hiqdev.com]
    - [961f39d] 2015-10-30 fixed composer type to yii2-extension [sol@hiqdev.com]
    - [66b9ec7] 2015-10-30 fixed composer type to yii2-extension; adding translations [sol@hiqdev.com]
    - [124f377] 2015-10-26 adding deposit views, model and action [sol@hiqdev.com]
    - [234edfc] 2015-10-26 + pay button rendering with merchant [sol@hiqdev.com]
    - [0614bd9] 2015-10-22 improved with `_loadMerchant` [sol@hiqdev.com]
    - [26d44b4] 2015-10-21 php-cs-fixed [sol@hiqdev.com]
    - [f538d37] 2015-10-21 hideved [sol@hiqdev.com]
    - [dfadf30] 2015-10-21 inited [sol@hiqdev.com]

## [Development started] - 2015-10-21

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
