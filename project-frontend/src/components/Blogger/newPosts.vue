<template>
  <div>
    <b-container>
      <b-row>
        <b-col class="col-10 ">
          <div v-if="validationErrors">
            <ul class="alert alert-danger">
              <li v-for="(value, key, index) in validationErrors">{{ value }}</li>
            </ul>
          </div>
      <b-form @submit.prevent="onSubmit"  enctype="multipart/form-data" >
        <b-form-group id="input-group-1" label="Title:"  label-for="input-1">
          <b-form-input
            id="input-1"
            v-model="posts.title"
            required
            type='text'
            placeholder="Enter Post Title"
          ></b-form-input>
        </b-form-group>
        <b-form-group
          id="input-group-2"
          label="Choose Avatar"
          label-for="input-8">
          <b-file v-model="posts.file" accept="image/x-png,image/gif,image/jpeg"></b-file>
        </b-form-group>
        <b-form-group id="input-group-3" label="Description:"  label-for="input-3">
          <b-form-textarea
            id="input-3"
            v-model="posts.description"
            required
            type='text'
            placeholder="Enter Post Description"
          ></b-form-textarea>
        </b-form-group>
        <b-form-group id="input-group-4" label="Category:"  label-for="input-4">
          <select v-model="posts.category_id" class="form-control form-control" >
            <option value="" selected disabled>Please choose Category</option>
            <option value="1">Csgo</option>
            <option value="2">Medal Of Honor</option>
            <option value="3">Dota 2</option>
            <option value="4">Call of Duty</option>
            <option value="5">Minecraft</option>
          </select>
        </b-form-group>
        <b-form-group id="input-group-5" label="Status"  label-for="input-5">
          <select v-model="posts.status" class="form-control form-control" >
            <option value="active" selected >Active</option>
            <option value="drafted">Drafted</option>
          </select>
        </b-form-group>
        <b-button type="submit" variant="primary" class='btn'>Create New Post</b-button>
      </b-form>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import axios from "axios"
import router from "../../router"
export default {
    data(){
      return{
        posts: {
          title: '',
          file: null,
          description: "",
          author: '',
          category_id: '',
          status: 'active',
          image: null,
        },
      }
    },
  mounted() {
    if(!this.$store.getters.authUser){
      router.push('/')

    }else{
      if(this.$store.getters.authUser.block_time != null){
        router.push('/blogger')
      }
    }
  },
  methods:{
    onSubmit(env){
      this.posts.author = this.$store.getters.authUser.name
      let formData = new FormData();
      formData.append('file', this.posts.file)
      formData.append('title', this.posts.title)
      formData.append('description', this.posts.description)
      formData.append('author', this.posts.author)
      formData.append('category_id', this.posts.category_id)
      formData.append('status', this.posts.status)
      formData.append('imageName', this.posts.image)
      this.$store.dispatch("newPost",formData).then((res) => {
        if(res) {
          alert(res.data.massage)
        }
      })
    }
  },
  computed:{
    validationErrors(){
      if(this.$store.getters.error){
        let errors = Object.values(this.$store.getters.error);
        errors = errors.flat();
        return errors;
      }else{
        return null;
      }


    }
  }
}
</script>

<style scoped>

</style>
