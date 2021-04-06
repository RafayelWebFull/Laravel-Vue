import axios from 'axios'
import router from '../../router'

export default{
  state: {
    errors:  null,
    loginMassage: null,
    uploadedImagePath: null,
    access_token: localStorage.getItem('access_token'),
    user: JSON.parse(localStorage.getItem('user')) || null ,
    bloggerList :JSON.parse(localStorage.getItem('bloggers')) || []
  },
  mutations: {
    storeBloggerList(state,data){
      state.bloggerList = data
      localStorage.setItem('bloggers', JSON.stringify(data));
    },
    storeErrors(state,data){
      state.errors = data
    },
    storeToken(state, data){
      state.access_token = data
      localStorage.setItem('access_token', data)
    },
    storeUserData(state, data){
      localStorage.setItem('user', JSON.stringify(data));
      state.user = data
    },
    storeImage(state, data){
      state.uploadedImage = data
    },
    storeLoginMassage(state, data){
      state.loginMassage = data
    },
    storeRegisterMassage(state, data){
      state.registerErrors = data
    },
    storePasswordTrue(state,data){
      state.passwordTrue = data
    }
  },
  getters: {
    errors(state){
      return state.errors
    },
    loggedIn(state){
      return state.access_token
    },
    listBlogger(state){
      return state.bloggerList
    },
    authUser(state){
      return state.user
    },
    storeImage(state){
      return state.uploadedImage
    },
    LoginMassage(state){
      return state.loginMassage
    },
    passwordTrue(state){
      return state.passwordTrue
    }
  },
  actions: {
    login(ctx, form){
      return new Promise((res, rej)=> {
        axios.post('/auth/login', form).then(res=> {
          if(res.data.massage){
              ctx.commit("storeLoginMassage",res.data.massage)
          }else{
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + res.data.access_token;
            ctx.commit('storeToken', res.data.access_token)
            ctx.dispatch('updateUser');
          }

        })
      })
    },
    changePassword(ctx,data){
      return new Promise((res,rej) => {
        axios.post("/auth/changePassword",data).then((res) => {
          ctx.commit("storeUserData",res.data.data)
          location.reload()
        })
      })
    },

    updateUser(ctx){
      return new Promise((responses, rej)=> {
        axios.post('/auth/me').then(response => {
          if(response.data.block_time != null){
            axios.post("auth/checkTime",[response.data.name]).then((res) => {
              ctx.commit('storeUserData', res.data.data);
              router.push("/blogger")
            })
          }else if(response.data.nickname == "Admin"){
            router.push("/admin")
            ctx.commit('storeUserData', response.data);
          }else{
            router.push("/")
            ctx.commit('storeUserData', response.data);
          }
        }).catch((res) => {
          ctx.commit('storeUserData', res.data);
        })
      })
    },
    EditProfile(ctx,data){
       return axios.post("/auth/avatar-upload",data).then(res => {
          ctx.commit("storeErrors",null)
          ctx.commit("storeUserData",res.data.data)
          return res.data.data
        }).catch((error) => {
          if (error.response.status == 422) {
            ctx.commit("storeErrors", error.response.data.errors)
            return error.response.data.errors
          }
        })

    },
    register(ctx,fData){
      return new Promise((res,rej) => {
        axios.post(`/auth/register`, fData, {headers: { 'Content-Type': 'multipart/form-data'}}).then((res) => {
          router.push("/login")
        }).catch((error) => {
          if (error.response.status == 422) {
            ctx.commit("storeError", error.response.data.errors)
          }
        })
      })
    },
    verify(ctx, data){
      return new Promise((res, rej)=> {
        axios.post('/auth/verify', data).then(res => {
          if(res.data.success){
            // return router.push({name: ''})
          }
        })
      })
    },
    bloggerList(ctx,page){
      return new Promise((res,rej) => {
        axios.get("auth/bloggerList?page=" + page).then((res) => {
            ctx.commit("storeBloggerList",res.data.data)
        })
      })
    }
  }
}
