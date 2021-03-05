import { tns } from 'tiny-slider/src/tiny-slider'

export default ({ app }, inject) => {
  inject('slider', {
    run(settings) {
      return tns(settings)
    },
  })
}
