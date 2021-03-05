<template>
  <div class="w-100">
    <div class="container box">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <h4>
            {{ data.date }}&nbsp;&nbsp;{{ data.tag }}
            <a
              v-if="editnow"
              :href="'https://admin.caliber.az/news/' + id + '/edit'"
              >Edit</a
            >
          </h4>
          <h1>{{ data.name }}</h1>

          <p class="font-sizer">
            <span @click="reduceFontSize">-</span> A
            <span @click="increaseFontSize">+</span>
          </p>

          <NuxtLink
            v-if="data.show_user"
            :to="'/user/' + data.user.username + '/posts'"
            tag="h2"
            >{{ data.user.name }}
          </NuxtLink>

          <img
            v-if="data.show_img"
            :src="data.image"
            class="mb-3 w-100 h-auto"
            :alt="data.name"
          />

          <!-- eslint-disable vue/no-v-html -->
          <article v-html="data.content" />
          <!--eslint-enable-->

          <client-only v-if="data.gallery && data.gallery.length > 0">
            <div class="mb-3 img">
              <XSlider
                name="inner"
                :items="1"
                :gutter="31"
                :autoplay="true"
                :nav="true"
                :controls="true"
                :responsive="{
                  1200: {
                    items: 3,
                  },

                  992: {
                    items: 2,
                  },
                }"
              >
                <div
                  v-for="(img, index) in data.gallery"
                  :key="index"
                  :data-id="index"
                  class="img-wrapper"
                >
                  <img :data-id="index" :src="img" alt="" />
                </div>
              </XSlider>
            </div>
          </client-only>

          <div class="count">
            <span>
              ПРОСМОТРЕНО: <b>{{ view }}</b>
            </span>
            <ul class="p-0 m-0 text-right">
              <li>
                <a @click="shareFacebook">
                  <img src="/content/fb.png" alt="facebook" />
                </a>
              </li>
              <li>
                <a @click="shareWhatsapp">
                  <img src="/content/wp.png" alt="whatsapp" />
                </a>
              </li>
              <li>
                <a @click="shareTelegram">
                  <img src="/content/tg.png" alt="telegram" />
                </a>
              </li>
              <li>
                <a @click="shareTwitter"
                  ><img src="/content/tw.png" alt="twitter"
                /></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div v-if="related.length > 0" class="container box related">
      <div class="row flex-wrap">
        <div
          v-for="(news, index) in related"
          :key="index"
          class="col-md-4 col-lg-4 col-md-6"
        >
          <Block :news="news"></Block>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Slug',

  async asyncData({ params, app: { $axios }, error }) {
    const slug = params.slug

    const resp = await $axios.get('/post/' + slug)

    if (resp.data.code !== 200) {
      error({ statusCode: 404 })
    }

    const data = resp.data.body

    const id = slug.split('-').pop()

    return { data, slug, id }
  },

  data() {
    return {
      editnow: false,
      scrollY: null,
      related: [],
      limit: 0,
      view: '',
      pages_left: false,
    }
  },

  watch: {
    scrollY(current) {
      // if (current + window.innerHeight === document.body.offsetHeight) {
      //   this.fetchRelated()
      // }
    },
  },

  async mounted() {
    this.editnow =
      (
        await this.$axios.get('/check', {
          proxy: false,
          baseURL: 'https://admin.caliber.az',
        })
      ).status === 200

    await this.$axios.get('/post/' + this.slug + '/view').then((resp) => {
      this.view = resp.data.body.view + 1
    })

    window.addEventListener('scroll', (event) => {
      if (
        window.innerHeight + Math.ceil(window.pageYOffset) >=
        document.body.offsetHeight - 300
      ) {
        this.fetchRelated()
      }
    })

    const _this = this

    if (screen.width > 576) {
      window.addEventListener(
        'load',
        function () {
          const imgs = document.querySelectorAll('.img-wrapper img')

          for (let i = 0; i < imgs.length; i++) {
            imgs[i].addEventListener('click', function (event) {
              const img = document.createElement('img')
              const div = document.createElement('div')

              div.classList.add('img')

              img.setAttribute('src', event.target.src)

              if (document.querySelector('body #imgwrapper > .img'))
                document.querySelector('body #imgwrapper > .img').remove()

              document
                .querySelector('body #imgwrapper')
                .classList.remove('d-none')

              div.appendChild(img)

              const left = document.createElement('button')
              left.classList.add('left')

              const right = document.createElement('button')
              right.classList.add('right')

              left.addEventListener('click', function (e) {
                e.preventDefault()
                e.stopImmediatePropagation()
                e.stopPropagation()

                let updateId = parseInt(event.target.dataset.id) - 1
                if (updateId <= 0) {
                  updateId = _this.data.gallery.length - 1
                }

                const element = document.querySelector(
                  '.img-wrapper[data-id="' + updateId + '"] img'
                )

                if (element) element.click()
              })

              div.appendChild(left)
              div.appendChild(right)

              right.addEventListener('click', function (e) {
                e.preventDefault()
                e.stopImmediatePropagation()
                e.stopPropagation()

                let updateId = parseInt(event.target.dataset.id) + 1

                if (updateId >= _this.data.gallery.length - 1) {
                  updateId = 0
                }

                const element = document.querySelector(
                  '.img-wrapper[data-id="' + updateId + '"] img'
                )

                if (element) element.click()
              })

              document.querySelector('body #imgwrapper').appendChild(div)
            })
          }
        },
        false
      )
    }

    window.instgrm.Embeds.process()

    const tgScript = document.createElement('script')
    tgScript.setAttribute(
      'src',
      'https://telegram.org/js/telegram-widget.js?14'
    )
    document.body.appendChild(tgScript)

    for (let i = 0; i < document.querySelectorAll('article img').length; i++) {
      const element = document.querySelectorAll('article img')[i]

      const alt = element.getAttribute('alt')

      if (typeof alt === typeof undefined || alt === false || alt === null) {
        element.setAttribute('alt', element.src)
      } else {
        const small = document.createElement('small')
        const content = document.createTextNode(alt)
        small.appendChild(content)

        element.parentNode.insertBefore(small, element.nextSibling)
      }
    }

    window.addEventListener('scroll', (event) => {
      this.scrollY = Math.round(window.scrollY)
    })

    this.fetchRelated()
  },

  methods: {
    zoomImage(event) {
      if (screen.width >= 576) {
        const img = document.createElement('img')

        img.setAttribute('src', event.target.src)

        if (document.querySelector('body>img'))
          document.querySelector('body>img').remove()

        document.querySelector('body #imgwrapper').classList.remove('d-none')

        document.querySelector('body #imgwrapper').appendChild(img)
      }
    },

    fetchRelated() {
      this.limit += 9

      this.$axios
        .get('/post/' + this.slug + '/related?limit=' + this.limit + '&page=1')
        .then((resp) => {
          this.related = resp.data.body.result
          this.pages_left = resp.data.body.pages_left
        })
    },

    shareFacebook() {
      window.open(
        'https://www.facebook.com/sharer/sharer.php?u=' +
          encodeURIComponent(window.location.href),
        'facebook-share-dialog',
        'width=626,height=436'
      )
    },

    shareWhatsapp() {
      window.open(
        'whatsapp://send?text=' + encodeURIComponent(window.location.href),
        'whatsapp-share-dialog',
        'width=626,height=436'
      )
    },

    shareTelegram() {
      window.open(
        'https://t.me/share?url=' + encodeURIComponent(window.location.href),
        'facebook-share-dialog',
        'width=626,height=436'
      )
    },

    shareTwitter() {
      window.open(
        'https://twitter.com/intent/tweet?url=' +
          encodeURIComponent(window.location.href),
        'twitter-share-dialog',
        'width=626,height=436'
      )
    },

    reduceFontSize() {
      const text = document.querySelectorAll('article p')

      for (let i = 0; i < text.length; ++i) {
        const style = window.getComputedStyle(text[i], null)

        if (parseFloat(style.getPropertyValue('font-size')) - 1 > 13) {
          text[i].style.fontSize =
            parseFloat(style.getPropertyValue('font-size')) - 1 + 'px'
        }

        if (parseFloat(style.getPropertyValue('line-height')) - 5 > 14) {
          text[i].style.lineHeight =
            parseFloat(style.getPropertyValue('line-height')) - 5 + 'px'
        }
      }
    },

    increaseFontSize() {
      const text = document.querySelectorAll('article p')

      for (let i = 0; i < text.length; ++i) {
        const style = window.getComputedStyle(text[i], null)

        text[i].style.fontSize =
          parseFloat(style.getPropertyValue('font-size')) + 1 + 'px'

        text[i].style.lineHeight =
          parseFloat(style.getPropertyValue('line-height')) + 5 + 'px'
      }
    },
  },

  head() {
    return {
      title: this.data.title,
      link: [
        {
          rel: 'canonical',
          href: process.env.BASE_URI + this.$route.path,
        },
      ],
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: this.data.description,
        },
        {
          name: 'keywords',
          content: this.data.keywords,
        },
        {
          property: 'og:title',
          content: this.data.title,
        },
        {
          property: 'og:url',
          content: process.env.BASE_URI + '/post/' + this.slug,
        },
        {
          property: 'og:description',
          content: this.data.description,
        },
        {
          property: 'og:image',
          content: this.data.image,
        },
        {
          name: 'twitter:title',
          content: this.data.title,
        },
        {
          name: 'twitter:description',
          content: this.data.description,
        },
        {
          name: 'twitter:image',
          content: this.data.image,
        },
      ],
    }
  },
}
</script>
