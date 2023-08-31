//Might work on older versions but the uncommented way works for symfony 6
// import Vue from 'vue';
// import App from './components/App.vue';

// new Vue({
//     el: '#app',
//     components: { App }
// });
import { createApp } from 'vue'; // Import createApp
import App from './components/App.vue';

const app = createApp(App);
app.mount('#app');