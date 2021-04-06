<template>
  <div>
    <b-navbar toggleable="lg" type="dark" variant="secondary">
      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <b-navbar-brand href="/">Home</b-navbar-brand>
        </b-navbar-nav>
        <b-navbar-nav v-if="this.user == null" class="ml-auto mr-5">
          <b-nav-item right>
            <router-link :to="{name: 'Login'}">
              <b-button  variant="success">Login</b-button>
            </router-link>

          </b-nav-item>
          <b-nav-item right>
            <router-link :to="{name : 'Register'}">
              <b-button variant="primary">Register</b-button>
            </router-link>
          </b-nav-item>
        </b-navbar-nav>
        <b-navbar-nav v-if="this.user != null"  class="ml-auto mr-5">
         <h4 class="text-dark" > {{ this.user.name}}</h4>
          <b-nav-item-dropdown class="text-dark"  right>
            <!-- Using 'button-content' slot -->
            <b-dropdown-item href=""><router-link :to="{name: 'Profile'}">Profile</router-link></b-dropdown-item>
            <b-dropdown-item-button @click="signOut()">Sign Out</b-dropdown-item-button>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>

    <div class="main">
      <div class="filters  d-flex">
      <div class="search active-cyan-4 w-100" >
        <input class="form-control" type="text" placeholder="Search" v-model="search_filter" aria-label="Search"/>
      </div>
      <div>
        <form action="">
          <label for="select">
          <select id="select" style="width: 200px;height: 40px " :onchange="onChangeCategory()" v-model="selected"  class="ml-2"  >
              <option disabled value="">Select Category</option>
              <option  v-for="category in categories" :value="category.id" >{{ category.category_name}}</option>
          </select>
          </label>
          <input type="button" @click="reset()" value="Reset" class="ml-2">
        </form>
      </div>
      </div>
      <div @per_page="perPage" class="d-flex row "  v-for="(post,index) in this.posts.data" >
        <div class="posts m-auto">
          <div  class="card mb-5">
            <div class="d-flex">
              <h2 >{{ post.title}} </h2><p class="ml-auto">Created By : {{post.author}}</p>
              <h5  class="ml-1">{{ post.created_time }}</h5>
            </div>
            <div style="height: 200px;">
              <img width="200px"  :src="`http://task.loc/storage/avatar/${post.image}`" alt="">
            </div>
            <p>{{post.description}}</p>
            <div class="d-flex">
<!--            <h4>Comments:{{ post.comments.length}}</h4>-->
              <h4 class="ml-auto">Category:{{ post.category.category_name}}</h4>
            </div>
            <div class="comment rounded mb-1"  v-for="(comment,index) in post.comments">
              <div class="d-inline">
                <div style="width: 300px">
                  <h6 class="d-block">{{index  + 1 +"." + comment.comment_author }}</h6>
                </div>
                <p class="ml-3 ">{{comment.comment}}</p>
                    <div class="bg-danger ml-4 align-content-center p-2" v-if="comment_reply.comment_id === comment.id" v-for="comment_reply in comments_reply">
                      <h5 class="mb-1">{{comment_reply.comment_reply_author}}</h5>
                        <p class="ml-2">{{comment_reply.comment_reply}}</p>
                    </div>
                <form v-if="userInfo != null"  @submit.prevent="onReply"  class="d-flex">
                  <b-input class="mt-1 ml-3 " style="width: 950px" type="text" :data-title="user.name" :data-comment-id="comment.id"  placeholder="Comment Reply"></b-input>
                  <b-button size="sm" type="submit" class="ml-1">Reply</b-button>
                </form>
              </div>
            </div>
            <div v-if="userInfo != null">
              <form  @submit.prevent="onSubmit"  class="d-flex">
                <b-form-input placeholder="Comment Post" :data-post-id="post.id" :data-id="index"  type="text" ></b-form-input>
                <b-button  type="submit" class="ml-1">Comment</b-button>
              </form>
            </div>
      </div>
    </div>
      </div>
      <b-pagination class="justify-content-center pagination-lg" :total-rows="posts.total" aria-controls="posts" @page-click="getResults"  v-model="currentPage" :per-page="posts.per_page" ></b-pagination>
  </div>
    </div>
