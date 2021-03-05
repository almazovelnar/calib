export default ({ app: { $axios, $action }, redirect }) => {
  $axios.setHeader('Accept', 'application/json')

  $axios.onError((error) => {
    const code = parseInt(error.response && error.response.status)

    if (process.server) {
      return Promise.reject(error)
    }

    let message = 'Whoops :( Something went wrong.'

    switch (code) {
      case 404: {
        return redirect('/')
      }
      case 400: {
        message = error.response.data.message
        break
      }
      case 422: {
        message = error.response.data.message
        $action.setErrors(error.response.data.body)
        break
      }
    }

    return Promise.reject(message)
  })
}
