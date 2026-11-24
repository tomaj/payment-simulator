// Navigation and sidebar. English lives at the site root; Slovak mirrors every page under /sk/.
import type { DefaultTheme } from 'vitepress'

export const nav: DefaultTheme.NavItem[] = [
  { text: 'Guide', link: '/guide/getting-started', activeMatch: '^/guide/' },
  { text: 'Banks', link: '/banks/', activeMatch: '^/banks/' },
  { text: 'Examples', link: '/examples/', activeMatch: '^/examples/' },
  { text: 'Endpoints', link: '/reference/endpoints' },
  { text: 'Live instance', link: 'https://platby.tomaj.sk/' },
]

export const sidebar: DefaultTheme.SidebarItem[] = [
  {
    text: 'Introduction',
    items: [
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'How it works', link: '/guide/how-it-works' },
      { text: 'Running it', link: '/guide/running' },
      { text: 'Configuration and test keys', link: '/guide/configuration' },
      { text: 'Using it with Omnipay', link: '/guide/omnipay' },
      { text: 'Troubleshooting', link: '/guide/troubleshooting' },
    ],
  },
  {
    text: 'Banks',
    items: [
      { text: 'Overview', link: '/banks/' },
      { text: 'Tatra banka: TatraPay', link: '/banks/tatrapay' },
      { text: 'Tatra banka: CardPay', link: '/banks/cardpay' },
      { text: 'Tatra banka: ComfortPay', link: '/banks/comfortpay' },
      { text: 'CardPay Direct API', link: '/banks/cardpay-direct-api' },
      { text: 'VÚB e-platby', link: '/banks/vub-eplatby' },
      { text: 'SLSP SporoPay', link: '/banks/slsp-sporopay' },
    ],
  },
  {
    text: 'Examples',
    items: [
      { text: 'Overview', link: '/examples/' },
      { text: 'omnipay-tatra', link: '/examples/omnipay-tatra' },
      { text: 'Plain PHP client', link: '/examples/plain-php' },
      { text: 'VÚB e-platby', link: '/examples/vub-eplatby' },
      { text: 'SLSP SporoPay', link: '/examples/slsp-sporopay' },
      { text: 'CardPay Direct API', link: '/examples/cardpay-direct-api' },
      { text: 'CardPay capture and refund', link: '/examples/cardpay-capture' },
    ],
  },
  {
    text: 'Reference',
    items: [
      { text: 'Endpoints', link: '/reference/endpoints' },
      { text: 'FAQ', link: '/faq' },
      { text: 'Security', link: '/security' },
      { text: 'Changelog', link: '/changelog' },
      { text: 'Contributing', link: '/contributing' },
    ],
  },
]

// Slovak mirror under /sk/: the same pages and order as above.
export const navSk: DefaultTheme.NavItem[] = [
  { text: 'Príručka', link: '/sk/guide/getting-started', activeMatch: '^/sk/guide/' },
  { text: 'Banky', link: '/sk/banks/', activeMatch: '^/sk/banks/' },
  { text: 'Príklady', link: '/sk/examples/', activeMatch: '^/sk/examples/' },
  { text: 'Endpointy', link: '/sk/reference/endpoints' },
  { text: 'Živá inštancia', link: 'https://platby.tomaj.sk/' },
]

export const sidebarSk: DefaultTheme.SidebarItem[] = [
  {
    text: 'Úvod',
    items: [
      { text: 'Začíname', link: '/sk/guide/getting-started' },
      { text: 'Ako to funguje', link: '/sk/guide/how-it-works' },
      { text: 'Spustenie', link: '/sk/guide/running' },
      { text: 'Konfigurácia a testovacie kľúče', link: '/sk/guide/configuration' },
      { text: 'Použitie s Omnipay', link: '/sk/guide/omnipay' },
      { text: 'Riešenie problémov', link: '/sk/guide/troubleshooting' },
    ],
  },
  {
    text: 'Banky',
    items: [
      { text: 'Prehľad', link: '/sk/banks/' },
      { text: 'Tatra banka: TatraPay', link: '/sk/banks/tatrapay' },
      { text: 'Tatra banka: CardPay', link: '/sk/banks/cardpay' },
      { text: 'Tatra banka: ComfortPay', link: '/sk/banks/comfortpay' },
      { text: 'CardPay Direct API', link: '/sk/banks/cardpay-direct-api' },
      { text: 'VÚB e-platby', link: '/sk/banks/vub-eplatby' },
      { text: 'SLSP SporoPay', link: '/sk/banks/slsp-sporopay' },
    ],
  },
  {
    text: 'Príklady',
    items: [
      { text: 'Prehľad', link: '/sk/examples/' },
      { text: 'omnipay-tatra', link: '/sk/examples/omnipay-tatra' },
      { text: 'Klient v čistom PHP', link: '/sk/examples/plain-php' },
      { text: 'VÚB e-platby', link: '/sk/examples/vub-eplatby' },
      { text: 'SLSP SporoPay', link: '/sk/examples/slsp-sporopay' },
      { text: 'CardPay Direct API', link: '/sk/examples/cardpay-direct-api' },
      { text: 'CardPay: dokončenie a vrátenie platby', link: '/sk/examples/cardpay-capture' },
    ],
  },
  {
    text: 'Referencia',
    items: [
      { text: 'Endpointy', link: '/sk/reference/endpoints' },
      { text: 'Časté otázky', link: '/sk/faq' },
      { text: 'Bezpečnosť', link: '/sk/security' },
      { text: 'Zoznam zmien', link: '/sk/changelog' },
      { text: 'Ako prispieť', link: '/sk/contributing' },
    ],
  },
]
