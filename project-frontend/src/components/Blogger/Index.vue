<template>
  <div>
    <div class="container-sm">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-title">
              <div class="d-flex align-items-center mb-5">
                <h2 >All Contacts</h2>
                <div class="ml-auto">
                  <a href="/newPosts"  class="btn btn-success"><i class="fa fa-plus-circle"></i>Add New</a>
                </div>
                <b-navbar-nav v-if="this.user != null"  class="p-1 mr-5">
                  <h4 class="text-dark" > {{ this.user.name}}</h4>
                  <b-nav-item-dropdown class="text-dark"  right>
                    <b-dropdown-item href="" :disabled="user.block_time != null"><router-link :to="{name: 'Profile'}">Profile</router-link></b-dropdown-item>
                    <b-dropdown-item-button @click="signOut">Sign Out</b-dropdown-item-button>
                  </b-nav-item-dropdown>
                </b-navbar-nav>
              </div>
              <select id="select" :disabled="user.block_time != null"  v-model="selected" class="mb-3 form-control" @change="Status" style="width: 200px; height: 40px;">
                <b-select-option  disabled selected value="">Select Filter</b-select-option>
                <option value="active">Active</option>
                <option value="Created">Created By Time</option>
                <option value="drafted">Drafted</option>
              </select>
                <div class="card">
                  <b-table :items="this.posts.data" :fields="field">
                    <template #cell(image)="data">
                      <img width="100px" height="100px" :src="`http://task.loc/storage/postsPhoto/${data.item.image}`" />
                    </template>
                    <template #cell(status)="data">
                      <b-form >
                      <select  :disabled="user.block_time != null"  name="" @change="onChange" :id="data.item.id">
                        <option  :selected="data.item.status === 'active'"  value="active">Active</option>
                        <option :selected="data.item.status === 'drafted'"  value="drafted">Drafted</option>
                      </select>
                      </b-form>
                    </template>
                    <template #cell(button)="data">
                      <div class="col-6 " >
                        <b-button :disabled="user.block_time != null" class="mb-1" type="button" @click="edit(data.item.id)" >Edit</b-button>
                        <b-button :disabled="user.block_time != null" class="mb-1" variant="danger" @click="deletePost(data.item.id)" type="button">Delete</b-button>
                        <b-button :disabled="user.block_time != null"  class="mb-1" variant="info" @click="show(data.item.id)" type="button">Show</b-button>
                      </div>
                    </template>
                  </b-table>
                  <b-pagination v-if="posts.total > posts.per_page " class="justify-content-center pagination-lg" :total-rows="posts.total" aria-controls="posts" @page-click="getResults"  v-model="currentPage" :per-page="posts.per_page" >
                  </b-pagination>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios"
import router from "../../router"
export default {
  data(){
    return {
      currentPage: 1,
      selected: '',
      selectedStatus: '',
        field: [
          {key: "title",label: "Title"},
          {key: "description",label: "Description"},
          {key: "author",label: "Author"},
          "image",
          {key: "category.category_name",label: "Category Name"},
          "status",
          {key: "created_time",label: "Created Time",sortable: true},
          {key: "button",label: "Action"}
        ],
      status: ''
    }
  },
    computed: {
      posts(){
        return this.$store.getters.bloggerPosts
      },
      user(){
        return this.$store.getters.authUser
      }
    },
  created() {
    if(this.user.block_time != null){
        alert("You blocked by Super Admin Until" + "  " + this.user.block_time)

    }
  },
  mounted() {
    if(!this.$store.getters.authUser){
      router.push("/")
    }else{
      if(this.$store.getters.authUser.role == "blogger"){
        this.$store.dispatch("currectPosts",[this.$store.getters.authUser,this.currentPage])
      }else{
        router.push("/")
      }
    }


  },
  methods:{
    edit(id){
      if(!this.user.block_time){
        router.push({"name": "EditPost",params:{id: id}})
      }

    },
    show(id){
      if(!this.user.block_time) {
        this.$store.dispatch("show", id)
      }
    },
    signOut(){
      axios.post("/auth/logout")
      localStorage.removeItem("user")
      // this.$store.commit("storeUserData",null)
      localStorage.removeItem("access_token")
      location.href = "/"
    },
    deletePost(id){
      if(!this.user.block_time) {
        this.$store.dispatch("delete", [id, this.$store.getters.authUser.name])
      }
    },
    onChange(evt){
      axios.post("/changeStatus",[evt.target.value,evt.target.getAttribute("id")]).then((res) => {
      })
    },
    Status(){
      axios.post("/filterByStatus",[this.selected,this.$store.getters.authUser]).then((res) => {
        this.$store.commit("storeBloggerPosts",res.data.data)
      })
    },
    getResults(evt){
      this.$store.dispatch("currectPosts",[this.$store.getters.authUser,evt.target.ariaPosInSet])
    },
  },

}
</script>

<style scoped>

</style>
