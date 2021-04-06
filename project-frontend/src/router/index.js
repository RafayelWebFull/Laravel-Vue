import Vue from 'vue'
import Router from 'vue-router'
import Index from "../components/Index";
import user from "./user"
import blogger from "./blogger";
import admin from "./admin";

Vue.use(Router)

export default new Router({
  mode: "history",
  routes: [
    {
      path: '/', name: 'Index', component: Index,
    },
    ...user,
    ...blogger,
    ...admin
  ]
})
