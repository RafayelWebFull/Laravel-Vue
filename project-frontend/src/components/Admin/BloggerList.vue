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
            <router-link :to="{name: 'NewCreated'}"><b-list-group><div class="text-info p-1"><h4><a >New Created</a></h4></div></b-list-group></router-link>
            <b-list-group><div class="text-info p-1"><h4><a >Blogger List</a></h4></div></b-list-group>
          </ul>
        </div>
      </div>
      <div class="main col-10">
        <div class="search active-cyan-4 w-100" >
          <input class="form-control" type="text" placeholder="Search" v-model="search_filter" aria-label="Search"/>
        </div>
        <div class="m-auto col-8">
          <b-row>
              <b-table :items="this.bloggers.data" v-if="this.bloggers" :fields="this.field">
                <template #cell(image)="data">
                  <img width="100px" height="100px" :src="`http://task.loc/storage/avatar/${data.item.avatar}`" />
                </template>
                <template #cell(block)="data">
                  <div>
                      <select v-if="!data.item.block_time" class="form-control" @change="blockUser(data.item.name,$event)">
                        <option selected  value="">Block Time</option>
                        <option value="1">1 day</option>
                        <option value="7">7 day</option>
                        <option value="30">30 day</option>
                    </select>
                    <tr>{{data.item.block_time}}</tr>
                  </div>
                </template>
              </b-table>
<!--              <b-pagination v-if="bloggers.total > bloggers.per_page " class="justify-content-center pagination-lg" :total-rows="bloggers.total" aria-controls="posts" @page-click="getResults"  v-model="currentPage" :per-page="posts.per_page" ></b-pagination>-->

          </b-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment"
export default {
  data(){
    return{
      search_filter: "",
      currentPage: 1,
      field: [
        {key: "id",label: "id"},
        {key: "name",label: "name"},
        {key: "email",label: "email"},
        "image",
        {key: "nickname",label: "nickname"},
        {key: "role",label: "role"},
        "block"
        // {key: "description",label: "Description"},
        // {key: "author",label: "Author"},
        // {key: "category.category_name",label: "Category Name"},
        // "status",
        // {key: "created_time",label: "Created Time",sortable: true},
      ],
      blockTime: null,
    }
  },
  created() {
    if(!this.user || this.user.role != 'admin'){
      this.$router.push("/login")
    }
   let data = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)
      this.$store.dispatch("bloggerList",1)
  },
  computed:{
    bloggers(){
      return this.$store.getters.listBlogger
    },
    user(){
        return this.$store.getters.authUser
    },
  },
  methods:{
    getResults(evt){
      this.$store.dispatch("index",evt.target.ariaPosInSet)
    },
    signOut(){
      axios.post("/auth/logout")
      localStorage.removeItem("user")
      localStorage.removeItem("access_token")
      location.href = "/"
    },
    blockUser(name,evt){
      this.blockTime = parseInt(evt.target.value)
      // Outputs the date and time in Mon dd, YYYY, H:MM:SS AM/PM
      axios.post("auth/blockUser",[this.blockTime,name]).then((res) => {
          this.$store.dispatch("bloggerList",1)
      })
    }
  },
      // this.blockTime = new Date(Date.now() + `${evt.target.value}` * 24 * 60 * 60 * 1000)
      // let now  = new Date(Date.now())
      // if(this.blockTime > Date.now().toString()){
      // }

  watch:{
    search_filter(){
      if(this.search_filter != "") {
        axios.post("/searchBlogger", [this.search_filter]).then((res) => {
          this.$store.commit("storeBloggerList", res.data.data)
        })
      }else{
        this.$store.dispatch("bloggerList",1)
      }
    },
  }
}
</script>

<style scoped>

</style>
