---
title: SLSP SporoPay
description: Endpoint SporoPay Slovenskej sporiteľne v payment-simulator - presmerovanie podpísané cez 3DES, polia, ktoré podpisuje spojené bodkočiarkou, a podpísaná odpoveď s účtom platiteľa.
---

# SLSP SporoPay

Zákazník platí z internet bankingu Slovenskej sporiteľne.

## `GET /payment/sporopay-des` {#get-payment-sporopay-des}

Kľúč: `Z3qY08EpvLlAAoMZdnyUdQ==` (base64, 3DES).

| Parameter | Povinný | Význam |
|---|---|---|
| `pu_predcislo` | áno | váš účet: predčíslie |
| `pu_cislo` | áno | váš účet: číslo účtu |
| `pu_kbanky` | áno | váš účet: kód banky |
| `suma` | áno | suma |
| `mena` | áno | mena, `EUR` |
| `vs` | áno | variabilný symbol |
| `ss` | áno | špecifický symbol |
| `url` | áno | návratová URL adresa |
| `param` | áno | váš parameter, vráti sa nezmenený |
| `sign1` | áno | podpis |

**Podpis požiadavky:** `pu_predcislo;pu_cislo;pu_kbanky;suma;mena;vs;ss;url;param`: polia **spojené znakom `;`**,
3DES ako v omnipay-slsp.

**Odpoveď:** `url?u_predcislo=123456&u_cislo=9876543210&u_kbanky=<pu_kbanky>&pu_predcislo=…&pu_cislo=…&pu_kbanky=…&suma=…&mena=…&vs=…&ss=…&url=…&param=…&result=OK|FAIL|TOUT&real=<result>&SIGN2=…`

`u_predcislo`, `u_cislo` a `u_kbanky` sú účet platiteľa: pevný účet s kódom vašej banky. `result` aj `real` obsahujú
zvolený výsledok. `SIGN2` je podpis nad všetkými týmito poľami spojenými znakom `;`:
`u_predcislo;u_cislo;u_kbanky;pu_predcislo;pu_cislo;pu_kbanky;suma;mena;vs;ss;url;param;result;real`. `url`,
`param` a `SIGN2` sú URL-enkódované.

Príklad: [SLSP SporoPay](/sk/examples/slsp-sporopay).
