#!/usr/bin/env python3
"""Writes the logo, favicon, hero illustration, diagram and social card SVGs into docs/public.

The hero and the diagram are written in English and in Slovak (`-sk` files, used by the pages under docs/sk/).

Each drawing exists once, with colour tokens; the light and dark files only swap the palette. Run it after a change,
then `node scripts/og-image.mjs` for the PNG icons, og.png and og-sk.png (or `npm run images` for both).

payment-simulator belongs to the visual family of the omnipay-* packages (a rounded square with a payment card, see
omnipay-tatra's docs/scripts/illustrations.py). It simulates several banks, so its square is a neutral slate and the
card's stripe carries the colours of the simulated banks: Tatra banka yellow, VÚB blue, SLSP red, and green.
"""

from __future__ import annotations

from pathlib import Path

PUBLIC = Path(__file__).resolve().parents[1] / "public"
NAME = "payment-simulator"
BANKS = ["#ffd200", "#0062ae", "#e30613", "#16a34a"]

LIGHT = {
    "square": "#1e293b", "card": "#f8fafc",
    "ink": "#0f172a", "muted": "#475569", "panel": "#ffffff", "line": "#cbd5e1", "bar": "#f1f5f9",
    "accent": "#0891b2", "on_accent": "#ffffff", "ok": "#15803d", "ok_soft": "#ecfdf3",
    "bad": "#b91c1c", "bad_soft": "#fef2f2", "warn": "#b45309", "warn_soft": "#fffbeb",
    "arrow": "#0e7490", "bg": "#f0f9ff",
}
DARK = {
    "square": "#e2e8f0", "card": "#1e293b",
    "ink": "#f1f5f9", "muted": "#94a3b8", "panel": "#1e1e24", "line": "#3f3f46", "bar": "#27272a",
    "accent": "#22d3ee", "on_accent": "#0f172a", "ok": "#4ade80", "ok_soft": "#11261a",
    "bad": "#f87171", "bad_soft": "#2a1515", "warn": "#fbbf24", "warn_soft": "#2a2210",
    "arrow": "#22d3ee", "bg": "#1b1b1f",
}

FONT = "font-family=\"Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif\""
MONO = "font-family=\"ui-monospace, SFMono-Regular, Menlo, Consolas, monospace\""


def mark(c: dict[str, str]) -> str:
    """The family mark: square, card, stripe (here in the banks' colours), a line and a check mark."""
    stripe = "".join(
        f'<rect x="{11 + i * 10.5:g}" y="23" width="10.5" height="6" fill="{color}"/>' for i, color in enumerate(BANKS)
    )
    return f"""<rect x="2" y="2" width="60" height="60" rx="14" fill="{c['square']}"/>
  <rect x="11" y="17" width="42" height="30" rx="5" fill="{c['card']}"/>
  {stripe}
  <path d="M17 39h11" fill="none" stroke="{c['square']}" stroke-width="3.5" stroke-linecap="round"/>
  <path d="M37 39.5l4.5 4 8-9" fill="none" stroke="{c['square']}" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>"""


def logo(c: dict[str, str]) -> str:
    return f"""<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64" role="img" aria-label="{NAME}">
  <title>{NAME}</title>
  {mark(c)}
</svg>
"""


def check(x: float, y: float, color: str, width: float = 2.5) -> str:
    return f'<path d="M{x} {y}l4 4 8-9" fill="none" stroke="{color}" stroke-width="{width}" stroke-linecap="round" stroke-linejoin="round"/>'