</template>

<script>
import axios from 'axios'
import router from '../router'
import user from "../router/user";
export default {

  data(){
    return{
      perPage: 3,
      currentPage: 1,
      field: [
        {key: "title",label: "Title"},
        {key: "description",label: "Description"},
        {key: "author",label: "Author"},
        "image",
        {key: "category.category_name",label: "Category Name"},
        "status",
        {key: "created_time",label: "Created Time",sortable: true},
      ],
      filtered_posts: [],
      search_filter: "",
      selected: "",
      comments:{
        comment: '',
        post_id: '',
        author: '',
        id: ''
      },
      comment_reply:{
        comment_reply: '',
        comment_id : '',
        comment_reply_author: '',
      },
      userInfo: null,
    }
  },
  mounted() {
    this.user
    if(this.userInfo){
      if(this.user.role == "blogger"){
        router.push("/blogger")
      }else if(this.user.role == "admin"){
        router.push("/admin")
      }
    }

   this.$store.dispatch("index",1)
  },
  created() {
    if(localStorage.getItem("user")) {
      this.userInfo = localStorage.getItem("user")
    }
  },
  computed: {
    user(){
      return this.$store.getters.authUser
    },
    posts() {
      return this.$store.getters.posts
    },
    comments_reply() {
      return this.$store.getters.CommentsReply;
    },
    categories(){
        return this.$store.getters.categories
    },

    newComment(){
      return this.$store.getters.newComment
    }
  },
  methods:{
    reset(){
      location.reload()
    },
    onReply(evt){
      this.comment_reply.comment_reply = evt.target[0].value
      evt.target[0].value= ""
      this.comment_reply.comment_id = evt.target[0].getAttribute("data-comment-id")
      this.comment_reply.comment_reply_author = this.user.name
      this.$store.dispatch("commentReply",this.comment_reply)
      // this.comments.id = evt.target[0].getAttribute("data-id")
    },
    getResults(evt){
      this.$store.dispatch("index",evt.target.ariaPosInSet)
    },
    onChangeCategory(){
       return this.selected
    },
    signOut(){
      axios.post("/auth/logout")
      localStorage.removeItem("user")
      localStorage.removeItem("access_token")
      location.reload()

    },
    onSubmit(evt){
      this.comments.comment = evt.target[0].value
      evt.target[0].value= ""
      this.comments.post_id = evt.target[0].getAttribute("data-post-id")
      this.comments.author = this.user.name
      this.comments.id = evt.target[0].getAttribute("data-id")
      this.$store.dispatch("comment",this.comments)
    }
  },
  watch:{
    search_filter(){
      if(this.search_filter != "") {
        axios.post("/search", [this.search_filter, this.selected]).then((res) => {
          console.log(res)
          this.$store.commit("storePosts", res.data.data)
        })
      }
    },
    selected(){
      let posts = JSON.parse(localStorage.getItem("posts"))
      posts.data = posts.data.filter((post) => {
        return post.category_id === this.selected;
      })
      this.$store.commit("storePosts",posts)
    }
  }
}

</script>

<style scoped>
  *{
    box-sizing: border-box;
  }
  .main{
    background-color: lightgray;
    margin: 0 auto;
    width: 1280px;

  }
  .posts{
    width: 1080px;
    margin-left: 20px;
  }
  /* Add a card effect for articles */
  .card {
    background-color: white;
    padding: 20px;
  }
  .comment{
    padding: 10px;
    background: lightslategray;
  }
  .active-cyan-4 input[type=text] {
    border: 1px solid #4dd0e1;
    box-shadow: 0 0 0 1px #4dd0e1;
  }
  .filters{
    margin: 0 auto 10px;
    width: 1080px;
  }

</style>
