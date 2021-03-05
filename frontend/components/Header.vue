<template>
  <header class="d-flex w-100 justify-content-center">
    <div id="mobile_menu">
      <div>
        <span class="close" @click.stop="openMenu">&#10005;</span>

        <ul class="p-0 m-0">
          <li
            v-for="(category, index) in this.$helpers.categories()"
            :key="index"
          >
            <a @click="goTo('/category/' + category.slug)">
              {{ category.name }}
            </a>
          </li>
          <li>
            <a @click="goTo('/about-us/')"> О нас </a>
          </li>
        </ul>
      </div>
    </div>
    <div
      class="w-100 py-2 d-flex align-items-center"
      :class="{ fixed: fixedHeader }"
    >
      <div class="container">
        <div class="row align-items-center">
          <div
            class="col-5 col-xl-2 col-lg-2 col-md-2 order-1 logo"
            @click.stop="goMain"
          >
            <div class="d-md-none menu" @click.stop="openMenu"></div>
            <span class="text-center text-sm-left"
              ><img src="/content/logo.png" alt="caliber.az logo"
            /></span>
          </div>
          <div
            class="col-12 col-xl-8 col-lg-10 col-md-12 order-lg-2 order-3 mt-3 mt-lg-0 d-md-block d-none"
          >
            <nav class="d-flex justify-content-center align-items-center">
              <ul class="p-0 m-0 text-center">
                <li
                  v-for="(category, index) in this.$helpers.categories()"
                  :key="index"
                >
                  <a :href="'/category/' + category.slug">
                    {{ category.name }}
                  </a>
                </li>
                <li>
                  <NuxtLink :to="'/about-us/'"> О нас </NuxtLink>
                </li>
              </ul>
            </nav>
          </div>
          <div
            class="col-7 col-xl-2 col-lg-12 col-md-10 order-lg-3 order-2 social"
          >
            <ul class="p-0 m-0 text-right">
              <li>
                <a
                  rel="noreferrer"
                  target="_blank"
                  href="https://www.facebook.com/caliber.az/"
                  ><img src="/content/facebook.svg" alt="facebook"
                /></a>
              </li>
              <li>
                <a
                  target="_blank"
                  rel="noreferrer"
                  href="https://twitter.com/Caliberaz"
                  ><img src="/content/twitter.svg" alt="twitter"
                /></a>
              </li>
              <li>
                <a
                  rel="noreferrer"
                  target="_blank"
                  href="https://www.instagram.com/caliberaz/"
                  ><img src="/content/instagram.svg" alt="instagram"
                /></a>
              </li>
              <li>
                <a
                  rel="noreferrer"
                  target="_blank"
                  href="https://www.youtube.com/channel/UCFKTz5yl8ghDotof1U0OWbg"
                  ><img src="/content/youtube.svg" alt="youtube"
                /></a>
              </li>
              <li>
                <a
                  rel="noreferrer"
                  target="_blank"
                  href="https://t.me/caliber_az_official"
                  ><img src="/content/telegram.svg" alt="telegram"
                /></a>
              </li>
              <li class="search-wrapper">
                <form action="/search">
                  <input type="text" name="keyword" @click.stop="" />
                </form>
              </li>
              <li>
                <a @click.stop="openSearch"
                  ><img src="/content/search.svg" alt="search"
                /></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!--      <div class="container">-->
      <!--        <div id="quotes" class="row">-->
      <!--          <div class="col-md-3">-->
      <!--            <ul class="p-0 m-0 left-side text-center text-md-left">-->
      <!--              <li class="down">$&nbsp;{{ this.$helpers.settings().usd }}</li>-->
      <!--              <li class="up">€&nbsp;{{ this.$helpers.settings().euro }}</li>-->
      <!--              <li class="up">-->
      <!--                <img src="/content/oil.svg" alt="oil" />&nbsp;{{-->
      <!--                  this.$helpers.settings().oil-->
      <!--                }}-->
      <!--              </li>-->
      <!--            </ul>-->
      <!--          </div>-->
      <!--          <div class="col-md-6 center-side text-center mt-md-0 mt-2">-->
      <!--            <ul class="p-0 m-0">-->
      <!--              <li>-->
      <!--                <img src="/content/covid.svg" alt="oil" />&nbsp;COVID-19 |-->
      <!--                ЗАРАЗИЛИСЬ: {{ this.$helpers.settings().covid_total }}. БАКУ:-->
      <!--                {{ this.$helpers.settings().covid_baku }}.-->
      <!--              </li>-->
      <!--            </ul>-->
      <!--          </div>-->
      <!--          <div class="col-md-3 text-center text-md-right mt-md-0 mt-2">-->
      <!--            <ul class="p-0 m-0">-->
      <!--              <li>ПОГОДА:</li>-->
      <!--              <li>-->
      <!--                БАКУ-->
      <!--                &lt;!&ndash;            <img src="/content/sun.svg" alt="" />&ndash;&gt;-->
      <!--                {{ this.$helpers.settings().weather_baku }}-->
      <!--              </li>-->
      <!--              <li>-->
      <!--                ГЯНДЖА-->
      <!--                &lt;!&ndash;            <img src="/content/ice.svg" alt="" />&ndash;&gt;-->
      <!--                {{ this.$helpers.settings().weather_ganca }}-->
      <!--              </li>-->
      <!--            </ul>-->
      <!--          </div>-->
      <!--        </div>-->
      <!--      </div>-->
    </div>
  </header>
</template>

<script>
export default {
  name: 'Header',

  data() {
    return { fixedHeader: false, scrollY: 0, offsetTop: null }
  },

  watch: {
    scrollY(current) {
      this.fixedHeader = current > this.offsetTop
    },
  },

  mounted() {
    this.offsetTop = document.getElementsByTagName('header')[0].offsetTop

    window.addEventListener('scroll', (event) => {
      this.scrollY = Math.round(window.scrollY)
    })
  },

  methods: {
    openMenu() {
      document.getElementById('mobile_menu').classList.toggle('open')
      document.getElementsByTagName('body')[0].classList.toggle('no-scroll')
      setTimeout(() => {
        document.querySelector('#mobile_menu ul').classList.toggle('open')
      }, 699)
    },

    goTo(to) {
      document.querySelector('#mobile_menu ul').classList.remove('open')
      document.getElementById('mobile_menu').classList.remove('open')

      window.location = to
    },

    goMain() {
      window.location = '/'
    },

    openSearch() {
      const wrapper = document.getElementsByClassName('search-wrapper')[0]

      if (wrapper.classList.contains('open')) {
        wrapper.classList.remove('open')

        wrapper.querySelector('input').value = ''
      } else {
        wrapper.classList.add('open')
      }
    },
  },
}
</script>