def hero_body(c: dict[str, str], t: dict[str, str]) -> str:
    """The simulator's payment page between a signed request coming in and three signed answers going back."""
    chips = ""
    for i, (label, color) in enumerate(zip(["TatraPay", "CardPay", "e-platby", "SporoPay"], [BANKS[0], BANKS[0], BANKS[1], BANKS[2]])):
        x = 120 + i * 80
        chips += f'<rect x="{x}" y="70" width="74" height="22" rx="11" fill="{c["bar"]}" stroke="{c["line"]}"/>'
        chips += f'<circle cx="{x + 12}" cy="81" r="4.5" fill="{color}"/>'
        chips += f'<text x="{x + 21}" y="85.5" {FONT} font-size="10.5" fill="{c["muted"]}">{label}</text>'
    return f"""
  <!-- the signed request -->
  <text x="16" y="34" {MONO} font-size="12" font-weight="700" fill="{c['ink']}">GET /payment/tatrapay-hmac</text>
  <text x="16" y="52" {MONO} font-size="11" fill="{c['muted']}">?MID=9999&amp;AMT=24.90&amp;VS=2026001…&amp;HMAC=d594…</text>
  {chips}
  <path d="M60 60V128" fill="none" stroke="{c['arrow']}" stroke-width="2.5" stroke-dasharray="6 5" marker-end="url(#arrow)"/>

  <!-- the simulator page -->
  <rect x="16" y="136" width="488" height="214" rx="14" fill="{c['panel']}" stroke="{c['line']}" stroke-width="1.5"/>
  <path d="M16 150a14 14 0 0 1 14-14h460a14 14 0 0 1 14 14v12H16z" fill="{c['bar']}"/>
  <circle cx="34" cy="150" r="4" fill="{c['line']}"/><circle cx="48" cy="150" r="4" fill="{c['line']}"/><circle cx="62" cy="150" r="4" fill="{c['line']}"/>
  <text x="80" y="154" {FONT} font-size="12" fill="{c['muted']}">127.0.0.1:8080/payment/tatrapay-hmac</text>
  <text x="36" y="194" {FONT} font-size="17" font-weight="700" fill="{c['ink']}">TatraPay simulator (HMAC version)</text>
  <text x="36" y="220" {MONO} font-size="11.5" fill="{c['muted']}">Input Sign:    d594feb52753b799…</text>
  <text x="36" y="238" {MONO} font-size="11.5" fill="{c['muted']}">Computed Sign: d594feb52753b799…</text>
  <rect x="36" y="252" width="104" height="28" rx="14" fill="{c['ok_soft']}" stroke="{c['ok']}"/>
  {check(48, 266, c['ok'], 2.4)}
  <text x="68" y="271" {FONT} font-size="13" font-weight="700" fill="{c['ok']}">Sign OK</text>
  <rect x="36" y="296" width="132" height="36" rx="8" fill="{c['accent']}"/>
  <text x="102" y="319" {FONT} font-size="13" font-weight="600" fill="{c['on_accent']}" text-anchor="middle">continue ok</text>
  <rect x="180" y="296" width="132" height="36" rx="8" fill="{c['bad_soft']}" stroke="{c['bad']}"/>
  <text x="246" y="319" {FONT} font-size="13" font-weight="600" fill="{c['bad']}" text-anchor="middle">continue fail</text>
  <rect x="324" y="296" width="160" height="36" rx="8" fill="{c['warn_soft']}" stroke="{c['warn']}"/>
  <text x="404" y="319" {FONT} font-size="13" font-weight="600" fill="{c['warn']}" text-anchor="middle">continue timeout</text>

  <!-- the signed answers -->
  <path d="M102 336V378" fill="none" stroke="{c['arrow']}" stroke-width="2.5" stroke-dasharray="6 5" marker-end="url(#arrow)"/>
  <path d="M246 336V378" fill="none" stroke="{c['arrow']}" stroke-width="2.5" stroke-dasharray="6 5" marker-end="url(#arrow)"/>
  <path d="M404 336V378" fill="none" stroke="{c['arrow']}" stroke-width="2.5" stroke-dasharray="6 5" marker-end="url(#arrow)"/>
  <text x="16" y="404" {MONO} font-size="11.5" fill="{c['ink']}">rurl?VS=2026001&amp;RES=OK|FAIL|TOUT&amp;…&amp;HMAC=…</text>
  <text x="16" y="424" {FONT} font-size="11.5" fill="{c['muted']}">{t['signed']}</text>
"""


DEFS = """<defs>
    <marker id="arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
      <path d="M0 0L10 5L0 10z" fill="{arrow}"/>
    </marker>
  </defs>"""


def hero(c: dict[str, str], t: dict[str, str]) -> str:
    return f"""<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 520 440" width="520" height="440" role="img" aria-labelledby="t d">
  <title id="t">payment-simulator</title>
  <desc id="d">{t['desc']}</desc>
  {DEFS.format(arrow=c['arrow'])}
  {hero_body(c, t)}
</svg>
"""


