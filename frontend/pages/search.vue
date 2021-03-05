<template>
  <div class="container pb-3">
    <!--    <div class="row justify-content-end">-->
    <!--      <div class="col-md-1 close">-->
    <!--        <span @click="close">&#10005;</span>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--    <div class="row">-->
    <!--      <div class="col-md-12">-->
    <!--        <input v-model="keyword" type="text" @keyup="search" />-->
    <!--      </div>-->
    <!--    </div>-->

    <div v-for="(news, index) in results" :key="index" class="row">
      <a :href="'/post/' + news.slug" class="col-md-12 mb-3">
        <div class="result-block">
          <div class="time">{{ news.date }}</div>
          <div class="content-wrapper">
            <div class="image">
              <img :src="news.image" :alt="news.name" />
            </div>

            <div class="content">
              <span class="tag">{{ news.category_name }}</span>
              <span class="title">{{ news.name }}</span>
              <span class="description">{{ news.desc }}</span>
            </div>
          </div>
        </div>
      </a>
    </div>

    <div v-show="searched && results.length === 0" class="row no-result">
      <div class="col-md-12">
        <span>НИЧЕГО НЕ НАЙДЕНО</span>
        <span>Попробуйте еще раз</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Search',
  layout: 'search',

  data() {
    return {
      keyword: '',
      results: [],
      page: 1,
      searched: false,
      timeout: null,
      has_pages: false,
    }
  },

  async mounted() {
    this.keyword = this.$route.query.keyword

    const _this = this

    if (_this.keyword === '') {
      return
    }

    _this.results = []

    const resp = (
      await _this.$axios.get(
        '/search?limit=10&page=1&keyword=' + encodeURIComponent(_this.keyword)
      )
    ).data.body

    _this.results = [..._this.results, ...resp.result]

    _this.searched = true

    _this.has_pages = resp.pages_left

    _this.page++

    window.addEventListener('scroll', (event) => {
      if (
        window.innerHeight + Math.ceil(window.pageYOffset) >=
        document.body.offsetHeight
      ) {
        this.fetchResults()
      }
    })
  },

  methods: {
    search() {
      clearTimeout(this.timeout)

      const _this = this

      this.timeout = setTimeout(async function () {
        if (_this.keyword === '') {
          return
        }

        _this.results = []

        const resp = (
          await _this.$axios.get(
            '/search?limit=10&page=1&keyword=' +
              encodeURIComponent(_this.keyword)
          )
        ).data.body

        _this.results = [..._this.results, ...resp.result]

        _this.searched = true

        _this.has_pages = resp.pages_left

        _this.page++
      }, 1000)
    },

    close() {
      window.location = '/'
    },

    async fetchResults() {
      if (this.keyword === '') {
        return
      }

      const resp = (
        await this.$axios.get(
          '/search?limit=10&page=' +
            this.page +
            '&keyword=' +
            encodeURIComponent(this.keyword)
        )
      ).data.body

      this.results = [...this.results, ...resp.result]

      this.has_pages = resp.pages_left

      this.searched = true

      this.page++
    },
  },

  head() {
    return {
      meta: [
        {
          name: 'robots',
          content: 'noindex,nofollow',
        },
      ],
    }
  },
}
</script>
