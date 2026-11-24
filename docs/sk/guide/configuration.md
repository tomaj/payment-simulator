---
title: Konfigurácia a testovacie kľúče
description: Pevné verejné testovacie kľúče každej simulovanej platobnej brány, konfiguračné súbory Nette, debug režim a logy payment-simulatora.
---

# Konfigurácia a testovacie kľúče

Nie je čo nastavovať: kľúče sú pevné a verejné a simulátor prijme ľubovoľný identifikátor obchodníka, sumu
aj variabilný symbol.

## Testovacie kľúče {#test-keys}

Týmito kľúčmi podpisujte svoje požiadavky. Sú to konštanty triedy `App\Presenters\PaymentPresenter`.

| Brána / algoritmus | Kľúč | Konštanta |
|---|---|---|
| Tatra banka HMAC (TatraPay, CardPay, ComfortPay, predautorizácia, dokončenie, Direct API) | `1` × 128 (hexadecimálne, 64 bajtov) | `HMAC_KEY` |
| Tatra banka AES (TatraPay, CardPay, ComfortPay) | `1` × 64 (hexadecimálne; AES-128 použije prvých 16 bajtov) | `AES_KEY` |
| Tatra banka DES (TatraPay, CardPay) | `11111111` | `DES_KEY` |
| VÚB e-platby HMAC | `1` × 64 (použitý ako text) | `EPLATBY_KEY` |
| SLSP SporoPay 3DES | `Z3qY08EpvLlAAoMZdnyUdQ==` (base64) | `SPOROPAY_KEY` |

V PHP: `str_repeat('1', 128)`, `str_repeat('1', 64)`, `'11111111'`.

Algoritmy sú tie, ktoré používajú banky: pre Tatra banku HMAC-SHA256 s hexadecimálne dekódovaným kľúčom, malé
hexadecimálne písmená; pre 64-znakový kľúč AES-ECB nad prvými 16 bajtmi SHA-1, veľké písmená; pre 8-znakový kľúč
DES-ECB nad SHA-1, veľké písmená; pre VÚB HMAC-SHA256 s kľúčom ako textom, veľké písmená; pre SporoPay 3DES. Každý
podpisovač pochádza z knižníc omnipay (`App\Model\Sign\LibrarySign`): podpisovače HMAC, AES a DES pre Tatra banku
z omnipay-tatra, e-platby z omnipay-vub a SporoPay z omnipay-slsp, takže simulátor a klienti nemôžu vypočítať podpis
rozdielne.

## Konfigurácia Nette {#nette-configuration}

| Súbor | Obsahuje |
|---|---|
| `app/config/common.neon` | mapovanie aplikácie, error presenter, session |
| `app/config/services.neon` | router |
| `app/config/local.neon` | načíta sa posledný, je prázdny: vaše lokálne úpravy |

Časové pásmo je `Europe/Prague`; `temp/` obsahuje cache Latte a DI, `log/` logy.

## Debug režim a logy {#debug-mode-and-logs}

Tracy rozhoduje podľa adresy klienta: požiadavky z localhostu bežia v debug režime a zobrazia Tracy bar a chybové
stránky; ostatné požiadavky dostanú produkčnú chybovú stránku a chyba sa zapíše do `log/`. Pri vstavanom PHP serveri
je vlastný log servera `log/php-server.log`.

## Porty a URL {#ports-and-url}

Vstavaný PHP server počúva tam, kde určuje `SIMULATOR_URL` (predvolene `http://127.0.0.1:8080`). V Dockeri je port
v `docker-compose.override.yml`; vnútri siete Compose je simulátor `http://paymentsandbox.test`. Simulátor skladá
svoje odkazy z vašej `RURL`, nikdy nie zo svojho hostu, takže funguje za ľubovoľným názvom hostu; iba 3-D Secure
formulár CardPay Direct API odosiela na host, na ktorý prišla požiadavka.
