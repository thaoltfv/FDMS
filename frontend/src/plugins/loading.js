import Vue from 'vue';
import LoadingComponent from '@/components/Loading.vue'; 

const LoadingConstructor = Vue.extend(LoadingComponent);

const Loading = {
  install(Vue) {
    Vue.prototype.$loading = {
      instance: null, 
      show() {
        if (!this.instance) {
          const instance = new LoadingConstructor({
            el: document.createElement('div'),
          });

          document.body.appendChild(instance.$el);
          this.instance = instance; 
        }
      },
      hide() {
        if (this.instance) {
          this.instance.$destroy(); 
          this.instance.$el.parentNode.removeChild(this.instance.$el);
          this.instance = null;
        }
      },
    };
  },
};

export default Loading;