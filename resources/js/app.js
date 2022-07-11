require('./bootstrap');



import { createApp, Vue }  from 'vue';
import App from './components/App.vue'
import router from './router';
import store from './store';


axios.defaults.withCredentials = true;

store.dispatch('getUser').then(()=>{
    let app=createApp(App);
    app.use(router);
        app.use(store);
        app.mount("#app");
        app.config.globalProperties.$filters = {
            valueFilter(value) {
              return value.toFixed(0);
            },
            
          }
})

