---
title: Banky
description: Platobné brány, ktoré payment-simulator simuluje - TatraPay, CardPay, ComfortPay a CardPay Direct API Tatra banky, e-platby VÚB a SporoPay SLSP - a ich endpointy.
---

# Banky

| Banka | Platobná brána | Čo robí zákazník | Endpointy |
|---|---|---|---|
| Tatra banka | [TatraPay](./tatrapay) | platí z internet bankingu | HMAC, AES, DES |
| Tatra banka | [CardPay](./cardpay) | platí kartou; predautorizácia, dokončenie, zrušenie, vrátenie platby | HMAC, AES, DES, predautorizácia, dokončenie |
| Tatra banka | [ComfortPay](./comfortpay) | platí kartou a zaregistruje ju na neskoršie platby | HMAC, AES |
| Tatra banka | [CardPay Direct API](./cardpay-direct-api) | platí cez Apple Pay alebo Google Pay priamo na vašej stránke | JSON API, stránka 3-D Secure |
| VÚB | [e-platby](./vub-eplatby) | platí z internet bankingu VÚB | HMAC (formulár POST) |
| SLSP | [SporoPay](./slsp-sporopay) | platí z internet bankingu Slovenskej sporiteľne | 3DES |

Všetky endpointy s presmerovaním fungujú rovnako, pozrite [Ako to funguje](/sk/guide/how-it-works): overia podpis,
zobrazia **Sign OK**, **Wrong sign** alebo **Missing parameters** a pri správnom podpise ponúknu odkazy `continue ok`,
`continue fail` a `continue timeout` späť na návratovú URL, podpísané tak, ako ich podpisuje banka.

Na stránkach bánk sú nepovinné parametre napísané *kurzívou*: môžu chýbať a chýbajúce alebo prázdne nepovinné pole
do podpisovaného reťazca nič nepridá. Podpisované polia sa spájajú v uvedenom poradí bez oddeľovačov, ak nie je
uvedené inak. Všetky endpointy na jednej stránke: [Endpointy](/sk/reference/endpoints).
