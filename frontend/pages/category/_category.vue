<template>
  <div class="w-100">
    <div class="container box">
      <div class="row flex-wrap">
        <div
          v-for="(news, index) in blocks"
          :key="index"
          class="col-md-4 col-lg-6 col-xl-4 col-md-6"
        >
          <Block :blank="true" :news="news"></Block>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Index',

  async asyncData({ params, app: { $axios }, error }) {
    const categoryResp = await $axios.get('/category/' + params.category)

    if (categoryResp.data.code !== 200) {
      error({ statusCode: 404 })
    }

    const category = categoryResp.data.body

    const postsResp = await $axios.get(
      '/category/' + category.slug + '/posts?limit=9&page=1'
    )

    if (postsResp.data.code !== 200) {
      error({ statusCode: 404 })
    }

    const blocks = postsResp.data.body.result
    const pagesLeft = postsResp.data.body.pages_left

    return { blocks, category, pagesLeft }
  },

  data() {
    return {
      page: 2,
      offsetBottom: 999,
    }
  },

  mounted() {
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
      const limit = this.page * 9

      const resp = (
        await this.$axios.get(
          '/category/' +
            this.category.slug +
            '/posts?limit=' +
            limit +
            '&page=1'
        )
      ).data.body

      this.blocks = resp.result

      this.pagesLeft = resp.pages_left
      this.page++
    },
  },

  head() {
    return {
      title: this.category.title,
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: this.category.description,
        },
        {
          property: 'og:title',
          content: this.category.title,
        },
        {
          property: 'og:description',
          content: this.category.description,
        },
        {
          property: 'og:image',
          content: process.env.BASE_URI + '/content/logo.png',
        },
        {
          name: 'twitter:title',
          content: this.category.title,
        },
        {
          name: 'twitter:description',
          content: process.env.BASE_URI + '/content/logo.png',
        },
        {
          name: 'twitter:image',
          content: process.env.BASE_URI + '/content/logo.png',
        },
      ],
    }
  },
}
</script>
