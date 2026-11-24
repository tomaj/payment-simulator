import { defineConfig } from 'vitepress'
import { seoHead, siteUrl } from './seo'

const description = 'Simulator of Slovak bank online payment gateways (TatraPay, CardPay, ComfortPay, CardPay Direct API, VÚB e-platby, SLSP SporoPay) for testing shop integrations without the bank and without money.'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: 'payment-simulator',
  description,
  base: '/payment-simulator/',
  cleanUrls: true,

  head: [
    ['link', { rel: 'icon', type: 'image/svg+xml', href: '/payment-simulator/favicon.svg' }],
    ['link', { rel: 'icon', type: 'image/png', sizes: '32x32', href: '/payment-simulator/favicon-32.png' }],
    ['link', { rel: 'icon', type: 'image/png', sizes: '16x16', href: '/payment-simulator/favicon-16.png' }],
    ['link', { rel: 'apple-touch-icon', sizes: '180x180', href: '/payment-simulator/apple-touch-icon.png' }],
    ['link', { rel: 'manifest', href: '/payment-simulator/site.webmanifest' }],
    ['meta', { name: 'theme-color', content: '#1e293b' }],
  ],

  lastUpdated: true,

  sitemap: {
    hostname: siteUrl,
  },

  transformHead({ pageData, siteConfig }) {
    return seoHead(pageData, siteConfig.srcDir, description)
  },

  // The examples are included from the repository root (<<< @/../examples/...).
  vite: {
    server: { fs: { allow: ['..'] } },
  },

  themeConfig: {
    logo: { light: '/logo.svg', dark: '/logo-dark.svg', alt: 'payment-simulator' },

    nav: [
      { text: 'Guide', link: '/guide/getting-started', activeMatch: '/guide/' },
      { text: 'Banks', link: '/banks/', activeMatch: '/banks/' },
      { text: 'Examples', link: '/examples/', activeMatch: '/examples/' },
      { text: 'Endpoints', link: '/reference/endpoints' },
      { text: 'Live instance', link: 'https://platby.tomaj.sk/' },
    ],

    sidebar: [
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
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/tomaj/payment-simulator' },
    ],

    editLink: {
      pattern: 'https://github.com/tomaj/payment-simulator/edit/master/docs/:path',
      text: 'Edit this page on GitHub',
    },

    lastUpdated: {
      text: 'Last updated',
    },

    outline: {
      level: [2, 3],
    },

    search: {
      provider: 'local',
    },

    footer: {
      message: 'Released under the <a href="https://github.com/tomaj/payment-simulator/blob/master/LICENSE.md">MIT License</a>. Not affiliated with any bank; the simulated pages only imitate the banks\' protocols.',
      copyright: 'Copyright © 2016-2026 Tomas Majer',
    },
  },
})