# The social card text per language: og.svg (English) and og-sk.svg (pages under docs/sk/).
OG = {
    "": {"label": "test Slovak bank payment gateways without the bank",
         "line1": "Slovak bank payment gateways,", "line2": "without the bank and without money"},
    "-sk": {"label": "testujte platobné brány slovenských bánk bez banky",
            "line1": "Platobné brány slovenských bánk", "line2": "bez banky a bez peňazí"},
}


def og(c: dict[str, str], suffix: str = "") -> str:
    t = OG[suffix]
    return f"""<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" width="1200" height="630" role="img" aria-label="{NAME}: {t['label']}">
  <rect width="1200" height="630" fill="{c['bg']}"/>
  {''.join(f'<rect x="{i * 300}" y="0" width="300" height="14" fill="{color}"/>' for i, color in enumerate(BANKS))}
  {DEFS.format(arrow=c['arrow'])}
  <g transform="translate(80 110) scale(2.6)">
  {mark(c)}
  </g>
  <text x="80" y="370" {FONT} font-size="64" font-weight="800" fill="{c['ink']}">{NAME}</text>
  <text x="80" y="432" {FONT} font-size="34" fill="{c['muted']}">{t['line1']}</text>
  <text x="80" y="476" {FONT} font-size="34" fill="{c['muted']}">{t['line2']}</text>
  <text x="80" y="566" {MONO} font-size="24" fill="{c['ink']}">TatraPay · CardPay · ComfortPay · e-platby · SporoPay</text>
  <g transform="translate(720 80) scale(0.9)">
  {hero_body(c, HERO[suffix])}
  </g>
</svg>
"""


def sequence(c: dict[str, str], title: str, desc: str, lanes: list[str], steps: list[tuple]) -> str:
    """A sequence diagram. A step is (from, to, label, note) for a message or (lane, None, label, note) for an action."""
    width = 760
    gap = width / len(lanes)
    xs = [gap / 2 + i * gap for i in range(len(lanes))]
    top = 76
    height = top + 30 + 58 * len(steps)
    out = [f"""<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {width} {height}" width="{width}" height="{height}" role="img" aria-labelledby="t d">
  <title id="t">{title}</title>
  <desc id="d">{desc}</desc>
  {DEFS.format(arrow=c['arrow'])}"""]
    for x, lane in zip(xs, lanes):
        out.append(f'  <rect x="{x - 105:.0f}" y="12" width="210" height="44" rx="10" fill="{c["panel"]}" stroke="{c["line"]}" stroke-width="1.5"/>')
        out.append(f'  <text x="{x:.0f}" y="40" {FONT} font-size="15" font-weight="700" fill="{c["ink"]}" text-anchor="middle">{lane}</text>')
        out.append(f'  <path d="M{x:.0f} 56V{height - 10}" stroke="{c["line"]}" stroke-width="1.5" stroke-dasharray="4 5"/>')
    y = top
    for number, (src, dst, label, note) in enumerate(steps, 1):
        y += 58
        if dst is None:
            x = xs[src]
            out.append(f'  <rect x="{x - 118:.0f}" y="{y - 40}" width="236" height="48" rx="9" fill="{c["ok_soft"]}" stroke="{c["ok"]}" stroke-width="1.3"/>')
            out.append(f'  <text x="{x:.0f}" y="{y - 20}" {MONO} font-size="12.5" font-weight="700" fill="{c["ink"]}" text-anchor="middle">{number}. {label}</text>')
            out.append(f'  <text x="{x:.0f}" y="{y - 3}" {FONT} font-size="11.5" fill="{c["muted"]}" text-anchor="middle">{note}</text>')
            continue
        x1, x2 = xs[src], xs[dst]
        direction = 1 if x2 > x1 else -1
        out.append(f'  <path d="M{x1 + direction * 4:.0f} {y}H{x2 - direction * 6:.0f}" stroke="{c["arrow"]}" stroke-width="2.2" marker-end="url(#arrow)"/>')
        mid = (x1 + x2) / 2
        out.append(f'  <text x="{mid:.0f}" y="{y - 24}" {MONO} font-size="12.5" font-weight="700" fill="{c["ink"]}" text-anchor="middle">{number}. {label}</text>')
        out.append(f'  <text x="{mid:.0f}" y="{y - 8}" {FONT} font-size="11.5" fill="{c["muted"]}" text-anchor="middle">{note}</text>')
    out.append("</svg>\n")
    return "\n".join(out)


