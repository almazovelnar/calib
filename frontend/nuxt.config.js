export default {
  // Global page headers (https://go.nuxtjs.dev/config-head)
  head: {
    htmlAttrs: {
      lang: 'ru',
    },
    script: [
      { hid: 'script', src: 'https://www.instagram.com/embed.js' },
      { hid: 'script', src: 'https://platform.twitter.com/widgets.js' },
    ],
    title: 'Caliber.az',
    meta: [
      { charset: 'utf-8' },
      {
        name: 'viewport',
        content:
          'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0',
      },
      { name: 'msapplication-TileColor', content: '#000' },
      { name: 'apple-mobile-web-app-title', content: 'Caliber.az' },
      { name: 'application-name', content: 'Caliber.az' },
      { property: 'og:type', content: 'website' },
      { name: 'msapplication-config', content: '/content/browserconfig.xml' },
      {
        name: 'msapplication-TileImage',
        content: '/content/mstile-144x144.png',
      },
      { name: 'theme-color', content: '#000' },
      { property: 'og:type', content: 'website' },
      { property: 'og:locale', content: 'ru_RU' },
    ],
    link: [
      {
        rel: 'icon',
        type: 'image/png',
        size: '32x32',
        href: '/content/favicon-32x32.png',
      },
      {
        rel: 'icon',
        type: 'image/png',
        size: '194x194',
        href: '/content/favicon-194x194.png',
      },
      {
        rel: 'icon',
        type: 'image/png',
        size: '192x192',
        href: '/content/android-chrome-192x192.png',
      },
      {
        rel: 'icon',
        type: 'image/png',
        size: '16x16',
        href: '/content/favicon-16x16.png',
      },
      {
        rel: 'apple-touch-icon',
        sizes: '180x180',
        href: '/content/apple-touch-icon.png',
      },
      {
        rel: 'manifest',
        href: '/content/site.webmanifest',
      },
      {
        rel: 'shortcut icon',
        href: '/content/favicon.ico',
      },
      {
        rel: 'mask-icon',
        color: '#000',
        href: '/content/safari-pinned-tab.svg',
      },
    ],
  },

  loading: {
    height: '5px',
    duration: 3000,
    continuous: true,
    color: '#129BDB',
  },

  // Global CSS (https://go.nuxtjs.dev/config-css)
  css: ['@/assets/scss/app.scss'],

  // Plugins to run before rendering page (https://go.nuxtjs.dev/config-plugins)
  plugins: [
    { src: '@/plugins/slider', mode: 'client' },
    { src: '@/plugins/axios', mode: 'all' },
    { src: '@/plugins/helpers', mode: 'all' },
  ],

  // Auto import components (https://go.nuxtjs.dev/config-components)
  components: true,

  // Modules for dev and build (recommended) (https://go.nuxtjs.dev/config-modules)
  buildModules: [
    // https://go.nuxtjs.dev/eslint
    '@nuxtjs/eslint-module',
    // https://go.nuxtjs.dev/stylelint
    '@nuxtjs/stylelint-module',
    '@nuxtjs/google-analytics',
  ],

  // Modules (https://go.nuxtjs.dev/config-modules)
  modules: [
    // https://go.nuxtjs.dev/axios
    '@nuxtjs/axios',
    'cookie-universal-nuxt',
  ],

  proxy: {
    '/api': {
      target: process.env.API_URI,
      pathRewrite: {
        '^/api': '/',
      },
      headers: {
        'X-Caliber-Token': process.env.TOKEN,
      },
      changeOrigin: true,
    },
  },

  googleAnalytics: {
    id: 'G-6RVB36SQZX',
  },

  router: {
    middleware: ['set-helpers'],
  },

  // Axios module configuration (https://go.nuxtjs.dev/config-axios)
  axios: {
    baseURL: process.env.BASE_URI + '/api',
    // baseURL: process.env.API_URI,
    browserBaseURL: process.env.BASE_URI + '/api',
    // browserBaseURL: process.env.API_URI,
    throttle: 100,
    proxy: true,
    withCredentials: true,
    credentials: true,
    proxyHeadersIgnore: [
      'host',
      'accept',
      'cf-ray',
      'cf-connecting-ip',
      'content-length',
    ],
    progress: true,
    retry: { retries: 0 },
    debug: process.env.NODE_ENV !== 'production',
  },

  // Build Configuration (https://go.nuxtjs.dev/config-build)
  build: {
    publicPath: '/src',
    extractCSS: process.env.NODE_ENV === 'production',
    transpile: ['tiny-slider'],
  },
}
