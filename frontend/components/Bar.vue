<template>
  <div
    class="w-100 news-bar-wrapper"
    :class="open ? 'open' : ''"
    @click="open = false"
  >
    <div class="container">
      <div class="row news-bar" :class="{ open: open }">
        <div v-show="!open" class="col-md-3">
          <span class="title" @click.stop="toggleBar">ЛЕНТА НОВОСТЕЙ</span>
          <ul id="custom-tns-nav">
            <li>
              <button aria-label="Next Item" />
            </li>
            <li>
              <button aria-label="Next Item" />
            </li>
            <li>
              <button aria-label="Next Item" />
            </li>
            <li>
              <button aria-label="Next Item" />
            </li>
            <li>
              <button aria-label="Next Item" />
            </li>
            <li>
              <button aria-label="Next Item" />
            </li>
            <li>
              <button aria-label="Next Item" />
            </li>
          </ul>
        </div>
        <div v-show="open" class="col-md-3 rotated">
          <div>
            <span class="title">ЛЕНТА НОВОСТЕЙ</span>
          </div>
        </div>
        <div class="col-md-8 col-11">
          <XSlider
            v-show="!open"
            name="news-bar-slider"
            :items="2"
            :gutter="44"
            nav-position="top"
            :autoplay="true"
            :nav="true"
            :ncontainer="'#custom-tns-nav'"
            :controls="false"
            :responsive="{
              768: {
                items: 2,
              },
            }"
          >
            <div
              v-for="(newsBarBlock, index) in news.result[
                Object.keys(news.result)[0]
              ].slice(0, 6)"
              :key="index"
              class="news-bar-block"
            >
              <a :href="'/post/' + newsBarBlock.slug" class="wrapper">
                <span class="time">{{ newsBarBlock.date }}</span>
                <span class="text">{{ newsBarBlock.name }}</span>
              </a>
            </div>
          </XSlider>
          <div v-show="open" class="col-md-11 bar-open">
            <ul v-for="(newsBarBlock, index) in news.result" :key="index">
              <li style="color: red">{{ index }}</li>
              <li
                v-for="(newsBarBlockItem, jindex) in newsBarBlock"
                :key="jindex"
              >
                <a :href="'/post/' + newsBarBlockItem.slug">
                  <span class="time">{{ newsBarBlockItem.date }}</span>
                  <span class="text">{{ newsBarBlockItem.name }}</span>
                </a>
              </li>
            </ul>
            <button
              v-if="news.pages_left"
              class="btn m-auto mb-3"
              @click.stop="fetchNews"
            >
              Загрузить еще
            </button>
          </div>
        </div>
        <div id="up" class="col-md-1 text-right col-1">
          <div>
            <button @click.stop="toggleBar">
              <transition name="fade">
                <img
                  v-show="!open"
                  class="up"
                  src="/content/grey-up.svg"
                  alt="Show all news"
                />
              </transition>
              <transition name="fade">
                <img
                  v-show="open"
                  class="down"
                  src="/content/grey-up.svg"
                  alt="Show all news"
                />
              </transition>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Bar',

  props: {
    news: {
      required: true,
      type: Object,
    },
  },

  data() {
    return {
      open: false,
      page: 1,
      limit: 20,
      pages_left: false,
    }
  },

  mounted() {
    let request = true

    setInterval(() => {
      if (request) {
        this.$axios
          .get(
            'posts/recent?bar=true&short=true&limit=' + this.limit + '&page=1',
            {
              progress: false,
            }
          )
          .then((resp) => {
            this.$emit('update:news', resp.data.body)
            request = true
          })
      }

      request = false
    }, 3000)
  },

  methods: {
    toggleBar() {
      if (this.open) {
        this.open = false
      } else {
        document
          .getElementsByClassName('news-bar-wrapper')[0]
          .classList.add('shadow')

        this.open = true
      }
    },

    fetchNews() {
      this.limit += 20

      this.$axios
        .get('posts/recent?bar=true&short=true&limit=' + this.limit + '&page=1')
        .then((resp) => {
          this.$emit('update:news', resp.data.body)
        })
    },

    goTo(to) {
      this.open = false

      window.location = to
    },
  },
}
</script>
