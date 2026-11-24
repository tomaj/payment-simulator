import { defineConfig } from 'vitepress'
import { nav, navSk, sidebar, sidebarSk } from '../site-nav'
import { seoHead, siteUrl } from './seo'

const description = 'Simulator of Slovak bank online payment gateways (TatraPay, CardPay, ComfortPay, CardPay Direct API, VÚB e-platby, SLSP SporoPay) for testing shop integrations without the bank and without money.'
const descriptionSk = 'Simulátor platobných brán slovenských bánk (TatraPay, CardPay, ComfortPay, CardPay Direct API, VÚB e-platby, SLSP SporoPay) na testovanie integrácie e-shopu bez banky a bez peňazí.'

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
    return seoHead(pageData, siteConfig.srcDir, pageData.relativePath.startsWith('sk/') ? descriptionSk : description)
  },

  // English is the default locale at the site root; Slovak mirrors every page under /sk/.
  locales: {
    root: {
      label: 'English',
      lang: 'en-US',
      description,
    },
    sk: {
      label: 'Slovenčina',
      lang: 'sk-SK',
      link: '/sk/',
      title: 'payment-simulator',
      description: descriptionSk,
      themeConfig: {
        nav: navSk,
        sidebar: sidebarSk,
        outline: { level: [2, 3], label: 'Na tejto stránke' },
        docFooter: {
          prev: 'Predchádzajúca stránka',
          next: 'Nasledujúca stránka',
        },
        editLink: {
          pattern: 'https://github.com/tomaj/payment-simulator/edit/master/docs/:path',
          text: 'Upraviť túto stránku na GitHube',
        },
        lastUpdated: {
          text: 'Naposledy upravené',
        },
        darkModeSwitchLabel: 'Vzhľad',
        lightModeSwitchTitle: 'Prepnúť na svetlý režim',
        darkModeSwitchTitle: 'Prepnúť na tmavý režim',
        sidebarMenuLabel: 'Ponuka',
        returnToTopLabel: 'Späť na začiatok',
        langMenuLabel: 'Zmeniť jazyk',
        skipToContentLabel: 'Preskočiť na obsah',
        footer: {
          message: 'Vydané pod <a href="https://github.com/tomaj/payment-simulator/blob/master/LICENSE.md">licenciou MIT</a>. Projekt nie je prepojený so žiadnou bankou; simulované stránky len napodobňujú protokoly bánk.',
          copyright: 'Copyright © 2016-2026 Tomas Majer',
        },
        notFound: {
          code: '404',
          title: 'STRÁNKA SA NENAŠLA',
          quote: 'Táto stránka neexistuje alebo sa presunula. Skúste vyhľadávanie alebo začnite od úvodu.',
          linkLabel: 'prejsť na úvod',
          linkText: 'Späť na úvod',
        },
      },
    },
  },

  // The examples are included from the repository root (<<< @/../examples/...).
  vite: {
    server: { fs: { allow: ['..'] } },
  },

  themeConfig: {
    logo: { light: '/logo.svg', dark: '/logo-dark.svg', alt: 'payment-simulator' },

    nav,
    sidebar,

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
      options: {
        locales: {
          sk: {
            translations: {
              button: {
                buttonText: 'Hľadať',
                buttonAriaLabel: 'Hľadať',
              },
              modal: {
                displayDetails: 'Zobraziť podrobný zoznam',
                resetButtonTitle: 'Vymazať hľadanie',
                backButtonTitle: 'Zavrieť hľadanie',
                noResultsText: 'Žiadne výsledky pre',
                footer: {
                  selectText: 'vybrať',
                  selectKeyAriaLabel: 'enter',
                  navigateText: 'prejsť',
                  navigateUpKeyAriaLabel: 'šípka hore',
                  navigateDownKeyAriaLabel: 'šípka dole',
                  closeText: 'zavrieť',
                  closeKeyAriaLabel: 'escape',
                },
              },
            },
          },
        },
      },
    },

    footer: {
      message: 'Released under the <a href="https://github.com/tomaj/payment-simulator/blob/master/LICENSE.md">MIT License</a>. Not affiliated with any bank; the simulated pages only imitate the banks\' protocols.',
      copyright: 'Copyright © 2016-2026 Tomas Majer',
    },
  },
})
