---
title: Použitie s Omnipay
description: Nasmerujte brány omnipay-tatra, omnipay-vub a omnipay-slsp na payment-simulator cez testMode a testHost a otestujte každú vetvu handlera vašej návratovej URL.
---

# Použitie s Omnipay

Simulátor vznikol pre brány `tomaj/omnipay-*`. V testovacom režime posielajú zákazníka namiesto do banky na `testHost`
(predvolene `https://platby.tomaj.sk`), na URL, ktorú simulátor očakáva:

| Balík | Brána | Testovací kľúč | URL simulátora |
|---|---|---|---|
| [omnipay-tatra](https://tomaj.github.io/omnipay-tatra/) | `TatraPay` | HMAC, AES alebo DES | `/payment/tatrapay-hmac`, `-aes256`, `-des` |
| | `CardPay` | HMAC, AES alebo DES | `/payment/cardpay-hmac`, `-aes256`, `-des`; `authorize()`: `/payment/cardpay-authorize-hmac`; `cancelAuthorize()`: `/payment/cardpay-cancel-purchase` |
| | `ComfortPay` | HMAC alebo AES | `/payment/comfortpay-hmac`, `-aes256`; webovú službu SOAP v testovacom režime odpovedá samotná knižnica |
| [omnipay-vub](https://github.com/tomaj/omnipay-vub) | `Eplatby` | e-platby | `/payment/eplatby-hmac` (odoslanie formulára POST) |
| [omnipay-slsp](https://github.com/tomaj/omnipay-slsp) | `SporoPay` | SporoPay | `/payment/sporopay-des` |

## Nastavenie brány {#configure-the-gateway}

<<< @/../examples/omnipay-tatra/run.php#config

`testMode` musí byť zapnutý: bez neho brána pošle zákazníka do skutočnej banky. `testHost` je základná URL simulátora
bez lomky na konci. [Testovacie kľúče](./configuration#test-keys) nahrádzajú váš skutočný `sharedSecret`; funguje
ľubovoľný `mid`.

## Celý priebeh platby {#round-trip}

[Príklad omnipay-tatra](/sk/examples/omnipay-tatra) to robí pre TatraPay, CardPay, ComfortPay a predautorizáciu
CardPay: začne platbu, otvorí stránku simulátora tak, ako by to urobil prehliadač zákazníka, a potom dokončí platbu
s každou z troch podpísaných odpovedí.

<<< @/../examples/omnipay-tatra/run.php#round-trip

`Omnipay::create($name, null, $request)` odovzdá bráne požiadavku zákazníka na návratovú URL; vo webovej aplikácii
ju `Omnipay::create($name)` prečíta z globálnych premenných PHP.

<<< @/../examples/omnipay-tatra/expected-output.txt{text}

Príklady [VÚB e-platby](/sk/examples/vub-eplatby) a [SLSP SporoPay](/sk/examples/slsp-sporopay) robia to isté
s omnipay-vub a omnipay-slsp.

## Čím sa líši od banky {#what-differs-from-the-bank}

| Brána | Rozdiel | Vplyv na vaše testy |
|---|---|---|
| TatraPay HMAC | v návrate nie je `TID` | ID transakcie je prázdne |
| CardPay | pevné `AC=111111`, `CC=1111********1111`, bez `TID` a `RC` | testujte iba to, že ich uložíte |
| CardPay predautorizácia | `TID=567898`, `CID=123456`, `AC=31232` iba s `TPAY=Y` a `RES=OK` | pošlite `tpay` `Y`, aby ste dostali ID transakcie pre `cancelAuthorize()` |
| ComfortPay | pevné ID karty `CID=111111`, `RC=123` | |
| webová služba ComfortPay | nesimuluje sa | omnipay-tatra ju v testovacom režime odpovedá lokálne (podľa parity ID) |
| všetky | `RES=TOUT` sa ponúka pre každú bránu | ako platbu v spracovaní ho hlási iba TatraPay; pri ostatných je to „neúspešná“ platba |

Skutočnú integráciu nasaďte až po jednej skutočnej platbe v prostredí banky.