# Labels per language. English files keep their names; Slovak files get a -sk suffix
# (illustrations/hero-sk-light.svg, diagrams/flow-sk-dark.svg) and are used by the pages under docs/sk/.
# The simulator's own UI labels (Sign OK, continue ok …) stay in English in both, as the app shows them.
HERO = {
    "": {
        "desc": "A signed request from the shop opens the simulator's payment page, which shows that the signature is correct and offers three signed answers back to the shop: a successful, a failed and a timed-out payment.",
        "signed": "signed like the bank signs it, for your completePurchase()",
    },
    "-sk": {
        "desc": "Podpísaná požiadavka z e-shopu otvorí platobnú stránku simulátora, ktorá ukáže, že podpis je správny, a ponúkne tri podpísané odpovede späť do e-shopu: úspešnú, neúspešnú a časovo vypršanú platbu.",
        "signed": "podpísané tak, ako podpisuje banka, pre váš completePurchase()",
    },
}

DIAGRAMS = {
    "": {
        "flow": (
            "A payment through payment-simulator",
            "The shop redirects the customer to the simulator with a signed request; the simulator recomputes the signature with its test key and shows Sign OK, Wrong sign or Missing parameters; with a correct signature the customer picks a successful, failed or timed-out payment and is sent back to the shop's return URL with a result signed like the bank signs it, which the shop verifies.",
            ["Customer's browser", "Your shop (test mode)", "payment-simulator"],
            [
                (1, 0, "302 / POST form", "signed with the test key"),
                (0, 2, "GET /payment/…", "MID AMT CURR VS RURL … HMAC"),
                (2, None, "recompute the signature", "Sign OK or Wrong sign"),
                (2, 0, "continue ok | fail | timeout", "the customer picks the bank's answer"),
                (0, 1, "GET rurl?VS=…&amp;RES=…", "signed like the bank signs it"),
                (1, None, "completePurchase()", "verify, then mark the order"),
            ],
        ),
    },
    "-sk": {
        "flow": (
            "Platba cez payment-simulator",
            "E-shop presmeruje zákazníka na simulátor s podpísanou požiadavkou; simulátor prepočíta podpis testovacím kľúčom a zobrazí Sign OK, Wrong sign alebo Missing parameters; pri správnom podpise zákazník zvolí úspešnú, neúspešnú alebo časovo vypršanú platbu a vráti sa na návratovú URL e-shopu s výsledkom podpísaným tak, ako ho podpisuje banka, ktorý e-shop overí.",
            ["Prehliadač zákazníka", "Váš e-shop (test. režim)", "payment-simulator"],
            [
                (1, 0, "302 / POST formulár", "podpísané testovacím kľúčom"),
                (0, 2, "GET /payment/…", "MID AMT CURR VS RURL … HMAC"),
                (2, None, "prepočíta podpis", "Sign OK alebo Wrong sign"),
                (2, 0, "continue ok | fail | timeout", "zákazník zvolí odpoveď banky"),
                (0, 1, "GET rurl?VS=…&amp;RES=…", "podpísané tak, ako podpisuje banka"),
                (1, None, "completePurchase()", "overiť, potom označiť objednávku"),
            ],
        ),
    },
}


def write(path: Path, text: str) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(text, encoding="utf-8")
    print(f"wrote {path.relative_to(PUBLIC.parent)}")


def main() -> None:
    write(PUBLIC / "logo.svg", logo(LIGHT))
    write(PUBLIC / "logo-dark.svg", logo(DARK))
    write(PUBLIC / "favicon.svg", logo(LIGHT))
    for suffix, strings in HERO.items():
        write(PUBLIC / "illustrations" / f"hero{suffix}-light.svg", hero(LIGHT, strings))
        write(PUBLIC / "illustrations" / f"hero{suffix}-dark.svg", hero(DARK, strings))
    for suffix in OG:
        write(PUBLIC / f"og{suffix}.svg", og(LIGHT, suffix))
    for suffix, diagrams in DIAGRAMS.items():
        for name, (title, desc, lanes, steps) in diagrams.items():
            write(PUBLIC / "diagrams" / f"{name}{suffix}-light.svg", sequence(LIGHT, title, desc, lanes, steps))
            write(PUBLIC / "diagrams" / f"{name}{suffix}-dark.svg", sequence(DARK, title, desc, lanes, steps))


if __name__ == "__main__":
    main()
