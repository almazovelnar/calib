<template>
  <div class="w-100">
    <div class="container box">
      <div v-if="isMobile" class="row custom-carous">
        <XSlider
          name="top-exc"
          :items="1"
          :gutter="0"
          :autoplay="true"
          :nav="true"
          :controls="false"
          :progress="true"
          :responsive="{
            1200: {
              items: 3,
              gutter: 24,
            },

            992: {
              items: 2,
              gutter: 24,
            },
          }"
        >
          <div v-if="top.left1" class="col-md-12 col-lg-6">
            <Block
              :blank="true"
              :progress="true"
              :hide-date="true"
              :type="'wide'"
              :news="top.left1"
            />
          </div>
          <div v-if="top.right1" class="col-md-12 mt-2 mt-lg-0 col-lg-6">
            <Block
              :blank="true"
              :progress="true"
              :hide-date="true"
              :type="'wide'"
              :news="top.right1"
            />
          </div>
          <div v-if="top.left2" class="col-md-12 mt-2 mt-lg-0 col-lg-6">
            <Block
              :blank="true"
              :progress="true"
              :hide-date="true"
              :type="'wide'"
              :news="top.left2"
            />
          </div>
          <div v-if="top.right2" class="col-md-12 mt-2 mt-lg-0 col-lg-6">
            <Block
              :blank="true"
              :progress="true"
              :hide-date="true"
              :type="'wide'"
              :news="top.right2"
            />
          </div>
          <div v-if="top.left3" class="col-md-12 mt-2 mt-lg-0 col-lg-6">
            <Block
              :blank="true"
              :progress="true"
              :hide-date="true"
              :type="'wide'"
              :news="top.left3"
            />
          </div>
          <!--          <div-->
          <!--            v-for="(news, index) in recent.slice(0, 3)"-->
          <!--            :key="index"-->
          <!--            class="col-lg-4 col-md-12"-->
          <!--          >-->
          <!--            <Block-->
          <!--              :progress="true"-->
          <!--              :hide-date="true"-->
          <!--              :blank="true"-->
          <!--              :type="'wide'"-->
          <!--              :news="news"-->
          <!--            />-->
          <!--          </div>-->
        </XSlider>
      </div>

      <div v-if="!isMobile" id="top-second" class="row">
        <div v-if="top.left1" class="col-md-12 col-lg-6">
          <Block
            :blank="true"
            :hide-date="true"
            :type="'wide'"
            :news="top.left1"
          />
        </div>
        <div v-if="top.right1" class="col-md-12 mt-2 mt-lg-0 col-lg-6">
          <Block
            :blank="true"
            :hide-date="true"
            :type="'wide'"
            :news="top.right1"
          />
        </div>
      </div>

      <div v-if="this.$helpers.settings().red_title" id="top-third" class="row">
        <div class="col-md-12">
          <div class="inside">
            <span class="title">
              {{ this.$helpers.settings().red_title }}
            </span>
            <!--            <span class="tag">#{{ this.$helpers.settings().red_tag }}</span>-->
            <span class="date">{{ this.$helpers.settings().red_date }}</span>
          </div>
        </div>
      </div>

      <div v-if="!isMobile" id="top-fourth" class="row">
        <div
          v-for="(news, index) in recent.slice(0, 3)"
          :key="index"
          class="col-lg-4 col-md-12"
        >
          <Block :blank="true" :news="news" />
        </div>
      </div>

      <div v-if="!isMobile" id="top-fifth" class="row">
        <div
          v-for="(news, index) in recent.slice(3)"
          :key="index"
          class="col-md-12 mt-2"
          :class="news.row === 3 ? 'col-lg-4' : 'col-lg-6'"
        >
          <Block
            :hide-date="news.row !== 3"
            :blank="true"
            :type="isMobile ? 'image' : news.row === 3 ? 'image' : 'wide'"
            :news="news"
          />
        </div>
      </div>

      <div v-if="isMobile" id="top-fifth" class="row">
        <div
          v-for="(news, index) in recent.slice(0)"
          :key="index"
          class="col-md-12 mt-2"
          :class="news.row === 3 ? 'col-lg-4' : 'col-lg-6'"
        >
          <Block
            :hide-date="news.row !== 3"
            :blank="true"
            :type="isMobile ? 'image' : news.row === 3 ? 'image' : 'wide'"
            :news="news"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  async asyncData({ app: { $axios }, error }) {
    const respRecent = await $axios.get(
      'posts/recent?limit=5&page=1&exclude=true'
    )

    if (respRecent.data.code !== 200) {
      error({ statusCode: 404 })
    }

    const recent = respRecent.data.body.result
    const pagesLeft = respRecent.data.body.pages_left

    const repTop = await $axios.get('posts/top?limit=5&page=1')

    if (repTop.data.code !== 200) {
      error({ statusCode: 404 })
    }

    const top = repTop.data.body

    return {
      recent,
      top,
      pagesLeft,
    }
  },
  data() {
    return {
      limit: 5,
      i: 9,
      isMobile: false,
      pages_left: false,
    }
  },

  mounted() {
    if (screen.width <= 576) {
      this.isMobile = true
    }

    window.addEventListener('scroll', (event) => {
      if (
        window.innerHeight + Math.ceil(window.pageYOffset) >=
        document.body.offsetHeight - 300
      ) {
        this.fetchNews()
      }
    })
  },

  methods: {
    async fetchNews() {
      this.limit += this.i

      const resp = (
        await this.$axios.get(
          '/posts/recent?limit=' +
            this.limit +
            '&page=1&exclude=true&count=' +
            this.i
        )
      ).data.body

      this.recent = resp.result
      this.pagesLeft = resp.pages_left

      // if (this.i === 3) {
      //   this.i = 2
      // } else {
      //   this.i = 3
      // }
    },
  },

  head() {
    return {
      meta: [
        { property: 'og:title', content: 'Caliber.az' },
        {
          property: 'og:description',
          content:
            'Heydar Mirza и Hans Kloss, авторы еженедельного военно-аналитического проекта RADIUS на Общественном Телевидении Азербайджана запускают независимый русскоязычный военно-аналитический интернет-проект CALIBER, рассчитанный как на широкий круг зрителей, интересующихся военно-политической и военно-технической тематикой с упором на регион Южного Кавказа, так и для экспертов и специалистов в данной области. Война и Южный Кавказ, военная-политическая и военно-техническая аналитика, информационная война и операции психологического воздействия, экспертные мнения и критика – все это и многое другое вы можете смотреть в нашем новом интернет-проекте "CALIBER".',
        },
        {
          property: 'og:image',
          content: process.env.BASE_URI + '/content/og-bg.jpg',
        },
        {
          hid: 'description',
          name: 'description',
          content:
            'Heydar Mirza и Hans Kloss, авторы еженедельного военно-аналитического проекта RADIUS на Общественном Телевидении Азербайджана запускают независимый русскоязычный военно-аналитический интернет-проект CALIBER, рассчитанный как на широкий круг зрителей, интересующихся военно-политической и военно-технической тематикой с упором на регион Южного Кавказа, так и для экспертов и специалистов в данной области. Война и Южный Кавказ, военная-политическая и военно-техническая аналитика, информационная война и операции психологического воздействия, экспертные мнения и критика – все это и многое другое вы можете смотреть в нашем новом интернет-проекте "CALIBER".',
        },
        { property: 'og:url', content: process.env.BASE_URI },
      ],
    }
  },
}
</script>
