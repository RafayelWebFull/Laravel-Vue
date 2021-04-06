<template>
  <div>
    <div class="header d-flex">
      <div class="bg-dark b-calendar-header p-2" style="width: 1980px">
        <h3 class="ml-5 text-info">Admin Page</h3>
      </div>
      <b-navbar-nav v-if="this.user != null"  class="bg-dark right p-2">
        <h4 class="text-info" > {{ this.user.name}}</h4>
        <b-nav-item-dropdown class="text-dark"  right>
          <!-- Using 'button-content' slot -->
          <b-dropdown-item href=""><router-link :to="{name: 'Profile'}">Profile</router-link></b-dropdown-item>
          <b-dropdown-item-button @click="signOut()">Sign Out</b-dropdown-item-button>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </div>
    <div class="d-flex">
      <div class="navbar-nav col-2">

        <div class="" style="height: 912px;background-color: #4b646f;">
          <ul class="pr-3">
            <router-link :to="{name: 'Admin'}"><b-list-group><div class="text-info p-1"><h4><a>All Posts</a></h4></div></b-list-group></router-link>
            <b-list-group><div class="text-info p-1"><h4><a >New Created</a></h4></div></b-list-group>
            <router-link :to="{name: 'bloggerList'}"><b-list-group><div class="text-info p-1"><h4><a >Blogger List</a></h4></div></b-list-group></router-link>
          </ul>
        </div>
      </div>
      <div class="main">
        <div class="search active-cyan-4 w-100" >
          <input class="form-control" type="text" placeholder="Search" v-model="search_filter" aria-label="Search"/>
        </div>
        <div class="col-10 m-auto">
          <b-row>
            <b-col>
              <b-table :items="this.posts.data" :fields="this.field">
                <template #cell(image)="data">
                  <img width="100px" height="100px" :src="`http://task.loc/storage/postsPhoto/${data.item.image}`" />
                </template>
                <template #cell(status)="data">
                  <select name="" :id="data.item.id" @change="changeStatus">
                    <option selected="selected" value="new">New</option>
                    <option value="allow">Allow</option>
                    <option value="block">Block</option>
                  </select>
                </template>
              </b-table>
              <b-pagination v-if="posts.total > posts.per_page " class="justify-content-center pagination-lg" :total-rows="posts.total" aria-controls="posts" @page-click="getResults"  v-model="currentPage" :per-page="posts.per_page" ></b-pagination>
            </b-col>
          </b-row>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from "axios";

export default {
  data(){
    return{
      search_filter: "",
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
    }
  },
  mounted() {
    if(this.$store.getters.authUser){
      if(this.$store.getters.authUser.role != "admin"){
       this.$router.push("/login")
      }
    }else{
      this.$router.push("/login")
    }
    this.$store.dispatch("admin",1)
  },
  computed:{
    user(){
      return this.$store.getters.authUser
    },
    posts(){
      return this.$store.getters.posts
    }
  },
  methods:{
    getResults(evt){
      this.$store.dispatch("admin",evt.target.ariaPosInSet)
    },
    signOut(){
      axios.post("/auth/logout")
      localStorage.removeItem("user")
      localStorage.removeItem("access_token")
      location.href = "/"
    },
    changeStatus(evt){
      axios.post("/changeCratedStatus",[evt.target.value,evt.target.getAttribute("id")]).then((res) => {
        this.$store.dispatch("admin",1)
        evt.target.value = "new"
      })

    }
  },
  watch:{
    search_filter(){
      if(this.search_filter != "") {
        axios.post("/searchNewPosts", [this.search_filter]).then((res) => {

          this.$store.commit("storePosts", res.data.data)
        })
      }else{
        this.$store.dispatch("admin",1)
      }
    },
  }

}
</script>

<style scoped>

</style>

