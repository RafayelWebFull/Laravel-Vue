import Vue from 'vue'
import Vuex from 'vuex'
import post from "./modules/post";
import auth from "./modules/auth";


Vue.use(Vuex)

export default new Vuex.Store({
  state: {},
  mutations: {},
  getters: {},
  actions: {},
  modules: {
    post: post,
    auth: auth
  }
})
