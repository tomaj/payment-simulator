---
title: SLSP SporoPay
description: The SLSP SporoPay endpoint of payment-simulator - a 3DES-signed redirect, the fields it signs with semicolons, and the signed return with the payer's account.
---

# SLSP SporoPay

The customer pays from Slovenská sporiteľňa internet banking.

## `GET /payment/sporopay-des`

Key: `Z3qY08EpvLlAAoMZdnyUdQ==` (base64, 3DES).

| Parameter | Required | Meaning |
|---|---|---|
| `pu_predcislo` | yes | your account: prefix |
| `pu_cislo` | yes | your account: number |
| `pu_kbanky` | yes | your account: bank code |
| `suma` | yes | amount |
| `mena` | yes | currency, `EUR` |
| `vs` | yes | variable symbol |
| `ss` | yes | specific symbol |
| `url` | yes | return URL |
| `param` | yes | your parameter, returned unchanged |
| `sign1` | yes | the signature |

**Signed request:** `pu_predcislo;pu_cislo;pu_kbanky;suma;mena;vs;ss;url;param`: the fields **joined with `;`**,
3DES as in omnipay-slsp.

**Return:** `url?u_predcislo=123456&u_cislo=9876543210&u_kbanky=<pu_kbanky>&pu_predcislo=…&pu_cislo=…&pu_kbanky=…&suma=…&mena=…&vs=…&ss=…&url=…&param=…&result=OK|FAIL|TOUT&real=<result>&SIGN2=…`

`u_predcislo`, `u_cislo` and `u_kbanky` are the payer's account: a fixed account at your bank code. `result` and
`real` are both the chosen result. `SIGN2` is signed over all of them joined with `;`:
`u_predcislo;u_cislo;u_kbanky;pu_predcislo;pu_cislo;pu_kbanky;suma;mena;vs;ss;url;param;result;real`. `url`,
`param` and `SIGN2` are URL-encoded.

Example: [SLSP SporoPay](/examples/slsp-sporopay).
