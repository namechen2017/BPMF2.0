import Vue from 'vue';
import App from './App';
import router from './router';
import axios from 'axios';
import ElementUI from 'element-ui';
import qs from 'qs'
/*import Counter from './components/page/workOrder/creatOrder.vue'*/
/*import store from './vuex/store'*/

import 'element-ui/lib/theme-default/index.css';    // 默认主题
// import '../static/css/theme-green/index.css';       // 浅绿色主题
import "babel-polyfill";
import './mock/index.js';



import VueCookie from 'vue-cookie';
// Tell Vue to use the plugin
Vue.use(VueCookie);
Vue.use(qs);


Vue.use(ElementUI);
Vue.prototype.$axios  = axios;
new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
