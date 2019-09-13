import axios from "axios";

const Api = axios.create({
  baseURL: window.wpApiSettings.url,
  headers: { "X-WP-Nonce": window.wpApiSettings.nonce }
});

Api.interceptors.response.use(
  response => {
    return response;
  },
  error => {
    // const { data, status } = error.response;
    // Do something when the request fails (e.g. show a notice)
    return Promise.reject(error);
  }
);

export default Api;
