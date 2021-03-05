<template>
  <div class="w-100">
    <div class="container box">
      <div class="row flex-wrap">
        <div
          v-for="(news, index) in blocks"
          :key="index"
          class="col-md-4 col-lg-6 col-xl-4 col-md-6"
        >
          <Block :news="news"></Block>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-center">
          <button class="btn" @click="fetchNews">Загрузить ещё</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Index',

  async asyncData({ params, app: { $axios }, error }) {
    const username = params.username

    const postsResp = await $axios.get(
      '/user/' + username + '/posts?limit=9&page=1'
    )
    if (postsResp.data.code !== 200) {
      error({ statusCode: 404 })
    }

    const blocks = postsResp.data.body

    return { blocks, username }
  },

  data() {
    return {
      page: 2,
    }
  },

  methods: {
    async fetchNews() {
      const limit = this.page * 9

      this.blocks = (
        await this.$axios.get(
          '/user/' + this.username + '/posts?limit=' + limit + '&page=1'
        )
      ).data.body

      this.page++
    },
  },

  head() {
    return {
      meta: [
        { property: 'og:title', content: 'Caliber - ' + this.username },
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
        {
          property: 'og:url',
          content: process.env.BASE_URI + '/user/' + this.username + '/posts',
        },
      ],
    }
  },
}
</script>
