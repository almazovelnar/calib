<template>
  <div class="w-100">
    <div class="container box">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <!-- eslint-disable vue/no-v-html -->
          <article
            style="margin-bottom: 50px"
            v-html="this.$helpers.settings().about_us_text"
          />
          <!--eslint-enable-->
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-10">
          <iframe
            width="100%"
            height="600"
            frameborder="0"
            scrolling="no"
            marginheight="0"
            marginwidth="0"
            :src="
              'https://maps.google.com/maps?q=' +
              this.$helpers.settings().lat +
              ',' +
              this.$helpers.settings().long +
              '&hl=ru&z=14&amp;output=embed'
            "
          >
          </iframe>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AboutUs',

  head() {
    return {
      meta: [
        { property: 'og:title', content: 'О НАС' },
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
