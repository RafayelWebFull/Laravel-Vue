<template>
  <div>
    <b-container>
      <b-row>
        <b-col class="col-8 m-auto">
    <b-form @submit.prevent="onSubmit"  enctype="multipart/form-data" >
    <b-form-group id="input-group-1" label="Title:"  label-for="input-1">
      <b-form-input
        id="input-1"
        v-model="post.title"
        required
        type='text'
        placeholder="Enter Post Title"
      ></b-form-input>
    </b-form-group>
    <b-form-group
      id="input-group-2"
      label="Choose Avatar"
      label-for="input-8">
      <b-file v-model="post.file" :placeholder="post.image" accept="image/x-png,image/gif,image/jpeg"></b-file>
    </b-form-group>
    <b-form-group id="input-group-3" label="Description:"  label-for="input-3">
      <b-form-textarea
        id="input-3"
        v-model="post.description"
        required
        type='text'
        placeholder="Enter Post Description"
      ></b-form-textarea>
    </b-form-group>
    <b-form-group id="input-group-4" label="Category:"  label-for="input-4">
      <select v-model="post.category_id" class="form-control form-control" >
        <option value="" selected disabled>Please choose Category</option>
        <option value="1">Csgo</option>
        <option value="2">Medal Of Honor</option>
        <option value="3">Dota 2</option>
        <option value="4">Call of Duty</option>
        <option value="5">Minecraft</option>
      </select>
    </b-form-group>
    <b-form-group id="input-group-5" label="Status"  label-for="input-5">
      <select v-model="post.status" class="form-control form-control" >
        <option value="active" selected >Active</option>
        <option value="drafted">Drafted</option>
      </select>
    </b-form-group>
    <b-button type="submit" variant="primary" class='btn'>Update</b-button>
    <b-button @click="cancel" variant="primary" class='btn'>Cancel</b-button>
    </b-form>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
export default {
    mounted() {
      if(this.user && this.user.role == "blogger"){
        if(this.user.block_time){
          this.$router.push("/blogger").catch(() => {})
        }else{
          if(this.$route.paramsid){
            this.$store.dispatch("editPost",[this.$route.params.id])
          }else{
            this.$router.push("/blogger")
          }
        }

      }else{
        this.$router.push("/blogger").catch(() => {})
      }

    },
    computed:{
      post(){
        return this.$store.getters.post
      },
      user(){
        return this.$store.getters.authUser
      }
    },
    methods:{
      onSubmit(){
        let formData = new FormData();
        formData.append('file', this.post.file)
        formData.append('title', this.post.title)
        formData.append('id', this.post.id)
        formData.append('description', this.post.description)
        formData.append('author', this.post.author)
        formData.append('category_id', this.post.category_id)
        formData.append('status', this.post.status)
        formData.append('imageName', this.post.image)
       this.$store.dispatch("updatePost",formData)
      },
      cancel(){
        this.$router.push("/blogger")
      }
    }
}
</script>

<style scoped>

</style>
