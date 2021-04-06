import axios from 'axios'
import router from '../../router'

export default {
  state:{
    error: '',
    post: localStorage.getItem("posts") ||  [],
    posts: localStorage.getItem("posts") ||  [],
    bloggerPosts: [],
    comments_reply: [],
    categories:[],
    image: localStorage.getItem("image") || null,
  },
  getters:{
    categories(state){
      return state.categories
    },
    post(state){
      return state.post
    },
    bloggerPosts(state){
      return state.bloggerPosts
    },
    posts(state){
      return state.posts
    },
    image(state){
      return state.image
    },
    CommentsReply(state){
      return state.comments_reply
    },
    error(state){
      return state.error
    }
  },
  mutations: {
    storeError(state,data){
      state.error = data
    },
    storePost(state,data){
      localStorage.setItem("post",JSON.stringify(data))
      state.post = data
    },
    storeCommentsReply(state, data){
      state.comments_reply = data
    },
    storePosts(state,data){
      state.posts = data
    },
    storeBloggerPosts(state,data){
      state.bloggerPosts = data
    },
    storeImage(state,data){
      state.image = data
      // localStorage.setItem("image",data)
    },
    storeCategories(state,data){
      state.categories = data
    }
  },
  actions: {
    currectPosts(ctx, data) {
      return new Promise((res, rej) => {
        axios.post("/bloggerPosts?page=" + data[1], [data[0]]).then((res) => {
          ctx.commit("storeBloggerPosts", res.data.data)

          // ctx.commit("storePosts", res.data.data)
        })
      })
    },
    index(ctx, page) {
      return new Promise((res, rej) => {
        axios.get('/posts?page=' + page).then(res => {
          localStorage.setItem("posts", JSON.stringify(res.data.data))
          ctx.commit("storePosts", res.data.data)
          ctx.commit("storeCommentsReply", res.data.comments_reply)
          ctx.commit("storeCategories", res.data.categories)

        })
      })
    },
    comment(ctx, data) {
      return new Promise((res, rej) => {
        axios.post("/comment", data).then((res) => {
          let posts = ctx.state.posts
          posts.data[res.data.post_index] = res.data.data[0]
          localStorage.removeItem("posts")
          ctx.commit("storePosts", "")
          ctx.commit("storePosts",posts)
        })
      })
    },
    commentReply(ctx,data){
      return new Promise((res,rej) => {
        axios.post("/commentReply",data).then((res) => {
            ctx.commit("storeCommentsReply",res.data.data)
        })
      })
    },
    commentReplyBlogger(ctx,data){
      return new Promise((res,rej) => {
        axios.post("/commentReplyBlogger",data).then((res) => {
          ctx.commit("storeCommentsReply",res.data.data)
        })
      })
    },
    newPost(ctx, data) {
       return axios.post("/newPosts", data).then((res) => {
          ctx.commit("storeError", "")
          router.push("/blogger")
         return res
        }).catch((error) => {
          if (error.response.status == 422) {
            ctx.commit("storeError", error.response.data.errors)
          }

        })

    },
    editPost(ctx, data) {
      return new Promise((res, rej) => {
        axios.post("/editPost", data).then((res) => {
          ctx.commit("storePost", res.data.data)
        })
      })
    },
    updatePost(ctx, data) {
      return new Promise((res, rej) => {
        axios.post("/updatePost", data).then((res) => {
          ctx.commit("storePost", res.data.data)
          router.push("/blogger")
        })
      })
    },
    delete(ctx, data) {
      return new Promise((res, rej) => {
        axios.post("/deletePost", data).then((res) => {
          localStorage.setItem("posts",JSON.stringify(res.data.data))
          ctx.commit("storeBloggerPosts", res.data.data)
          // ctx.commit("storePosts", res.data.data.data)
        })
      })
    },
    show(ctx, data) {
      axios.post("/showPost", [data]).then((res) => {
        ctx.commit("storePost", res.data.data[0])
        ctx.commit("storeCommentsReply", res.data.comment_reply)
        router.push("/showPost")
      })
    },
    commentPost(ctx, data) {
      return new Promise((res, rej) => {
        axios.post("/addComment", data).then((res) => {
          ctx.commit("storePost", res.data.data[0])
          ctx.commit("storeCommentsReply", res.data.comment_reply)
        })
      })
    },
    admin(ctx, page) {
      return new Promise((res, rej) => {
        axios.post("/newPostsList?page=" + page).then((res) => {
            ctx.commit("storePosts",res.data.data)
        })
      })
    },
    allPosts(ctx,page){
      return new Promise((res, rej) => {
        axios.post("/allPosts?page=" + page).then((res) => {
          ctx.commit("storePosts",res.data.data)
        })
      })
    }
  }
}
